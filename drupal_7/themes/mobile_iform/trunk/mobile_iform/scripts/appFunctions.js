
 
jQuery(document).ready(function() {
	updateFormCounter();
	jQuery("#entry-form-submit").click(submitStart);
});
	
//GLOBALS
var FORM_COUNT_KEY = "form_count";
var FORM_KEY = "form_";
var PIC_KEY = "_pic_";
var MAX_IMG_HEIGHT = MAX_IMG_WIDTH = 800;

/*
 * Updates the saved form counters in the menu and other places.
 */
function updateFormCounter() {
	console.log("DEBUG: Updating form counter");
	var count = localStorage.getItem("form_count");

	jQuery("#savedFormCounter").fadeOut(200);
	jQuery("#savedFormCounter").text(count);
	jQuery("#savedFormCounter").fadeIn(200);
    
	jQuery("#savedFormCounterWording").text(((count == 1) ? " form" : " forms"));
	jQuery(".savedFormCounter").text(count);

	if (count != null && count != 0) {
		jQuery(".a-savedFormCounter").css("display", "");
	} else {
		jQuery(".a-savedFormCounter").css("display", "none");
	}
}

/*
 *  Converts DataURI object to a Blob
 * @param {type} form_count
 * @param {type} pic_count
 * @param {type} file
 * @returns {undefined}
 */
function dataURItoBlob(dataURI, file_type) {
	var binary = atob(dataURI.split(',')[1]);
	var array = [];
	for (var i = 0; i < binary.length; i++) {
		array.push(binary.charCodeAt(i));
	}
	return new Blob([new Uint8Array(array)], {
		type : file_type
	});
}

/*
 * Sending all saved forms.
 * @returns {undefined}
*/
function sendAllSavedForms() {
	if (navigator.onLine) {
		var count = localStorage.getItem(FORM_COUNT_KEY);
		if (count > 0) {
			jQuery.mobile.loading('show');
			console.log("Sending form: " + count);
			sendSavedForm();
		} else {
			jQuery.mobile.loading('hide');	
		}
	} else {
		makePopup("<a href='#' data-rel='back' data-role='button' data-theme='b' data-icon='delete' data-iconpos='notext' class='ui-btn-right ui-link ui-btn ui-btn-b ui-icon-delete ui-btn-icon-notext ui-shadow ui-corner-all' role='button'>Close</a>" +
					" <div style='padding:10px 20px;'>" +
					" <center><h2>Looks like you are offline!</h2></center>" +
					 " </div>");
		jQuery('#app-popup').popup().popup('open');
	}
}

/*
 * Sends the form recursively
 */
function sendSavedForm() {
	console.log("DEBUG: SEND");
	var formsCount = localStorage.getItem(FORM_COUNT_KEY);
	//send the last form
	if (formsCount != null && formsCount > 0) {
		//Send form
		console.log("DEBUG: SEND - creating the form.");
		var data = new FormData();
		var files_clean = [];
		//files to clean afterwards
		var input_array = JSON.parse(localStorage.getItem(FORM_KEY + formsCount));

		for (var k = 0; k < input_array.length; k++) {
			if (input_array[k].type == "file") {
				var pic_file = localStorage.getItem(input_array[k].value);
				if (pic_file != null) {
					console.log("DEBUG: SEND - attaching '" + input_array[k].value + "' to " + input_array[k].name);
					files_clean.push(input_array[k].value);
					var type = pic_file.split(";")[0].split(":")[1];
					var extension = type.split("/")[1];
					data.append(input_array[k].name, dataURItoBlob(pic_file, type), "pic." + extension);
				} else {
					console.log("DEBUG: SEND - " + input_array[k].value + " is " + pic_file);
				}
			} else {
				data.append(input_array[k].name, input_array[k].value);
			}
		}
    data.append('appsecret', Drupal.settings.appSecret);

		//AJAX POST
		console.log("DEBUG: SEND - form ajax");
		jQuery.ajax({
			url : Drupal.settings.basePath + 'mobile/submit',
			type : 'POST',
			data : data,
			cache : false,
			enctype : 'multipart/form-data',
			processData : false, // Don't process the files
			contentType : false, // Set content type to false as jQuery will tell the server its a query string request
            success:function(data){
               console.log("DEBUG: SEND - form ajax (success):");
               console.log(data);
               
               //clean
              console.log("DEBUG: SEND - cleaning up");
              localStorage.removeItem(FORM_KEY + formsCount);
              localStorage.setItem(FORM_COUNT_KEY, --formsCount);
              for (var j = 0; j < files_clean.length; j++)
                localStorage.removeItem(files_clean[j]);
            
              updateFormCounter();
              sendAllSavedForms();  
            },
            error: function (xhr, ajaxOptions, thrownError) {
                      jQuery.mobile.loading('hide');
                      makePopup("<a href='#' data-rel='back' data-role='button' data-theme='b' data-icon='delete' data-iconpos='notext' class='ui-btn-right ui-link ui-btn ui-btn-b ui-icon-delete ui-btn-icon-notext ui-shadow ui-corner-all' role='button'>Close</a>" +
                          " <div style='padding:10px 20px;'>" +
                          " <center><h2>Sorry!</h2></center><p> " + xhr.responseText + "</p>" +
                          " </div>");
                      jQuery('#app-popup').popup().popup('open');
                       console.log("DEBUG: SEND - form ajax (ERROR " + xhr.status + " " + thrownError + " " + xhr.responseText +")");
                    }
		});
	}
}

