app = app || {};
app.form = (function(m, $){
    m.MULTIPLE_GROUP_KEY = "multiple_"; //to separate a grouped input
    m.COUNT = "form_count";
    m.STORAGE = "form_";
    m.PIC = "_pic_";

    m.totalFiles = 0;

    m.DATA = "data";
    m.FILES = "files";
    m.SETTINGS = "formSettings";
    m.LASTID = "lastId";

    m.FORM = "form"; //current form key for the tmp storage

    /**
     *
     * @returns {*}
     */
    m.init = function(){
        var settings = m.getSettings();
        if (settings == null){
            settings = {};
            settings[m.LASTID] = 0;
            m.setSettings(settings);
            return settings;
        }
        return null;
    };

    m.setSettings = function(settings){
        app.storage.set(m.SETTINGS, settings);
    };

    m.initSettings = function(){
        var settings = {};
        settings[m.LASTID] = 0;
        m.setSettings(settings);
        return settings;
    };

    m.getSettings = function(){
        var settings = app.storage.get(m.SETTINGS) || m.initSettings();
        return settings;
    };

    /*
     * Starts the form submission process.
     */
    m.submit = function() {
        _log("DEBUG: SUBMIT - start");
        var processed = false;
        $(document).trigger('app.submitRecord.start');
        setTimeout(function(){
            //validate form
            var invalids = app.form.validate(indiciaData.jQuery);
            if(invalids.length == 0){
                //validate GPS lock
                var gps = app.geoloc.validate();
                switch(gps){
                    case app.TRUE:
                        _log("DEBUG: GPS Validation - accuracy Good Enough");
                        processed = true;
                        m.process();
                        break;
                    case app.FALSE:
                        _log("DEBUG: GPS Validation - accuracy " );
                        $(document).trigger('app.geoloc.lock.bad');
                        break;
                    case app.ERROR:
                        _log("DEBUG: GPS Validation - accuracy -1");
                        $(document).trigger('app.geoloc.lock.no');
                        break;
                    default:
                        _log('DEBUG: GPS validation unknown');
                }
            } else {
                jQuery(document).trigger('app.form.invalid', [invalids]);
            }
            $(document).trigger('app.submitRecord.end', [processed]);
        }, 20);
    };

    /**
     * Processes the form either by saving it and sending (online) or simply saving (offline).
     */
    m.process = function(){
        if (navigator.onLine) {
            m.processOnline();
        } else {
            m.processOffline()
        }
    };

    /**
     * Saves and submits the form.
     */
    m.processOnline = function(){
        _log("DEBUG: SUBMIT - online");
        var onSaveSuccess = function(savedFormId){
            //#2 Post the form
            app.io.sendSavedForm(savedFormId);
        };
        //#1 Save the form first
        app.form.storage.saveUsingFormId('#entry_form', onSaveSuccess);
    };

    /**
     * Saves the form.
     */
    m.processOffline = function(){
        _log("DEBUG: SUBMIT - offline");
        $.mobile.loading('show');
        if (app.form.storage.saveUsingFormId('#entry_form') > 0){
            $(document).trigger('app.submitRecord.save');
        } else {
            $(document).trigger('app.submitRecord.error');
        }
    };

    /*
     * Form validation.
     * Note: uses old jQuery
     */
    m.validate = function($){
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
                        if (invalids[i].name == (app.form.MULTIPLE_GROUP_KEY + this.name)){
                            found = true;
                            break;
                        } if (invalids[i].name == this.name) {
                            var new_id = (this.id).substr(0, this.id.lastIndexOf(':'));
                            invalids[i].name = app.form.MULTIPLE_GROUP_KEY + this.name;
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
            return invalids;
        }
        return [];
    };

    /**
     * Extracts data (apart from files) from provided form into a form_array that it returns.
     * @param form
     * @returns {Array}
     */
    m.extract = function(form) {
        //extract form data
        var form_array = [];
        var name, value, type, id, needed;

        form.find('input').each(function(index, input) {
            name = $(input).attr("name");
            value = $(input).attr('value');
            type = $(input).attr('type');
            id = $(input).attr('id');
            needed = true; //if the input is empty, no need to send it

            switch(type){
                case "checkbox":
                    needed = $(input).is(":checked");
                    break;
                case "text":
                    value = $(input).val();
                    break;
                case "radio":
                    needed = $(input).is(":checked");
                    break;
                case "button":
                case "file":
                    needed = false;
                    //do nothing as the files are all saved
                    break;
                case "hidden":
                    break;
                default:
                    _log("Error, unknown input type: " + type);
                    break;
            }

            if (needed){
                if(value != ""){
                    form_array.push({
                        "name" : name,
                        "value" : value,
                        "type" : type
                    });
                }
            }
        });

        //TEXTAREAS
        form.find('textarea').each(function(index, textarea) {
            name = $(textarea).attr('name');
            value = $(textarea).val();
            type = "textarea";

            if(value != ""){
                form_array.push({
                    "name" : name,
                    "value" : value,
                    "type" : type
                });
            }
        });

        //SELECTS
        form.find("select").each(function(index, select) {
            name = $(select).attr('name');
            value = $(select).find(":selected").val();
            type = "select";

            if(value != ""){
                form_array.push({
                    "name" : name,
                    "value" : value,
                    "type" : type
                });
            }
        });

        return form_array;
    };

    return m;
}(app.form || {}, app.$ || jQuery));