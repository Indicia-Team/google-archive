/**
 * Takes care of the record storage functionality.
 */

app = app || {};
app.record = app.record || {};

app.record.storage = (function(m, $){
    m.RECORDS = "records";

    /**
     * Gets a specific saved record from the storage.
     * @param recordStorageId The stored record Id.
     * @returns {*}
     */
    m.get =  function(recordStorageId){
        var records = this.getAll();
        return records[recordStorageId];
    };

    /**
     * Brings back all saved records from the storage.
     * @returns {*|{lat: *, lon: *, acc: *}|{}}
     */
    m.getAll =  function(){
        return app.storage.get(m.RECORDS) || {};
    };

    /**
     * Saves all the records in the storage.
     * @param records
     */
    m.setAll = function(records){
        app.storage.set(m.RECORDS, records);
    };

    /**
     * Returns a specific saved record in FormData format.
     * @param recordStorageId
     * @returns {FormData}
     */
    m.getData =  function(recordStorageId){
        var data = new FormData();

        //Extract data from storage
        var savedRecord = this.get(recordStorageId);
        for (var k = 0; k < savedRecord.length; k++) {
            if (savedRecord[k].type == "file") {
                var pic_file = app.image.storage.get(savedRecord[k].value);
                if (pic_file != null) {
                    _log("SEND - attaching '" + savedRecord[k].value + "' to " + savedForm[k].name);
                    var type = pic_file.split(";")[0].split(":")[1];
                    var extension = type.split("/")[1];
                    data.append(savedRecord[k].name, dataURItoBlob(pic_file, type), "pic." + extension);
                } else {
                    _log("SEND - " + savedRecord[k].value + " is " + pic_file);
                }
            } else {
                var name = savedRecord[k].name;
                var value = savedRecord[k].value;
                data.append(name, value);
            }
        }
        return data;
    };

    /**
     * Clears all the saved records.
     */
    m.clear = function(){
        app.storage.set(m.RECORDS, {});

        //reset the form counter
        var settings = app.record.getSettings();
        settings[app.record.LASTID] = 0;
        app.record.setSettings(settings);
    };

    /**
     * Removes a saved record from the storage.
     * @param recordStorageId
     */
    m.remove =  function(recordStorageId){
        if(recordStorageId == null) return;

        _log("SEND - cleaning up");
        var records = this.getAll();

        //clean files
        var input = {};
        for (var i = 0; i < records[recordStorageId].length; i++){
            input = records[recordStorageId][i];
            if(input['type'] == 'file'){
                app.storage.remove(input['value']);
            }
        }
        //remove record and save
        delete records[recordStorageId];
        app.storage.set(m.RECORDS, records);
    };

    /**
     * Saves a record using dynamic inputs.
     */
    m.save = function(onSuccess){
        _log("Record.");
        //get new record ID
        var settings = app.record.getSettings();
        var savedRecordId = ++settings[app.record.LASTID];

        //INPUTS
        var onSaveAllFilesSuccess = function(files_array){
            //Put
            var record_array = app.record.extract();

            //merge files and the rest of the inputs
            record_array = record_array.concat(files_array);

            _log("Record - saving the record into storage");
            try{
                var records = app.record.storage.getAll();
                records[savedRecordId] = record_array;
                m.setAll(records);
                app.record.setSettings(settings);
            } catch (e){
                _log("Record - ERROR while saving the record");
                _log(e);
                return app.ERROR;
            }

            app.record.clear();

            if(typeof onSuccess != 'undefined'){
                onSuccess(savedRecordId);
            }
        };

        var files = app.image.extractAll();
        app.image.storage.saveAll(files, onSaveAllFilesSuccess);
        return app.TRUE;
    };

    /*
     * Saves the provided record.
     * Returns the savedRecordId of the saved record, otherwise an app.ERROR.
     */
    m.saveUsingRecordId =  function(recordId, onSuccess){
        _log("Record.");
        var records = this.getAll();

        //get new record ID
        var settings = app.record.getSettings();
        var savedRecordId = ++settings[app.record.LASTID];

        //INPUTS
        var record = $(recordId);
        var onSaveAllFilesSuccess = function(files_array){
            //get all the inputs/selects/textboxes into array
            var record_array = app.record.extractFromRecord(record);

            //merge files and the rest of the inputs
            record_array = record_array.concat(files_array);

            _log("Record - saving the record into storage");
            try{
                records[savedRecordId] = record_array;
                m.setAll(records);
                app.record.setSettings(settings);
            } catch (e){
                _log("Record - ERROR while saving the record");
                _log(e);
                return app.ERROR;
            }
            if(typeof onSuccess != 'undefined'){
                onSuccess(savedRecordId);
            }
        };

        var files = app.image.extractAllFromRecord(record);
        app.image.storage.saveAll(files, onSaveAllFilesSuccess);
        return app.TRUE;
    };

    return m;
}(app.record.storage || {}, app.$ || jQuery));