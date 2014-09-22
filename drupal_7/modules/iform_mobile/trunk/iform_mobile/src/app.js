app = (function(m, $){
    //configuration should be setup in app config file
    m.CONF = {
        HOME: "",
        DEBUG: false
    };

    //GLOBALS
    m.$ = $;

    //CONSTANTS:
    m.TRUE = 1;
    m.FALSE = 0;
    m.ERROR = -1;

    /*
        Events from.
        http://jqmtricks.wordpress.com/2014/03/26/jquery-mobile-page-events/
     */
    m.pageEvents = [
        'pagebeforecreate',
        'pagecreate',
        'pagecontainerbeforechange ',
        'pagecontainerbeforetransition',
        'pagecontainerbeforehide',
        'pagecontainerhide',
        'pagecontainerbeforeshow',
        'pagecontainershow',
        'pagecontainertransition',
        'pagecontainerchange',
        'pagecontainerchangefailed',
        'pagecontainerbeforeload',
        'pagecontainerload',
        'pagecontainerloadfailed',
        'pagecontainerremove'
    ];

    /**
     * Init function
     */
    m.initialise = function(){
        _log('App initialised.');

        //todo: needs tidying up
        //Bind JQM page events with page controller handlers
        $(document).on(app.pageEvents.join(' '), function (e, data) {
            var event = e.type;
            var id = null;
            switch(event){
                case 'pagecreate':
                case 'pagecontainerbeforechange':
                    id = data.prevPage != null ? data.prevPage[0].id : e.target.id;
                    break;

                case 'pagebeforecreate':
                    id = e.target.id;
                    break;

                case 'pagecontainershow':
                case 'pagecontainerbeforetransition':
                case 'pagecontainerbeforehide':
                case 'pagecontainerbeforeshow':
                case 'pagecontainertransition':
                case 'pagecontainerhide':
                case 'pagecontainerchangefailed':
                case 'pagecontainerchange':
                    id = data.toPage[0].id;
                    break;

                case 'pagecontainerbeforeload':
                case 'pagecontainerload':
                case 'pagecontainerloadfailed':
                default:
                    break;
            }

              //  var ihd = e.target.id || data.toPage[0].id;
                var controller = app.controller[id];

                //if page has controller and it has an event handler
                if (controller && controller[event]) {
                    controller[event](e, data);
                }
            });
        };

    m.initSettings = function(){
        app.storage.set('settings', {});
    };

    m.settings = function(item, data){
        var settings = app.storage.get('settings');
        if (settings == null){
            app.initSettings();
            settings = app.storage.get('settings');
        }

        if(data != null){
            settings[item] = data;
            return app.storage.set('settings', settings);
        } else {
            return (item != undefined) ? settings[item] : settings;
        }
    };

    return m;
}(window.app || {}, jQuery)); //END