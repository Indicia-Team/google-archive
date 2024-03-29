/**
 * Indicia, the OPAL Online Recording Toolkit.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 */

/**
* Class: uploader
* A jQuery plugin that provides an upload box for multiple images.
*/


(function($) {
  $.fn.uploader = function(options) {
    // Extend our default options with those provided, basing this on an empty object
    // so the defaults don't get changed.
    var opts = $.extend({}, $.fn.uploader.defaults, options);
    
    if (typeof opts.jsPath == "undefined") {
      alert('The file_box control requires a jsPath setting to operate correctly. It should point to the URL '+
          'path of the media/js folder.');
    }
    return this.each(function() {
      var uploadSelectBtn='', flickrSelectBtn='', uploadStartBtn='', id=Math.floor((Math.random())*0x10000);
      this.settings = opts;
      if (this.settings.upload) {
        uploadSelectBtn = this.settings.buttonTemplate
            .replace('{caption}', this.settings.uploadSelectBtnCaption)
            .replace('{id}', 'upload-select-btn-' + id);
        if (!this.settings.autoupload) {
          uploadStartBtn = this.settings.buttonTemplate
              .replace('{caption}', this.settings.uploadStartBtnCaption)
              .replace('{id}', 'upload-start-btn-' + id);
        }
      }
      if (this.settings.flickr) {
        flickrSelectBtn = this.settings.buttonTemplate
            .replace('{caption}', this.settings.flickrSelectBtnCaption)
            .replace('{id}', 'flickr-select-btn-' + id);
      }
      $(this).append(this.settings.file_boxTemplate
          .replace('{caption}', this.settings.caption)
          .replace('{uploadSelectBtn}', uploadSelectBtn)
          .replace('{flickrSelectBtn}', flickrSelectBtn)
          .replace('{uploadStartBtn}', uploadStartBtn)
      );
      // Set up a resize object if required
      var resize = (this.settings.resizeWidth!==0 && this.settings.resizeHeight!==0) ?
          { width: this.settings.resizeWidth, height: this.settings.resizeHeight, quality: this.settings.resizeQuality } : null;
      this.uploader = new plupload.Uploader({
        runtimes : this.settings.runtimes,
        container : this.id,
        browse_button : 'upload-select-btn-'+id,
        url : this.settings.uploadScript,
        resize : resize,
        flash_swf_url : this.settings.swfAndXapFolder + 'plupload.flash.swf',
        silverlight_xap_url : this.settings.swfAndXapFolder + 'plupload.silverlight.xap',
        filters : [
          {title : "Image files", extensions : "jpg,gif,png,jpeg"}
        ],
        // limit the max file size to the Indicia limit, unless it is first resized.
        max_file_size : resize ? '10mb' : plupload.formatSize(this.settings.maxUploadSize)
      });
      
      if (this.settings.autoupload) {
        this.uploader.bind('QueueChanged', function(up) {
          up.start();
        });
      }
      // make the main object accessible
      var div = this;
      
      // load the existing data if there is any
      var existing, uniqueId;
      $.each(div.settings.existingFiles, function(i, file) {
        uniqueId = file.path.split('.')[0];
        existing = div.settings.file_box_initial_file_infoTemplate.replace('{id}', uniqueId)
            .replace(/\{filename\}/g, file.caption)
            .replace(/\{filesize\}/g, 'Uploaded')
            .replace(/\{imagewidth\}/g, div.settings.imageWidth);
        $('#' + div.id.replace(/:/g,'\\:') + ' #filelist').append(existing);
        var thumbnailfilepath = div.settings.finalImageFolder + 'med-' + file.path;
        var origfilepath = div.settings.finalImageFolder + file.path;
        $('#' + uniqueId + ' .photo-wrapper').append(div.settings.file_box_uploaded_imageTemplate
              .replace(/\{id\}/g, uniqueId)
              .replace(/\{thumbnailfilepath\}/g, thumbnailfilepath)
              .replace(/\{origfilepath\}/g, origfilepath)
              .replace(/\{imagewidth\}/g, div.settings.imageWidth)
              .replace(/\{captionField\}/g, div.settings.table + ':caption:' + uniqueId)
              .replace(/\{captionValue\}/g, file.caption.replace(/\"/g, '&quot;'))
              .replace(/\{pathField\}/g, div.settings.table + ':path:' + uniqueId)
              .replace(/\{pathValue\}/g, file.path)
              .replace(/\{idField\}/g, div.settings.table + ':id:' + uniqueId) 
              .replace(/\{idValue\}/g, file.id) // If ID is set, the picture is uploaded to the server
        );
      });
      
      // Add a box to indicate a file that is added to the list to upload, but not yet uploaded.
      this.uploader.bind('FilesAdded', function(up, files) {
        // Find any files over the upload limit
        existingCount = $('#filelist').children().length;
        extras = files.splice(div.settings.maxFileCount - existingCount, 9999);
        if (extras.length!==0) {
          alert(div.settings.msgTooManyFiles.replace('[0]', div.settings.maxFileCount));
          // remove the extras from the queue
          $.each(extras, function(file) {
            div.uploader.removeFile(file);
          });
        }
        $.each(files, function(i, file) {
          if (resize===null && file.size>div.settings.maxUploadSize) {
            // We are not resizing, and the file is too big for the Indicia server. So display a warning.
            alert(div.settings.msgFileTooBig);
          } else {
            $('#filelist').append(div.settings.file_box_initial_file_infoTemplate.replace('{id}', file.id)
                .replace(/\{filename\}/g, file.name)
                .replace(/\{filesize\}/g, plupload.formatSize(file.size))
                .replace(/\{imagewidth\}/g, div.settings.imageWidth)
            );
            // change the file name to be unique
            file.name=plupload.guid() + '.jpg';
          }
          $('#' + file.id + ' .progress-percent').progressbar ({value: 0});
          if (div.settings.resizeWidth!==0 && div.settings.resizeHeight!==0) {
            $('#' + file.id + ' .progress-percent').html('Resizing...');
          } else {
            $('#' + file.id + ' .progress-percent').html('0% Uploaded...');
          }
        });
        
      });
      
      // As a file uploads, update the progress bar and percentage indicator
      this.uploader.bind('UploadProgress', function(up, file) {
        $('#' + file.id + ' .progress-bar').progressbar ('option', 'value', file.percent);
        $('#' + file.id + ' .progress-percent').html(file.percent + '% Uploaded...');
      });
      
      // On upload completion, check for errors, and show the uploaded file if OK.
      this.uploader.bind('FileUploaded', function(uploader, file, response) {
        $('#' + file.id + ' .progress').remove();
        // check the JSON for errors
        var resp = eval('['+response.response+']');
        if (resp[0].error) {
          $('#' + file.id).remove();
          alert(div.settings.msgUploadError + ' ' + resp[0].error.message);
        } else {
          var filepath = div.settings.destinationFolder + file.name;
          // Show the uploaded file, and also set the mini-form values to contain the file details.
          $('#' + file.id + ' .photo-wrapper').append(div.settings.file_box_uploaded_imageTemplate
                .replace(/\{id\}/g, file.id)
                .replace(/\{thumbnailfilepath\}/g, filepath)
                .replace(/\{origfilepath\}/g, filepath)
                .replace(/\{imagewidth\}/g, div.settings.imageWidth)
                .replace(/\{captionField\}/g, div.settings.table + ':caption:' + file.id)
                .replace(/\{captionValue\}/g, '')
                .replace(/\{pathField\}/g, div.settings.table + ':path:' + file.id)
                .replace(/\{pathValue\}/g, '')
                .replace(/\{idField\}/g, div.settings.table + ':id:' + file.id) 
                .replace(/\{idValue\}/g, '') // Set ID to blank, as this is a new record.
          );
          // Copy the path into the hidden path input. Watch colon escaping for jQuery selectors.
          $('#' + div.settings.table.replace(/:/g,'\\:') + '\\:path\\:' + file.id).val(file.name);
        }
      });
      
      this.uploader.init();
      
      if (this.settings.useFancybox) {
        // Hack to get fancybox working as a jQuery live, because some of our images load from AJAX calls. 
        // So we temporarily create a dummy link to our image and click it.
        $('a.fancybox').live('click', function() {
          jQuery("body").after('<a id="link_fancybox" style="display: hidden;" href="'+jQuery(this).attr('href')+'"></a>');
          jQuery('#link_fancybox').fancybox(); 
          jQuery('#link_fancybox').click();
          jQuery('#link_fancybox').remove();
          return false;
        });
      }
      
      $('#upload-start-btn-' + id).click(function(e) {
        div.uploader.start();
        e.preventDefault();
      });
    });
  };
})(jQuery);

/**
 * Main default options for the uploader
 */
$.fn.uploader.defaults = {
  caption : "Files",
  uploadSelectBtnCaption : 'Add File(s)',
  flickrSelectBtnCaption : 'Select photo on Flickr',
  uploadStartBtnCaption : 'Start Upload',
  useFancybox: true,
  imageWidth: 200,
  resizeWidth: 0,
  resizeHeight: 0,
  resizeQuality: 90,
  upload : true,
  flickr : true,
  autoupload : true,
  maxFileCount : 4,
  existingFiles : [],
  buttonTemplate : '<div class="indicia-button ui-state-default ui-corner-all" id="{id}"><span>{caption}</span></div>',
  file_boxTemplate : '<fieldset class="ui-corner-all">\n<legend>{caption}</legend>\n{uploadSelectBtn}\n{flickrSelectBtn}\n<div id="filelist"></div>' +
                 '{uploadStartBtn}</fieldset>',
  file_box_initial_file_infoTemplate : '<div id="{id}" class="ui-widget-content ui-corner-all photo"><div class="ui-widget-header ui-corner-all">{filename} ({filesize})</div><div class="progress"><div class="progress-bar" style="width: {imagewidth}px"></div><div class="progress-percent"></div></div><span class="photo-wrapper"></span></div>',
  file_box_uploaded_imageTemplate : '<a class="fancybox" href="{origfilepath}"><img src="{thumbnailfilepath}" width="{imagewidth}"/></a>' +
      '<input type="hidden" name="{idField}" id="{idField}" value="{idValue}" />' +
      '<input type="hidden" name="{pathField}" id="{pathField}" value="{pathValue}" />' +
      '<label for="{captionField}">Caption:</label><br/><input type="text" maxlength="100" style="width: {imagewidth}px" name="{captionField}" id="{captionField}" value="{captionValue}"/>',
  msgUploadError : 'An error occurred uploading the file.',
  msgFileTooBig : 'The file is too big to upload. Please resize it then try again.',
  msgTooManyFiles : 'Only [0] files can be uploaded.',
  uploadScript : 'upload.php',
  destinationFolder : '',
  swfAndXapFolder : '',
  runtimes : 'gears,silverlight,browserplus,html5,flash,html4'
};