/*
 * Checks if it is possible to store some sized data in localStorage.
 */
function localStorageHasSpace(size){
	var taken = JSON.stringify(localStorage).length;
	var left = 1024 * 1024 * 5 - taken; 
	if ((left - size) > 0)
		return 1;
	else
		return 0;
}

/*
 * Saves the form
 * Returns 1 if save is successful, else 0 if error. 
 */
function saveForm() {
	console.log("DEBUG: FORM.");
	var input_array = new Array();
	var input_key = {};
	var name, value, type, id;

	//INPUTS
	var pic_count = 0;
	var file_storage_status = 1; //if localStorage has little space it becomes 0
	jQuery('form').find('input').each(function(index, input) {
		name = jQuery(input).attr("name");
		value = jQuery(input).attr('value');
		type = jQuery(input).attr('type');
		id = jQuery(input).attr('id');

		//checkbox
		if (jQuery(input).attr('type') == "checkbox") {
			if(!jQuery(input).is(":checked"))
					value = ""; 
			//text
		} else if (type == "text")
			value = jQuery(input).val();
		//file
		else if (type == "file" && id != null) {
			var file = jQuery(input).prop("files")[0];
			if (file != null) {
				console.log("DEBUG: FORM - working with " + file.name);
				if (!localStorageHasSpace(file.size)){
				 	return file_storage_status = 0;
				}
				
				var reader = new FileReader();
				var key = Date.now() + "_" + jQuery(input).val().replace("C:\\fakepath\\", "");
				value = key;
				reader.onload = function(e) {
					console.log("DEBUG: FORM - resizing file");
					var image = new Image();
					image.onload = function() {
						var width = image.width;
						var height = image.height;
						
						//resizing
						var res;
						if (width > height){
						  res = width / MAX_IMG_WIDTH;
						} else {
						  res = height / MAX_IMG_HEIGHT;
						}
		
						width = width / res;
						height = height / res;
						
						var canvas = document.createElement('canvas');
						canvas.width = width;
						canvas.height = height;
						
						var imgContext = canvas.getContext('2d');
						imgContext.drawImage(image, 0, 0, width, height);
						
						var shrinked = canvas.toDataURL(file.type);
						try {
							console.log("DEBUG: FORM - saving file in storage ("  
							+ (shrinked.length / 1024) + "KB)" );
							
							localStorage.setItem(key,  shrinked); //stores the image to localStorage
						}
						catch (e) {
							console.log("DEBUG: FORM - saving file in storage failed: " + e);
						}
					};
					image.src = reader.result;
				};
				reader.readAsDataURL(file);
			}
		}

		input_array.push({
			"name" : name,
			"value" : value,
			"type" : type
		});
	});
	
	//return if unsaccessfull file saving
	if (file_storage_status == 0)
		return 0;

	//SELECTS
	jQuery('form').find("select").each(function(index, select) {
		name = jQuery(select).attr('name');
		value = jQuery(select).find(":selected").val();
		type = "select";

		input_array.push({
			"name" : name,
			"value" : value,
			"type" : type
		});
	});
	
	//form counter
	var form_count = localStorage.getItem(FORM_COUNT_KEY);
	if (form_count != null) {
		console.log("DEBUG: FORM - incrementing form counter");
		localStorage.setItem(FORM_COUNT_KEY, ++form_count);
	} else {
		console.log("DEBUG: FORM - setting up form counter for the first time");
		form_count = 1;
		localStorage.setItem(FORM_COUNT_KEY, form_count);
	}

	input_array_string = JSON.stringify(input_array);
	console.log("DEBUG: FORM - saving the form into storage");
	try{
		localStorage.setItem(FORM_KEY + form_count, input_array_string);
	} catch (e){
		console.log("DEBUG: FORM - ERROR while saving the form");
		console.log(e);
		return 0;	
	}
	updateFormCounter();
	return 1;
}

