
/*
 * Gets a query parameter from the URL.
 */
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function _log(message){
    if(app.CONF.DEBUG){
       console.debug(message);
    }
}

function loadScript(src) {
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = src;
    document.body.appendChild(script);
}

function startManifestDownload(id, files_no, src, callback, onError){
    /*todo: Add better offline handling:
      If there is a network connection, but it cannot reach any
      Internet, it will carry on loading the page, where it should stop it
      at that point.
      */
    if (navigator.onLine) {
        src = Drupal.settings.basePath + src + '?base_path=' + Drupal.settings.basePath + '&files=' + files_no;
        var frame = document.getElementById(id);
        if (frame) {
            //update
            frame.contentWindow.applicationCache.update();
        } else {
            //init
            app.navigation.popup('<iframe id="' + id + '" src="' + src + '" width="215px" height="215px" scrolling="no" frameBorder="0"></iframe>', true);
            frame = document.getElementById(id);

            //After frame loading set up its controllers/callbacks
            frame.onload = function() {
                _log('Manifest frame loaded');
                if (callback != null) {
                    frame.contentWindow.finished = callback;
                }

                if (onError != null) {
                    frame.contentWindow.error = onError;
                }
            }
        }
    } else {
        $.mobile.loading( 'show', {
            text: "Looks like you are offline!",
            theme: "b",
            textVisible: true,
            textonly: true
        });
    }
}

/**
 * Initialises and returns a variable.
 * @param name
 * @returns {*}
 */
function varInit(name){
    var name_array = name.split('.');
    window[name_array[0]] = window[name_array[0]] || {};
    var variable = window[name_array[0]];

    //iterate through the namespaces
    for(var i = 1; i < name_array.length; i++){
        if(variable[name_array[i]] !== 'object'){
            //overwrite if it is not an object
            variable[name_array[i]] = {};
        }
        variable = variable[name_array[i]];
    }
    return variable;
}

function objClone(obj) {
    if (null == obj || "object" != typeof obj) return obj;
    var copy = obj.constructor();
    for (var attr in obj) {
        if (obj.hasOwnProperty(attr)) copy[attr] = objClone(obj[attr]);
    }
    return copy;
}

/**
 * FROM: http://kylestechnobabble.blogspot.co.uk/2013/08/easy-way-to-enable-disable-hide-jquery.html
 * USAGE:
 * $('MyTabSelector').disableTab(0);        // Disables the first tab
 * $('MyTabSelector').disableTab(1, true);  // Disables & hides the second tab
 */
(function ($) {
    $.fn.disableTab = function (tabIndex, hide) {

        // Get the array of disabled tabs, if any
        var disabledTabs = this.tabs("option", "disabled");

        if ($.isArray(disabledTabs)) {
            var pos = $.inArray(tabIndex, disabledTabs);

            if (pos < 0) {
                disabledTabs.push(tabIndex);
            }
        }
        else {
            disabledTabs = [tabIndex];
        }

        this.tabs("option", "disabled", disabledTabs);

        if (hide === true) {
            $(this).find('li:eq(' + tabIndex + ')').addClass('ui-state-hidden');
        }

        // Enable chaining
        return this;
    };

    $.fn.enableTab = function (tabIndex) {

        // Remove the ui-state-hidden class if it exists
        $(this).find('li:eq(' + tabIndex + ')').removeClass('ui-state-hidden');

        // Use the built-in enable function
        this.tabs("enable", tabIndex);

        // Enable chaining
        return this;

    };

})(jQuery);