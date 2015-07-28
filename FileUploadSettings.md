# Introduction #

As a website designer, when you add a filebox to a page for file uploading you can set various options that put limits on image sizes and file sizes. The web server that hosts your page will also have separate limits on upload sizes. Moreover the warehouse to which an uploaded file is then transferred will have its own limits too. You need to be aware of these limits to ensure that users of your website do not encounter problems when trying to upload files that are too big.

## File\_box settings. ##
When calling the data\_entry\_helper::file\_box function the following settings can be passed in the $options parameter.

**resizeWidth**
If set, then the file will be resized before upload using this as the maximum pixels width.

**resizeHeight**
If set, then the file will be resized before upload using this as the maximum pixels height.

**resizeQuality**
Defines the quality of the resize operation (from 1 to 100). Has no effect unless either resizeWidth or resizeHeight are set.

**maxFileCount**
Maximum number of files to allow upload for. Defaults to 4. Set to false to allow unlimited files.


File\_box also calculates a further setting, **maxUploadSize**. This either takes the value of $maxUploadSize, if it has been set in helper\_config, or 4M. When setting a value in helper config you can use a suffix of G (gigabytes), M (megabytes) or K (kilobytes).

When a file is selected and uploaded to the client website, if either resizeWidth or resizeHeight has been set then resizing is attempted in the browser before upload. There is no indication whether a file has been resized or what its new size is.

If resizing is not enabled then no attempt is made to upload a file larger than maxUploadSize. If resizing is enabled then an attempt to upload the file is made and success will depend upon the settings on the client web server.

## Client web server settings. ##

Three settings in php.ini combine to affect file uploading to the client.

**upload\_max\_filesize**
Maximum allowed size for uploaded files.

**max\_file\_uploads**
Maximum number of files that can be uploaded via a single request

**post\_max\_size**
Maximum size of POST data that PHP will accept.

As from version 0.7.0, an uploaded file is again checked for size against $maxUploadSize since it may be still too large after resizing or if resizing was not possible in the browser. If $maxUploadSize has not been set it defaults to 1M (data\_entry\_helper::check\_upload\_size). Because this default is different from that prior to uploading it is strongly recommended that you set $maxUploadSize in helper\_config.

## Warehouse web server settings. ##

The same three settings in php.ini also exist for the warehouse and are likely to be different from those of the client web server. At the time of writing, the settings for warehouse1.indicia.org.uk were:

upload\_max\_filesize = 2M

max\_file\_uploads is not specified.

post\_max\_size = 8M