/*
 * Updates the dialog box appended to the page
 */
function makeDialog(text) {
	jQuery('#app-dialog-content').empty().append(text);
}

/*
 * Updares the popup div appended to the page
 */
function makePopup(text){
	jQuery('#app-popup').empty().append(text);
}


/*
 * Submits the form.
 */
function submitForm(form_id, onSend, onSuccess){
	var form = document.getElementById(form_id);
	var data = new FormData(form);
  data.append('appsecret', Drupal.settings.appSecret);
	jQuery.ajax({
			url: Drupal.settings.basePath + 'mobile/submit',
			type: 'POST',
			data: data,
			cache: false,
			enctype: 'multipart/form-data',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
      success: function(data){
        console.log("DEBUG: SEND - form ajax (success):");
        console.log(data);
        onSuccess();
      },
       error: function (xhr, ajaxOptions, thrownError) {
                jQuery.mobile.loading('hide');
                makePopup("<a href='#' data-rel='back' data-role='button' data-theme='b' data-icon='delete' data-iconpos='notext' class='ui-btn-right ui-link ui-btn ui-btn-b ui-icon-delete ui-btn-icon-notext ui-shadow ui-corner-all' role='button'>Close</a>" +
                    " <div style='padding:10px 20px;'>" +
                    " <center><h2>Sorry!</h2></center><p> " + xhr.responseText + "</p>" +
                    " </div>");
                jQuery('#app-popup').popup().popup('open');
                console.log("DEBUG: SEND - form ajax (ERROR " + xhr.status + " " + thrownError + " " + xhr.responseText + ")");
              },
      beforeSend: onSend
		});
}

function startGeolocation(timeout){
	var start_time = new Date().getTime();
	
	console.log("DEBUG: GPS - start");
  var tries = indiciaData.gps_try;
  if(tries == 0 || tries == null)
  	indiciaData.gps_try = 1;
  else
  	indiciaData.gps_try = tries +  1;
	  	
  //stop any other geolocation service started before
  navigator.geolocation.clearWatch(indiciaData.gps_running_id);
	
  if(!navigator.geolocation) {
  	console.log("DEBUG: GPS - error, no gps support!");
      // Early return if geolocation not supported.
      makePopup('<div style="padding:10px 20px;"><center><h2>Geolocation is not supported by your browser.</h2></center></div>');   
      jQuery('#app-popup').popup().popup('open');
      return;
    }

    // Callback if geolocation succeeds.
    var counter = 0;
    function success(position) {
    	//timeout
    	var current_time = new Date().getTime();
    	if ((current_time - start_time) > timeout){
    		//stop everything
    		console.log("DEBUG: GPS - timeout");
    		jQuery.mobile.loading('hide');
    		jQuery('#app-popup').popup('close');
        navigator.geolocation.clearWatch(indiciaData.gps_running_id);
        submitStart();
    	}
    	console.log("TIME: left - " + (current_time - start_time));
    	
      var latitude  = position.coords.latitude;
      var longitude = position.coords.longitude;
      var accuracy = position.coords.accuracy;
      
      //set for the first time
      var prev_accuracy = jQuery('#sref_accuracy').val();
      if (prev_accuracy == -1)
      	prev_accuracy = accuracy + 1;
      	
      //only set it up if the accuracy is increased
      if (accuracy > -1 && accuracy < prev_accuracy){    
        jQuery('#imp-sref').attr('value', latitude + ', ' + longitude);
        jQuery('#sref_accuracy').attr('value', accuracy);
        console.log("DEBUG: GPS - setting accuracy of " + accuracy + " meters" );
        if (accuracy < indiciaData.GPS_ACCURACY_LIMIT){
        	console.log("DEBUG: GPS - Success! Accuracy of " + accuracy + " meters");
        	jQuery.mobile.loading('hide');
        	jQuery('#app-popup').popup('close');
            navigator.geolocation.clearWatch(indiciaData.gps_running_id);
            submitStart();
        }
    }
    };
    
    // Callback if geolocation fails.
    function error(error) {
    	console.log("DEBUG: GPS - error");
    	jQuery.mobile.loading('hide');
    	jQuery('#app-popup').popup('close');
    	submitStart();
    };
    
    // Geolocation options.
    var options = {
      enableHighAccuracy: true,
      maximumAge: 0,
      timeout: 120000
    };
    
    // Request geolocation.
    indiciaData.gps_running_id = navigator.geolocation.watchPosition(success, error, options);
    jQuery.mobile.loading('show');
}

