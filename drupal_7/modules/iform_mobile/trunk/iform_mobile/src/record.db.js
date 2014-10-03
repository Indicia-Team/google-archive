/**
 * Takes care of the record database functionality.
 */

app = app || {};
app.record = app.record || {};

app.record.db = (function(m, $){
    m.RECORDS = "records";

    m.DB_VERSION = 5;
    m.DB_MAIN = "app";
    m.STORE_RECORDS = "records";

    /**
     * Opens a database connection and returns a records store.
     * @param name
     * @param storeName
     * @param callback
     */
    m.open = function(callback, onError){
        var req = window.indexedDB.open(m.DB_MAIN, m.DB_VERSION);

        req.onsuccess = function(e){
            _log("Database opened successfully");
            var db = e.target.result;
            var transaction = db.transaction([m.STORE_RECORDS], "readwrite");
            var store = transaction.objectStore(m.STORE_RECORDS);

            if (callback != null){
                callback(store);
            }
        };

        req.onupgradeneeded = function(e){
            _log("Database is upgrading");
            var db = e.target.result;

            var store = db.createObjectStore(m.STORE_RECORDS, {'keyPath': 'id'});
            store.createIndex('id', 'id', {unique: true});
        };

        req.onerror = function(e){
            _log("Database NOT opened successfully");
            _log(e);
            e.message = "Database NOT opened successfully";
            if (onError != null) {
                onError(e);
            }
        };

        req.onblocked = function(e){
            _log("Opening database blocked");
            _log(e);
            if (onError != null) {
                onError(e);
            }
        }

    };

    /**
     * Adds a record to the database.
     * @param record
     * @param key
     * @param callback
     */
    m.add = function(record, key, callback, onError){
        m.open(function(store){
            _log("Adding to the store.");
            record['id'] = key;
            store.add(record);
            store.transaction.db.close();

            if(callback != null){
                callback();
            }
        }, onError);
    };

    /**
     * Gets a specific saved record from the database.
     * @param recordKey The stored record Id.
     * @returns {*}
     */
    m.get = function(key, callback, onError){
        m.open(function(store){
            _log('Getting from the store.');

            var req = store.index('id').get(key);
            req.onsuccess = function(e) {
                var result = e.target.result;

                if (callback != null) {
                    callback(result);
                }
            };

        }, onError);
    };

    /**
     * Removes a saved record from the database.
     * @param recordKey
     */
    m.remove =  function(key, callback, onError){
        m.open(function(store){
            _log('Removing from the store.');

            var req = store.openCursor(IDBKeyRange.only(key));
            req.onsuccess = function(){
                var cursor = req.result;
                if(cursor){
                    store.delete(cursor.primaryKey);
                    cursor.continue();
                } else {
                    if(callback != null) {
                        callback();
                    }
                }
            }

        }, onError);
    };

    /**
     * Brings back all saved records from the database.
     * @returns {*|{lat: *, lon: *, acc: *}|{}}
     */
    m.getAll = function(callback, onError){
        m.open(function(store){
            _log('Getting all from the store.');

            // Get everything in the store
            var keyRange = IDBKeyRange.lowerBound(0);
            var req = store.openCursor(keyRange);

            var data = [];
            req.onsuccess = function(e) {
                var result = e.target.result;

                // If there's data, add it to array
                if (result) {
                    data.push(result.value);
                    result.continue();

                    // Reach the end of the data
                } else {
                    if(callback != null) {
                        callback(data);
                    }
                }
            };

        }, onError);
    };

    m.is = function(key, callback, onError){
        function onSuccess(data) {
            if ($.isPlainObject(data)) {
                if(callback != null) {
                    callback(!$.isEmptyObject(data));
                }
            } else {
                if(callback != null) {
                    callback(data != null);
                }
            }
        }
        this.get(key, onSuccess, onError);
    };

    /**
     * Clears all the saved records.
     */
    m.clear = function(callback, onError){
        m.open(function(store){
            _log('Clearing store');
            store.clear();

            if(callback != null) {
                callback(data);
            }
        }, onError);
    };

    /**
     * Returns a specific saved record in FormData format.
     * @param recordKey
     * @returns {FormData}
     */
    m.getData =  function(recordKey, callback, onError){
        function onSuccess(savedRecord) {
            var data = new FormData();

            for (var k = 0; k < savedRecord.length; k++) {
                if (savedRecord[k].type == "file") {
                    var file = savedRecord[k].value;
                    var type = file.split(";")[0].split(":")[1];
                    var extension = type.split("/")[1];
                    data.append(savedRecord[k].name, dataURItoBlob(file, type), "pic." + extension);
                } else {
                    var name = savedRecord[k].name;
                    var value = savedRecord[k].value;
                    data.append(name, value);
                }
            }
            callback(data);
        }

        //Extract data from database
        var savedRecord = this.get(recordKey, onSuccess, onError);
    };

    /**
     * Saves a record using dynamic inputs.
     */
    m.save = function(callback, onError){
        _log("Record.");
        //get new record ID
        var settings = app.record.getSettings();
        var savedRecordId = ++settings[app.record.LASTID];

        //INPUTS
        var onExtractFilesSuccess = function(files_array){
            var record_array = app.record.extract();
            //merge files and the rest of the inputs
            record_array = record_array.concat(files_array);

            _log("Record - saving the record into database");
            function onSuccess(){
                //on record save success
                app.record.setSettings(settings);

                if (callback != null) {
                    callback(savedRecordId);
                }
            }
            m.add(record_array, savedRecordId, onSuccess, onError);
        };

        app.image.extractAllToArray(null, onExtractFilesSuccess, onError);
        return app.TRUE;
    };

    /*
     * Saves the provided record.
     * Returns the savedRecordId of the saved record, otherwise an app.ERROR.
     */
    m.saveForm =  function(formId, onSuccess){
        _log("Record.");
        var records = this.getAll();

        //get new record ID
        var settings = app.record.getSettings();
        var savedRecordId = ++settings[app.record.LASTID];

        //INPUTS
        var record = $(formId);
        var onSaveAllFilesSuccess = function(files_array){
            //get all the inputs/selects/textboxes into array
            var record_array = app.record.extractFromRecord(record);

            //merge files and the rest of the inputs
            record_array = record_array.concat(files_array);

            _log("Record - saving the record into database");
            try{
                records[savedRecordId] = record_array;
                m.setAll(records);
                app.record.setSettings(settings);
            } catch (e){
                _log("Record - ERROR while saving the record");
                _log(e);
                return app.ERROR;
            }

            if(onSuccess != null){
                onSuccess(savedRecordId);
            }
        };

        app.image.getAll(onSaveAllFilesSuccess);
        return app.TRUE;
    };

    return m;
}(app.record.db || {}, app.$ || jQuery));