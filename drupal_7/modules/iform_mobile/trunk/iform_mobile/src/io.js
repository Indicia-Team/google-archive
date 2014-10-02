app = app || {};
app.io = (function(m, $){
    //configuration should be setup in app config file
    m.CONF = {
        RECORD_URL: ""
    };

    /*
     * Sending all saved records.
     * @returns {undefined}
     */
    m.sendAllSavedRecords = function() {
        if (navigator.onLine) {
            function onSuccess() {
                //todo
                var key = Object.keys(records)[0]; //getting the first one of the array
                if (key != null) {
                    $.mobile.loading('show');
                    _log("Sending record: " + key);
                    var onSendSavedSuccess = function (data) {
                        var recordKey = this.callback_data.recordKey;
                        _log("SEND - record ajax (success): " + recordKey);

                        app.record.db.remove(recordKey);
                        $(document).trigger('app.record.sentall.success');
                        app.io.sendAllSavedRecords();
                    };
                    m.sendSavedRecord(key, onSendSavedSuccess);
                } else {
                    $.mobile.loading('hide');
                }
            }
            app.record.db.getAll(onSuccess);
        } else {
            $.mobile.loading( 'show', {
                text: "Looks like you are offline!",
                theme: "b",
                textVisible: true,
                textonly: true
            });

            setTimeout(function(){
                $.mobile.loading('hide');
            }, 3000);
        }
    };

    /*
     * Sends the saved record
     */
    m.sendSavedRecord = function(recordKey, callback, onError, onSend) {
        _log("SEND - creating the record.");
        function onSuccess(data) {
            var record = {
                'data': data,
                'recordKey': recordKey
            };

            m.postRecord(record, callback, onError, onSend)
        }
        app.record.db.getData(recordKey, onSuccess);

    };

    /*
     * Submits the record.
     */
    m.postRecord = function(record, onSuccess, onError, onSend){
        _log('SEND - Posting a record with AJAX.');
        var data = {};
        if(record.data == null){
            //extract the record data
            form = document.getElementById(record.id);
            data = new FormData(form);
        } else {
            data = record.data;
        }

        //Add authentication
        data = app.auth.append(data);

        $.ajax({
            url : m.getRecordURL(),
            type : 'POST',
            data : data,
            callback_data : record,
            cache : false,
            enctype : 'multipart/form-data',
            processData : false,
            contentType : false,
            success: onSuccess || m.onSuccess,
            error: onError || m.onError,
            beforeSend: onSend || m.onSend
        });
    };

    /**
     * Function callback on Successful Ajax record post.
     * @param data
     */
    m.onSuccess = function(data){
        var recordKey = this.callback_data.recordKey;
        _log("SEND - record ajax (success): " + recordKey);

        app.record.db.remove(recordKey);
        $(document).trigger('app.record.sent.success', [data]);
    };

    /**
     * Function callback on Error Ajax record post.
     * @param xhr
     * @param ajaxOptions
     * @param thrownError
     */
    m.onError = function (xhr, ajaxOptions, thrownError) {
        _log("SEND - record ajax (ERROR "  + xhr.status+ " " + thrownError +")");
        _log(xhr.responseText);

        $(document).trigger('app.record.sent.error', [xhr, thrownError]);
        //TODO:might be a good idea to add a save option here
    };

    /**
     * Function callback before sending the Ajax record post.
     */
    m.onSend = function () {
        _log("SEND - onSend");
    };

    /**
     * Returns App main record Path.
     * @returns {*}
     */
    m.getRecordURL = function(){
        return Drupal.settings.basePath + m.CONF.RECORD_URL;
    };

    /**
     * Services related functions.
     */
    m.services = {};

    /**
     * Main function to Send/Receive request
     */
    m.services.req = function(url, data, onSuccess, onError) {
        var req = new XMLHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                if (req.status == 200) {
                    if(onSuccess != null){
                        onSuccess(JSON.parse(req.responseText));
                    }
                }
                else {
                    if (onError != null){
                        onError(req);
                    }
                }
            }
        };

        if (data != null){
            //post
            req.open('POST', url, true);
            req.setRequestHeader("Content-type", "application/json");
            req.send(JSON.stringify(this.data));
        } else {
            //get
            req.open('GET', url, true);
            req.send();
        }
    };

    return m;
}(app.io || {}, jQuery));