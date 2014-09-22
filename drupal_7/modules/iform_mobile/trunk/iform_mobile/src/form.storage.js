/**
 * Takes care of the form storage functionality.
 */

app = app || {};
app.form = app.form || {};

app.form.storage = (function(m, $){
    m.FORMS = "forms";

    /**
     * Gets a specific saved form from the storage.
     * @param formStorageId The stored form Id.
     * @returns {*}
     */
    m.get =  function(formStorageId){
        var forms = this.getAll();
        return forms[formStorageId];
    };

    /**
     * Brings back all saved forms from the storage.
     * @returns {*|{lat: *, lon: *, acc: *}|{}}
     */
    m.getAll =  function(){
        return app.storage.get(m.FORMS) || {};
    };

    /**
     * Saves all the forms in the storage.
     * @param forms
     */
    m.setAll = function(forms){
        app.storage.set(m.FORMS, forms);
    };

    /**
     * Returns a specific saved form in FormData format.
     * @param formStorageId
     * @returns {FormData}
     */
    m.getData =  function(formStorageId){
        var data = new FormData();

        //Extract data from storage
        var savedForm = this.get(formStorageId);
        for (var k = 0; k < savedForm.length; k++) {
            if (savedForm[k].type == "file") {
                var pic_file = app.image.storage.get(savedForm[k].value);
                if (pic_file != null) {
                    _log("SEND - attaching '" + savedForm[k].value + "' to " + savedForm[k].name);
                    var type = pic_file.split(";")[0].split(":")[1];
                    var extension = type.split("/")[1];
                    data.append(savedForm[k].name, dataURItoBlob(pic_file, type), "pic." + extension);
                } else {
                    _log("SEND - " + savedForm[k].value + " is " + pic_file);
                }
            } else {
                var name = savedForm[k].name;
                var value = savedForm[k].value;
                data.append(name, value);
            }
        }
        return data;
    };

    /**
     * Removes a saved form from the storage.
     * @param formStorageId
     */
    m.remove =  function(formStorageId){
        if(formStorageId == null) return;

        _log("SEND - cleaning up");
        var forms = this.getAll();

        //clean files
        var input = {};
        for (var i = 0; i < forms[formStorageId].length; i++){
            input = forms[formStorageId][i];
            if(input['type'] == 'file'){
                app.storage.remove(input['value']);
            }
        }
        //remove form and save
        delete forms[formStorageId];
        app.storage.set(m.FORMS, forms);
    };

    /**
     * Saves a form using dynamic inputs.
     */
    m.save = function(onSuccess){
        _log("FORM.");
        //get new form ID
        var settings = app.form.getSettings();
        var savedFormId = ++settings[app.form.LASTID];

        //INPUTS
        var onSaveAllFilesSuccess = function(files_array){
            //Put
            var form_array = app.form.extract();

            //merge files and the rest of the inputs
            form_array = form_array.concat(files_array);

            _log("FORM - saving the form into storage");
            try{
                var forms = app.form.storage.getAll();
                forms[savedFormId] = form_array;
                m.setAll(forms);
                app.form.setSettings(settings);
            } catch (e){
                _log("FORM - ERROR while saving the form");
                _log(e);
                return app.ERROR;
            }

            app.form.inputs.clearRecord();

            if(typeof onSuccess != 'undefined'){
                onSuccess(savedFormId);
            }
        };

        var files = app.image.extractAll();
        app.image.storage.saveAll(files, onSaveAllFilesSuccess);
        return app.TRUE;
    };

    /*
     * Saves the provided form.
     * Returns the savedFormId of the saved form, otherwise an app.ERROR.
     */
    m.saveUsingFormId =  function(formId, onSuccess){
        _log("FORM.");
        var forms = this.getAll();

        //get new form ID
        var settings = app.form.getSettings();
        var savedFormId = ++settings[app.form.LASTID];

        //INPUTS
        var form = $(formId);
        var onSaveAllFilesSuccess = function(files_array){
            //get all the inputs/selects/textboxes into array
            var form_array = app.form.extractFromForm(form);

            //merge files and the rest of the inputs
            form_array = form_array.concat(files_array);

            _log("FORM - saving the form into storage");
            try{
                forms[savedFormId] = form_array;
                m.setAll(forms);
                app.form.setSettings(settings);
            } catch (e){
                _log("FORM - ERROR while saving the form");
                _log(e);
                return app.ERROR;
            }
            if(typeof onSuccess != 'undefined'){
                onSuccess(savedFormId);
            }
        };

        var files = app.image.extractAllFromForm(form);
        app.image.storage.saveAll(files, onSaveAllFilesSuccess);
        return app.TRUE;
    };

    return m;
}(app.form.storage || {}, app.$ || jQuery));