/*
 * Validates the current GPS lock quality
 */
function validateGeolocation(){
	var accuracy = jQuery('#sref_accuracy').val();
	jQuery.mobile.loading('hide');
	//No GPS lock yet
	if ( accuracy == -1 ){
		console.log("DEBUG: GPS Validation - accuracy -1");
		var tries = indiciaData.gps_try;
		if (tries == 0 || tries == null){
			makePopup("<a href='#' data-rel='back' data-role='button' data-theme='b' data-icon='delete' data-iconpos='notext' class='ui-btn-right ui-link ui-btn ui-btn-b ui-icon-delete ui-btn-icon-notext ui-shadow ui-corner-all' role='button'>Close</a>" +
					 " <div style='padding:10px 20px;'>" +
					 " <center><h3>GPS</h3></center" +
					 " <p>Sorry, we couldn't get your location. Please make sure the GPS is on and try again.</p>"+
					 " <button onclick='startGeolocation(60000)' data-theme='a' class=' ui-btn ui-btn-a ui-shadow ui-corner-all'>Try again</button>"+
					 " </div>");
		} else if (tries == 5){
			makePopup("<a href='#' data-rel='back' data-role='button' data-theme='b' data-icon='delete' data-iconpos='notext' class='ui-btn-right ui-link ui-btn ui-btn-b ui-icon-delete ui-btn-icon-notext ui-shadow ui-corner-all' role='button'>Close</a>" +
					 " <div style='padding:10px 20px;'>" +
					 " <center><h3>GPS</h3></center" +
					 " <p>Hmm.. don't worry, some day you might just get lucky. </p>"+
					 " <button onclick='startGeolocation(60000)' data-theme='a' class=' ui-btn ui-btn-a ui-shadow ui-corner-all'>Try again</button>"+
					 " </div>");
		}else {
			makePopup("<a href='#' data-rel='back' data-role='button' data-theme='b' data-icon='delete' data-iconpos='notext' class='ui-btn-right ui-link ui-btn ui-btn-b ui-icon-delete ui-btn-icon-notext ui-shadow ui-corner-all' role='button'>Close</a>" +
					  " <div style='padding:10px 20px;'>" +
					  " <center><h3>GPS</h3></center" +
					 " <p>Still can't get your location. Make sure you are outside and move away from tall buildings, trees and try again.</p>"+
					 " <button onclick='startGeolocation(60000)' data-theme='a' class=' ui-btn ui-btn-a ui-shadow ui-corner-all'>Try again</button>"+
					 " </div>");
		}
		jQuery('#app-popup').popup({
			afterclose: function( event, ui ) {
				console.log("DEBUG: POPUP - closed");
				jQuery.mobile.loading('hide');
				navigator.geolocation.clearWatch(indiciaData.gps_running_id);
			}
		});
		jQuery('#app-popup').popup('open');
		
		return false;
		
  //Geolocation bad accuracy
	} else if (accuracy > indiciaData.GPS_ACCURACY_LIMIT){
		console.log("DEBUG: GPS Validation - accuracy " );
		makePopup("<a href='#' data-rel='back' data-role='button' data-theme='b' data-icon='delete' data-iconpos='notext' class='ui-btn-right ui-link ui-btn ui-btn-b ui-icon-delete ui-btn-icon-notext ui-shadow ui-corner-all' role='button'>Close</a>" +
				 " <div style='padding:10px 20px;'>" +
				 " <center><h3>GPS</h3></center" + 
				 " <p>Sorry, we haven't got your GPS location accurately yet.</p>"+
				 " <p>Accuracy: " + accuracy + " meters (we need < " +  indiciaData.GPS_ACCURACY_LIMIT + ")</p>" +
				 " <button onclick='startGeolocation(60000)' data-theme='a' class=' ui-btn ui-btn-a ui-shadow ui-corner-all'>Try again</button>"+
				 " </div>");
		jQuery('#app-popup').popup({
			afterclose: function( event, ui ) {
				console.log("DEBUG: POPUP - closed");
				jQuery.mobile.loading('hide');
				navigator.geolocation.clearWatch(indiciaData.gps_running_id);
			}
		});
		jQuery('#app-popup').popup('open');
		
		return false;
		
  //Geolocation accuracy is good enough
	} else {
		console.log("DEBUG: GPS Validation - accuracy Good Enough ( " + accuracy + ") loc: " +  jQuery('#imp-sref').val());
		return true;	
	} 
}

