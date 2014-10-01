/**
 *  Module responsible for large data management.
 */

app = app || {};
app.db = (function(m, $){

    m.DB_VERSION = 3;
    m.DB_MAIN = "app";
    m.STORE_RECORDS = "records";

    m.open = function(name, storeName, callback){
        var req = window.indexedDB.open(name, m.DB_VERSION);

        req.onsuccess = function(e){
            _log("Database opened successfully");
            var db = e.target.result;
            var transaction = db.transaction([storeName], "readwrite");
            var store = transaction.objectStore(storeName);

            if (callback != null){
                callback(store);
            }
        };

        req.onupgradeneeded = function(e){
            _log("Database is upgrading");
            var db = e.target.result;

            db.deleteObjectStore(app.db.STORE_RECORDS);
            db.createObjectStore(app.db.STORE_RECORDS);
        };

        req.onerror = function(e){
            _log("Database NOT opened successfully");
            _log(e);
        };

        req.onblocked = function(e){
            _log("Opening database blocked");
            _log(e);
        }

    };

    m.add = function(record, key, callback){
        m.open(m.DB_MAIN, m.STORE_RECORDS, function(store){
            _log("Adding to the store.");

            store.add(record, key);
            store.transaction.db.close();

            if(callback != null){
                callback();
            }
        });
    };

    m.get = function(key, callback){
        m.open(m.DB_MAIN, m.STORE_RECORDS, function(store){
            _log('Getting from the store.');

            var result = store.get(key);
            if(callback != null) {
                callback(result);
            }

        });
    };

    m.getAll = function(callback){
        m.open(m.DB_MAIN, m.STORE_RECORDS, function(store){
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

        });
    };

    m.is = function(key, callback){

    };

    m.clear = function(callback){
        m.open(m.DB_MAIN, m.STORE_RECORDS, function(store){
            _log('Clearing store');
            store.clear();

            if(callback != null) {
                callback(data);
            }
        });
    };


    return m;
}(app.db || {}, app.$ || jQuery));