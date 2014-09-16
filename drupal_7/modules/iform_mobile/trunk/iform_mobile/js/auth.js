app = app || {};
app.auth = (function(m, $){
    //module configuration should be setup in an app config file
    m.CONF = {
        APPNAME: "",
        APPSECRET: ""
    };

    /**
     * Appends user and app authentication to the passed FormData object.
     *
     * @param formData A FormData object to modify
     * @returns {*} A FormData object
     */
    m.append = function(formData){
        //user logins
        formData = m.appendUser(formData);
        //app logins
        formData = m.appendApp(formData);

        return formData;
    };

    /**
     * Appends user authentication - Email and Password to
     * the passed FormData object.
     *
     * @param formData A FormData object to modify
     * @returns {*} A FormData object
     */
    m.appendUser = function(formData){
        var user = m.getUser();
        if (m.isUser()){
            formData.append('email', user.email);
            formData.append('password', user.password)
        }

        return formData;
    };

    /**
     * Appends app authentication - Appname and Appsecret to
     * the passed FormData object.
     *
     * @param formData A FormData object to modify
     * @returns {*} A FormData object
     */
    m.appendApp = function(formData){
        formData.append('appname', this.CONF.APPNAME);
        formData.append('appsecret', this.CONF.APPSECRET);

        return formData;
    };

    /**
     * Checks if the user has authenticated with the app.
     * @returns {boolean} True if the user exists, else False
     */
    m.isUser = function(){
        var user = m.getUser();
        return !$.isEmptyObject(user);
    };

    /**
     * Brings the user details from the storage.
     * @returns {Object|*}
     */
    m.getUser = function(){
        return app.settings('user');
    };

    /**
     * Saves the authenticated user details to the storage.
     * @param user A user object
     */
    m.setUser = function(user){
        app.settings('user', user);
    };

    /**
     * Removes the current user details from the storage.
     */
    m.removeUser = function(){
        app.settings('user', {});
    };

    return m;
}(app.auth || {}, jQuery));