/*
 * Form validation
 */
function validateForm($){
    var MULTIPLE_GROUP_KEY = "multiple_"; //to separate a grouped input
    var invalids = [];
   
    var tabinputs = $('#entry_form').find('input,select,textarea').not(':disabled,[name=],.scTaxonCell,.inactive');
    var tabtaxoninputs = $('#entry_form .scTaxonCell').find('input,select').not(':disabled');
    if (tabinputs.length>0){
      tabinputs.each(function(index){
        if (!$(this).valid()){
            var found = false;
            
            //this is necessary to check if there was an input with 
            //the same name in the invalids array, if found it means 
            //this new invalid input belongs to the same group and should 
            //be ignored.
            for (var i = 0; i < invalids.length; i++){
                if (invalids[i].name == (MULTIPLE_GROUP_KEY + this.name)){
                   found = true;
                   break;
                } if (invalids[i].name == this.name) {
                  var new_id = (this.id).substr(0, this.id.lastIndexOf(':'));
                  invalids[i].name = MULTIPLE_GROUP_KEY + this.name;
                  invalids[i].id = new_id;
                  found = true;
                  break;
                }              
            }
            //save the input as a invalid
            if (!found)
              invalids.push({ "name" :this.name, "id" : this.id });
        }
      });      
    }
      
    if (tabtaxoninputs.length>0) {
     tabtaxoninputs.each(function(index){
        invalids.push({ "name" :this.name, "id" : this.id });
     }); 
    }
    
    //constructing a response about invalid fields to the user
    if (invalids.length > 0){
      var popupString = "<a href='#' data-rel='back' data-role='button' data-theme='b' data-icon='delete' data-iconpos='notext' class='ui-btn-right ui-link ui-btn ui-btn-b ui-icon-delete ui-btn-icon-notext ui-shadow ui-corner-all' role='button'>Close</a>" +
        " <div style='padding:10px 20px;'>" +
        " <center><h3>Validation</h3></center" +
        " <p>The following is still missing:</p><ul>";
        
        for (var i=0; i < invalids.length; i++)
          if (invalids[i].name.indexOf(MULTIPLE_GROUP_KEY) != 0)
            popupString += "<li>" + $("label[for='" + invalids[i].id + "']").text() + "</li>";
          else
            popupString += "<li>" + $("label[data-for='" + invalids[i].id + "']").text() + "</li>";
        
        popupString += "</ul></div>";
        makePopup(popupString);
        jQuery.mobile.loading('hide');
        jQuery('#app-popup').popup().popup('open');
      
      return false;
    }
    return true;
}


/*
 * Starts the submition process.
 */
function submitStart() {
	 console.log("DEBUG: SUBMIT - start");
	
	jQuery.mobile.loading('show');
	setTimeout(function(){
	 if(validateForm(indiciaData.jQuery) && validateGeolocation()){
	   
   	//Online
  	if (navigator.onLine) {
  		console.log("DEBUG: SUBMIT - online");
  		submitForm('entry_form', 
  			function(){
  				//start load
  				jQuery.mobile.loading('show');
  			},
  			function(){
  				//end load 
  				jQuery.mobile.loading('hide');
  				makePopup("<div style='padding:10px 20px;'>" +
  					"<center><h2>Submitted successfully. </br>Thank You!</h2></center>" +
  					 " </div>");
  				jQuery('#app-popup').popup().popup('open');
  				goHome(4000);
  			});
  			
  	//Offline		
  	} else {
  		console.log("DEBUG: SUBMIT - offline");
  		jQuery.mobile.loading('show');
  		if (saveForm() == 1){
  			jQuery.mobile.loading('hide');
  			makePopup("<div style='padding:10px 20px;'>" +
  					"<center><h2>No Internet. Form saved.</h2></center>" +
  					 " </div>");
  			jQuery('#app-popup').popup().popup('open');
  			goHome(4000);
  		} else {
  			jQuery.mobile.loading('hide');
  			makePopup("<div style='padding:10px 20px;'>" +
  					" <center><h2>Error.</h2></center>" +
  					" <p>Full local storage. Please send the old forms first.</p>"+
  					" </div>");
  			jQuery('#app-popup').popup().popup('open');
  			setTimeout(function() {
  				jQuery.mobile.changePage(Drupal.settings.mobileIformStartPath + 'form');
  			}, 4000);
  		}
  	}
	}
	  }, 20);
}

/*
 * Goes to the app home page
 */
function goHome(delay) {
	setTimeout(function() {
		window.location = Drupal.settings.mobileIformStartPath + "home";
	}, delay);
}