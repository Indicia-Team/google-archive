/* ************ BACK BUTTON FIX *****************************************************
 * Since the back button does not work in current iOS 7.1.1 while in app mode, it is 
 * necessary to manually assign the back button urls.
 * 
 * Set up the replacements so that the nearest div's, with data-role of a page,  
 * id is matched with the new URL of the back button eg.
  
   BACK_BUTTON_URLS = {
  'app-.*':'home',
  'app-examples':'home',
  'tab-location':'home' 
};

*/

var BACK_BUTTON_URLS = {
  'app-home' : '/record/app',
  'tab-location'  :  '/record/app/home',
  'tab-species'  :  '#tab-location',
  'tab-photograph'  :  '#tab-species',
  'tab-injury'  :  '#tab-photograph',
  'tab-weather'  :  '#tab-injury',
  'tab-pollution'  :  '#tab-weather',
  'app-examples-.*' : '/record/app/examples',
  'app-symptoms-.*' : '/record/app/symptoms',
  'app-other-causes-.*' : '/record/app/other-causes',
  'app-.*' :  '/record/app/home'
};

/*
 * Fixes back buttons for specific page
 */
function fixPageBackButtons(page_id){
  console.log('FIXING: back buttons (' + page_id + ')');

  var url = "";
  //check if in array
  for (var regex in BACK_BUTTON_URLS){
    var re = new RegExp(regex, "i");
    if(re.test(page_id)){
      url = BACK_BUTTON_URLS[regex];
      break;
    } 
  }
  
  //return if no match
  if (url == ""){
   return; 
  }
  
  var buttons = jQuery("div[id='" + page_id + "'] a[data-rel='back']");
  buttons.each( function(index, button){
    //assign new url to the button
    jQuery(button).attr('href', url);   
    //remove data-rel="back" attribute
    jQuery(button).removeAttr('data-rel');
    //use reverse transition for back button
    jQuery(button).attr('data-direction', 'reverse');
  });
}
/************ END BACK BUTTON FIX ****************************************************/

/************** STANDALONE LINK FIX **************************************************/
function fixForwardButtons() {
  var links = jQuery("a[data-ajax='false']");
  links.each(function(index, link) {
    var $link = jQuery(link);
    console.log('FIXING: link ( ' + $link.attr('href') + ')');
    $link.on('click', function(event){
      event.preventDefault();
      location.href = jQuery(event.target).attr('href');
    });
  });
  
}
/************ END STANDALONE LINK FIX ************************************************/



/* Fix buttons on pagecreate event */

jQuery(document).on('pagecreate', function(event, ui) {
     if (browserDetect('Safari')){
       fixPageBackButtons(event.target.id);
       if(("standalone" in window.navigator) && window.navigator.standalone) {
         // We are in iOS standalone mode.
         // Links that have the data-prefetch attribute will load standalone but
         // links that have data-ajax = "false" will open in the browser
         // Lets fix this too.
         fixForwardButtons();
     }
         
     }
});

/*
 * Generic function to detect the browser
 * 
 * Chrome has to have and ID of both Chrome and AppleWebKit while
 * Safari has to have an ID of only AppleWebKit and not Chrome
 * 
 * Examples of User Agent
 * 
 * In iOS/Safari:
 * Mozilla/5.0 (iPad; CPU OS 7_1_1 like Mac OS X) AppleWebKit/537.51.2 
 * (KHTML, like Gecko) Version/7.0 Mobile/11D201 Safari/9537.53
 * 
 * In iOS/standalone
 * Mozilla/5.0 (iPad; CPU OS 7_1_1 like Mac OS X) AppleWebKit/537.51.2 
 * (KHTML, like Gecko) Version/7.0 Mobile/11D201
 * 
 * In Windows/Chrome
 * Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) 
 * Chrome/35.0.1916.114 Safari/537.36
 * 
 * In Android/Chrome
 * Mozilla/5.0 (Linux; Android 4.4.2; Nexus 5 Build/KOT49H) AppleWebKit/537.36 
 * (KHTML, like Gecko) Chrome/30.0.1599.105 Mobile safari/537.36
 * 
 */
function browserDetect(browser){
    if (browser == 'Chrome' || browser == 'Safari'){
        var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
        var is_safari = navigator.userAgent.indexOf("AppleWebKit") > -1;

        if (is_safari){
          if (browser == 'Chrome'){
            //Chrome
            return (is_chrome) ? true : false;
          } else {
            //Safari
            return (!is_chrome) ? true : false;
          }
        } 
        return false;
    }

    if (navigator.userAgent.indexOf(browser) > -1){
      return true;
    }
    return false;
}