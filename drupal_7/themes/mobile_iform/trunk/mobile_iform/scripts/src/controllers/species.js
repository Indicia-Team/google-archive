(function($){
    app.controller = app.controller || {};
    app.controller.species = {
        pagecreate: function(){

        },

        pagecontainershow: function(event, ui){
            _log('species: pagecontainershow.');
            //var id = app.controller.list.getCurrentSpecies().id;

            var template = $('#species-template').html();
            var placeholder = $('#species-placeholder');

            var compiled_template = Handlebars.compile(template);

            var species = app.controller.list.getCurrentSpecies();

            //check for the favourite
            var favourites = app.controller.list.getFavourites();
            if (favourites[species.id] != null){
                $("#species-profile-fav-button").addClass("on");
            } else {
                $("#species-profile-fav-button").removeClass("on");
            }

            placeholder.html(compiled_template(species));
            placeholder.trigger('create');

            //app.controller.species.gallery.init($('.gallery').attr('id'));
        },

        /**
         * Toggles the current species as favourite by saving it into the
         * storage and changing the buttons appearance.
         */
        toggleSpeciesFavourite: function(){
            var favButton = $("#species-profile-fav-button");
            favButton.toggleClass("on");

            var species = app.controller.list.getCurrentSpecies();
            app.controller.list.changeFavourite(species.id, favButton.hasClass('on'));
        },

        /**
         *
         */
        gallery: {

            gallery : {},
            init : function(gallery_id){
                this.gallery =  $('#' + gallery_id + ' a').photoSwipe({
                    jQueryMobile: true,
                    loop: false,
                    enableMouseWheel: false,
                    enableKeyboard: false
                });
            },

            show : function(){
                this.gallery.show(0);
            }
        }
    };
}(jQuery));

