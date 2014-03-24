//APP CACHE DEBUGGING
// log each of the events fired by window.applicationCache
window.applicationCache.onchecking = function(e) {
	console.log("CACHE: Checking for updates");
};
window.applicationCache.onnoupdate = function(e) {
	console.log("CACHE: No updates");
};
window.applicationCache.onupdateready = function(e) {
	console.log("CACHE: Update ready");
};
window.applicationCache.onobsolete = function(e) {
	console.log("CACHE: Obsolete");
};
window.applicationCache.ondownloading = function(e) {
	console.log("CACHE: Downloading");
};
window.applicationCache.oncached = function(e) {
	console.log("CACHE: Cached - available offline");
};
window.applicationCache.onerror = function(e) {
	console.log("CACHE: Error");
};
// END APP CACHE DEBUGGING
 
jQuery(document).ready(function() {
	updateFormCounter();

	//adds a reusable dialog to the pages
jQuery("#entry_form").append('<div id="app-dialog" data-role="dialog"><div id="app-dialog-content"data-role="content"></div></div>');
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

	jQuery("#dialog-savedFormCounter").text(count);
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
function dataURItoBlob(dataURI) {
	var binary = atob(dataURI.split(',')[1]);
	var array = [];
	for (var i = 0; i < binary.length; i++) {
		array.push(binary.charCodeAt(i));
	}
	return new Blob([new Uint8Array(array)], {
		type : 'image/jpeg'
	});
}

/*
 * Sending all saved forms.
 * @returns {undefined}
 */
function sendAllSavedForms() {
	if (navigator.onLine) {
		var count = localStorage.getItem("form_count");
		while (count != null && count-- != 0) {
			setTimeout(function() {
				console.log("Sending form: " + count);
				sendSavedForm();
			}, 700);
		}
	} else {
		alert("offline");
	}
}

/*
 * Sends the form
 */
function sendSavedForm(i) {
	if (i != null)
		;
	//get the right form
	else {
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
						//TODO change pic.jpg to something to correspond to fyle type
						data.append(input_array[k].name, dataURItoBlob(pic_file), "pic.jpg");
					} else {
						console.log("DEBUG: SEND - " + input_array[k].value + " is " + pic_file);
					}
				} else {
					data.append(input_array[k].name, input_array[k].value);
				}
			}

			//AJAX POST
			console.log("DEBUG: SEND - form ajax");
			jQuery.ajax({
				url : 'form',
				type : 'POST',
				data : data,
				cache : false,
				enctype : 'multipart/form-data',
				dataType : 'json',
				processData : false, // Don't process the files
				contentType : false, // Set content type to false as jQuery will tell the server its a query string request
				//                            success:function(data){
				//                                console.log("success");
				//                                console.log(data);
				//                            },
				//                            error: function (xhr, ajaxOptions, thrownError) {
				//                                console.log(xhr.status);
				//                                console.log(thrownError);
				//                            }
			});

			//clean
			console.log("DEBUG: SEND - cleaning up");
			localStorage.removeItem(FORM_KEY + formsCount);
			localStorage.setItem(FORM_COUNT_KEY, --formsCount);
			for (var j = 0; j < files_clean.length; j++)
				localStorage.removeItem(files_clean[j]);
		}
	}
	updateFormCounter();
}

/*
 * Saves the form
 */
function saveForm() {
	console.log("DEBUG: FORM.");
	var input_array = new Array();
	var input_key = {};
	var name, value, type, id;

	//form counter
	var form_count = localStorage.getItem(FORM_COUNT_KEY);
	if (form_count != null) {
		console.log("DEBUG: FORM - incrementing form counter");
		localStorage.setItem(FORM_COUNT_KEY, ++form_count);
	} else {
		console.log("DEBUG: FORM - setting up form counter for the first time")
		form_count = 1;
		localStorage.setItem(FORM_COUNT_KEY, form_count);
	}

	//INPUTS
	var pic_count = 0;

	jQuery('form').find('input').each(function(index, input) {
		name = jQuery(input).attr("name");
		value = jQuery(input).attr('value');
		type = jQuery(input).attr('type');
		id = jQuery(input).attr('id');

		//checkbox
		if (jQuery(input).attr('type') == "checkbox") {
			name = id;
			value = jQuery(input).is(":checked");
			//text
		} else if (type == "text")
			value = jQuery(input).val();
		//file
		else if (type == "file" && id != null) {
			var file = jQuery(input).prop("files")[0];
			if (file != null) {
				console.log("DEBUG: FORM - working with a file");
				var reader = new FileReader();
				var key = Date.now() + "_" + jQuery(input).val().replace("C:\\fakepath\\", "");
				value = key;
				reader.onload = function(e) {
					localStorage.setItem(key, reader.result);
					// console.log("DEBUG: FORM - resizing file");
					// var image = new Image();
					// image.src = reader.result;
					// var width = image.width;
					// var height = image.height;
					//
					// //resizing
					// var res;
					// if (width > height){
					// res = width / MAX_IMG_WIDTH;
					// } else {
					// res = height / MAX_IMG_HEIGHT;
					// }
					// width = width / res;
					// height = height / res;
					//
					// var canvas = document.createElement('canvas');
					// canvas.width = width;
					// canvas.height = height;
					//
					// var imgContext = canvas.getContext('2d');
					// imgContext.drawImage(image, 0, 0, width, height);
					//
					// var shrinked = canvas.toDataURL("image/jpeg");
					//
					// try {
					// console.log("DEBUG: FORM - saving file in storage");
					// localStorage.setItem(key,  shrinked); //stores the image to localStorage
					// }
					// catch (e) {
					// console.log("DEBUG: FORM - saving file in storage failed: " + e);
					// }
				}
				reader.readAsDataURL(file);
			}
		}

		input_array.push({
			"name" : name,
			"value" : value,
			"type" : type
		});
	});

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

	input_array_string = JSON.stringify(input_array);
	console.log("DEBUG: FORM - saving the form into storage");
	localStorage.setItem(FORM_KEY + form_count, input_array_string);

	updateFormCounter();
}

/*
 * Updates the dialog box appended to the page
 */
function makeDialog(text) {
	jQuery('#app-dialog-content').empty().append(text);
}

/*
 * Starts the submition process.
 */
function submitStart() {
	//TODO: validate the form
	if (navigator.onLine) {
		//ONLINE
		console.log("DEBUG: SUBMIT - online");
		saveForm();
		setTimeout(function() {
			sendSavedForm();
		}, 500); //needs a delay as the storage is not so fast
		makeDialog("<center><h2>Submitted successfully. </br>Thank You!</h2></center>");
		jQuery.mobile.changePage('#app-dialog');
		goHome(2000);
	} else {
		//OFFLINE
		console.log("DEBUG: SUBMIT - offline");
		saveForm();
		makeDialog("<center><h2>No Internet. Form saved.</h2></center>");
		jQuery.mobile.changePage('#app-dialog');
		goHome(2000);
	}
}

/*
 * Goes to the app home page
 */
function goHome(delay) {
	setTimeout(function() {
		window.location = '/drupal/app';
	}, delay);
}