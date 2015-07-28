# Introduction #

This tutorial guides you through the steps required to setup the IForm module for integrating Indicia's prebuilt form library into Drupal.

Before starting, you should have a working installation of both Drupal and Indicia, or access to a working Warehouse. In addition you will need a tool that can decompress a tar.gz tarball file such as [7-Zip](http://www.7-zip.org/download.html).

# Steps #

In the Drupal installation folder, navigate to sites/all. Create a modules folder in this directory unless it is already present.

Download the IForm module (accessible from the featured downloads section at http://code.google.com/p/indicia/). Unzip this archive and copy the resulting  iform folder into this modules folder so your folder structure now looks like:

![http://indicia.googlecode.com/svn/wiki/drupal_folder_layout.png](http://indicia.googlecode.com/svn/wiki/drupal_folder_layout.png)

Download the following modules:
  * Jquery\_update version 6.x-2.x-dev which supports jQuery 1.3, from http://drupal.org/project/jquery_update. Note the currently recommended release 6.x-1.1 will not work because it is for jQuery 1.2.
  * Jquery\_ui version 6.x-1.3 from http://drupal.org/project/jquery_ui.

To extract the files from the archives you have downloaded, right click the downloaded tar.gz file then select 7-Zip->Open archive. Now, in 7-zip itself, right click the .tar file and select Open Inside. Now you can drag the folder you see into the sites/all/modules alongside the iform module.

The jquery\_ui plugin also needs you to download the latest stable version of the jQuery UI. The downloads are on this page: http://code.google.com/p/jquery-ui/downloads/list. I chose 1.7.3 since it is compatible with the version of jQuery we are using. Unzip this file, then copy the folder inside it into your sites/all/modules/jquery\_ui folder and rename it to jquery.ui. So, you should have the following path setup which is where the jQuery UI will be loaded from:

| drupal folder\sites\all\modules\jquery\_ui\jquery.ui\ui |
|:--------------------------------------------------------|

It's worth double checking this path is correct right now before you go any further. Next you need to install the modules into Drupal. In the Drupal front-end, select Administer->Site building->Modules. Scroll down the list of modules and tick the Indicia forms module, jQuery UI and jQuery Update modules then click on the Save Configuration button at the bottom of the page.

You also need to configure the Indicia "client helper". In your sites/all/modules/iform/client\_helpers folder, create a file called helper\_config.php and paste the following content into it, replacing the url in the first entry with that of the Indicia Warehouse you are using, including the http:// prefix even for localhost connections and the trailing slash:
```
<?php
class helper_config {
  static $base_url='http://www.mysite.com/indicia/';
  static $upload_path = './sites/all/modules/iform/upload/';
  static $geoserver_url = '';
  static $geoplanet_api_key='';
  static $google_search_api_key='';
  static $google_api_key='';
  static $multimap_api_key='';
  static $flickr_api_key='';
  static $flickr_api_secret='';
}
?>
```
There is more information on the configuration settings in this file at http://code.google.com/p/indicia/wiki/SetupHelperConfig, but just the $base\_url is enough for setting up the form in this tutorial. You can refer back to this link later, for example if you need to enable the place search facilities or to use Google maps layers.

You can now run a quick diagnostic check on the IForm module setup. To do this, select Administer > Site configuration > IForm Diagnostics from the menu in Drupal. You should see something like
  * Success: PHP version is 5.2.10.
  * Success: The cUrl PHP library is installed.
  * Success: Indicia Warehouse URL responded to a POST request.
  * Warning: The following configuration entries are not specified in helper\_config.php : $geoserver\_url, $geoplanet\_api\_key, $google\_search\_api\_key, $google\_api\_key, $multimap\_api\_key, $flickr\_api\_key, $flickr\_api\_secret. This means the respective areas of functionality will not be available.
  * Success: Cache directory is present and writeable.

Depending on whether you set up all the API keys or not you may get some warnings, that is OK as long as they are not API keys for parts of the system you plan to use.

To create forms, you first need to set up at least a website, survey and species list in the Indicia Warehouse. Then, use the Create content link in Drupal and select Indicia Forms as the type of content.

![http://indicia.googlecode.com/svn/wiki/drupal_select_content_type.png](http://indicia.googlecode.com/svn/wiki/drupal_select_content_type.png)

You will be asked for a page title and the usual Drupal page configuration, but also to select an Indicia Form. Start by selecting the Basic 1 - species, date, place, survey and comment form. When you select this, the Drupal page will load the parameters you need to fill in before using the form, which vary depending on the form you selected. In this case, specify the following parameters:

**Species Control Type** - what type of data entry control to use for picking a species.

**Website ID** - the ID of your website registration in Indicia.

**Website Password** - the Password of your website registration in Indicia.

**Species List ID** - the ID of the species list on the Warehouse which the users are allowed pick from.

**Preferred Species Only** - whether to limit the selection of species to preferred names.

**Use Tabbed Interface** - tick this box to split the entry form up into tabs.

**Redirect to page after successful data entry** - leave blank for now, but can be used to specify a page to go to after successfully entering a record.

![http://indicia.googlecode.com/svn/wiki/drupal_form_parameters.png](http://indicia.googlecode.com/svn/wiki/drupal_form_parameters.png)

That's it, save this and your Drupal page is ready to go.