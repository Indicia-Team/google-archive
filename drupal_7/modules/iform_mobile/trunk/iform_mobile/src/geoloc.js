app = app || {};

app.geoloc = (function(m, $){
    m.TIMEOUT = 120000;
    m.HIGH_ACCURACY = true;

    //configuration should be setup in app config file
    m.CONF = {
        GPS_ACCURACY_LIMIT: 26000
    };

    m.latitude = null;
    m.longitude = null;
    m.accuracy = -1;

    m.start_time = 0;
    m.tries = 0;
    m.id = 0;
    m.map = null;

    /**
     *
     * @returns {*}
     */
    m.run = function(onSuccess, onError){
        _log('GEOLOC: run.');

        // Early return if geolocation not supported.
        if(!navigator.geolocation) {
            _log("GEOLOC: ERROR not supported!");
            if (onError != null) {
                onError({message: "Geolocation is not supported!"});
            }
            return;
        }

        //stop any other geolocation service started before
        navigator.geolocation.clearWatch(this.id);

        //check if the lock is acquired and the accuracy is good enough
        var accuracy = app.geoloc.getAccuracy();
        if ((accuracy > -1) && (accuracy < this.CONF.GPS_ACCURACY_LIMIT)){
            if (onSuccess != null) {
                onSuccess(this.get());
            }
        }

        this.start_time = new Date().getTime();
        this.tries = (this.tries == 0) ? 1 : this.tries +  1;

        // Request geolocation.
        this.id = app.geoloc.watchPosition(onSuccess, onError);
    };

    /*
     * Validates the current GPS lock quality
     * @returns {*}
     */
    m.valid = function(){
        var accuracy = this.getAccuracy();
        if ( accuracy == -1 ){
            //No GPS lock yet
            return app.ERROR;

        } else if (accuracy > this.CONF.GPS_ACCURACY_LIMIT){
            //Geolocated with bad accuracy
            return app.FALSE;

        } else {
            //Geolocation accuracy is good enough
            return app.TRUE;
        }
    };

    /**
     *
     */
    m.watchPosition = function(onSuccess, onError){
        // Geolocation options.
        var options = {
            enableHighAccuracy: app.geoloc.HIGH_ACCURACY,
            maximumAge: 0,
            timeout: app.geoloc.TIMEOUT
        };

        onGeolocSuccess = function(position) {
            //timeout
            var current_time = new Date().getTime();
            if ((current_time - app.geoloc.start_time) > app.geoloc.TIMEOUT){
                //stop everything
                navigator.geolocation.clearWatch(app.geoloc.id);
                _log("GEOLOC: ERROR timeout.");
                if (onError != null) {
                    onError({message: "Geolocation timed out!"});
                }
                return;
            }

            var latitude  = position.coords.latitude;
            var longitude = position.coords.longitude;
            var accuracy = position.coords.accuracy;

            //set for the first time
            var prev_accuracy = app.geoloc.getAccuracy();
            if (prev_accuracy == -1){
                prev_accuracy = accuracy + 1;
            }

            //only set it up if the accuracy is increased
            if (accuracy > -1 && accuracy < prev_accuracy){
                app.geoloc.set(latitude, longitude, accuracy);
                _log("GEOLOC: acc: " + accuracy + " meters." );
                if (accuracy < app.geoloc.CONF.GPS_ACCURACY_LIMIT){
                    _log("GEOLOC: finished: " + accuracy + " meters.");
                    navigator.geolocation.clearWatch(app.geoloc.id);

                    //save in storage
                    var location = {
                        'lat' : latitude,
                        'lon' : longitude,
                        'acc' : accuracy
                    };

                    app.settings('location', location);
                    if (onSuccess != null) {
                        onSuccess(location);
                    }
                }
            }
        };

        // Callback if geolocation fails.
        onGeolocError = function(error) {
            _log("GEOLOC: ERROR.");
            if (onError != null) {
                onError({'message': error.message});
            }
        };

        navigator.geolocation.watchPosition(
            onGeolocSuccess,
            onGeolocError,
            options);
    };

    /**
     * @param lat
     * @param lon
     * @param acc
     */
    m.set = function(lat, lon, acc){
        this.latitude = lat;
        this.longitude = lon;
        this.accuracy = acc;
    };

    /**
     *
     * @returns {{lat: *, lon: *, acc: *}}
     */
    m.get = function(){
        return {
            'lat' : this.latitude,
            'lon' : this.longitude,
            'acc' : this.accuracy
        }
    };

    /**
     *
     * @returns {*}
     */
    m.getAccuracy = function(){
        return this.accuracy;
    };

    return m;
})(app.geoloc || {}, app.$ || jQuery);
