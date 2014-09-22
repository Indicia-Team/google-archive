app = app || {};
app.image = (function(m, $){
    m.MAX_IMG_HEIGHT = 800;
    m.MAX_IMG_WIDTH = 800;

    m.setImage = function(input, output){
        var img_holder = 'sample-image-placeholder';
        var upload = $(input);

        if (typeof window.FileReader === 'undefined') {
            return false;
        }

        // upload.before(sample_tmpl);
        $('#photo').append('<div id="' + img_holder + '"></div>');

        $('#' + img_holder).on('click', function(){
            upload.click();
        });

        upload.change(function (e) {
            e.preventDefault();
            var file = this.files[0];
            var reader = new FileReader();

            reader.onload = function (event) {
                var img = new Image();
                img.src = event.target.result;
                // note: no onload required since we've got the dataurl...I think! :)
                if (img.width > 560) { // holder width
                    img.width = 560;
                }
                $('#sample-image-placeholder').empty().append(img);
                $('#' + img_holder).css('border', '0px');
                //$('#' + img_holder).css('background-color', 'transparent');
                $('#' + img_holder).css('background-image', 'none');
            };
            reader.readAsDataURL(file);

            return false;
        });

    };

    /**
     * Responsible for image storage functionality
     * @type {{get: get, save: save, saveAll: saveAll}}
     */
    m.storage = {
        /**
         * Returns a specific saved image
         * @param item
         */
        get: function(item){
            app.storage.get(item);
        },

        /**
         * Transforms and resizes an image file into a string and saves it in the
         * storage.
         * @param key
         * @param file
         * @param onSaveSuccess
         * @returns {number}
         */
        save: function(key, file, onSaveSuccess){
            if (file != null) {
                _log("FORM - working with " + file.name);
                //todo: not to hardcode the size
                if (!app.storage.hasSpace(file.size/4)){
                    return file_storage_status = app.ERROR;
                }

                var reader = new FileReader();
                //#2
                reader.onload = function() {
                    _log("FORM - resizing file");
                    var image = new Image();
                    //#4
                    image.onload = function(e){
                        var width = image.width;
                        var height = image.height;

                        //resizing
                        var res;
                        if (width > height){
                            res = width / app.image.MAX_IMG_WIDTH;
                        } else {
                            res = height / app.image.MAX_IMG_HEIGHT;
                        }

                        width = width / res;
                        height = height / res;

                        var canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;

                        var imgContext = canvas.getContext('2d');
                        imgContext.drawImage(image, 0, 0, width, height);

                        var shrinked = canvas.toDataURL(file.type);
                        try {
                            _log("FORM - saving file in storage ("
                                + (shrinked.length / 1024) + "KB)" );

                            app.storage.set(key,  shrinked); //stores the image to localStorage
                            onSaveSuccess();
                        }
                        catch (e) {
                            _log("FORM - saving file in storage failed: " + e);
                        }
                    };
                    //#3
                    image.src = reader.result;
                };
                //1#
                reader.readAsDataURL(file);
            }
        },

        /**
         * Saves all the files. Uses recursion.
         * @param files An array of files to be saved
         * @param onSaveAllFilesSuccess
         */
        saveAll: function(files, onSaveAllFilesSuccess){
            //recursive calling to save all the images
            saveAllFilesRecursive(files, null);
            function saveAllFilesRecursive(files, files_array){
                files_array = files_array || [];

                //recursive files saving
                if(files.length > 0){
                    var file_info = files.pop();
                    //get next file in file array
                    var file = file_info['file'];
                    var value = Date.now() + "_" + file['name'];
                    var name = file_info['input_field_name'];

                    //recursive saving of the files
                    var onSaveSuccess = function(){
                        files_array.push({
                            "name" : name,
                            "value" : value,
                            "type" : 'file'
                        });
                        saveAllFilesRecursive(files, files_array, onSaveSuccess);
                    };
                    app.image.storage.save(value, file, onSaveSuccess);
                } else {
                    onSaveAllFilesSuccess(files_array);
                }
            }
        }
    };

    /**
     * Extracts all files from the form into a form array.
     * @param form
     */
    m.extractAll =  function(form){
        var files = [];
        form.find('input').each(function(index, input) {
            if ($(input).attr('type') == "file" && input.files.length > 0) {
                var file = app.image.extract(input);
                files.push(file);
            }
        });
        return files;
    };

    /**
     * Returns a file object with its name.
     * @param inputId The file input Id
     * @returns {{file: *, input_field_name: *}}
     */
    m.extract = function(input){
       var file = {
            'file' : input.files[0],
            'input_field_name' : input.attributes.name.value
        };
        return file;
    };

    return m;
}(app.image || {}, jQuery));

