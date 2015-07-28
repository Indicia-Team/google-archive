# Introduction #

The Importer provides an import wizard style form for uploading data into Indicia. It provides exactly the same functionality as the import on the Indicia Warehouse but the prebuilt form allows import functionality to be exposed directly on your website.

# Details #

The Importer form has the following settings:

## Website ##

![http://indicia.googlecode.com/svn/wiki/screenshots/prebuilt_form_edit_website.png](http://indicia.googlecode.com/svn/wiki/screenshots/prebuilt_form_edit_website.png)

**Website ID** and **Password** need to be set to authorise the Importer against your website.

## Other IForm Parameters ##

![http://indicia.googlecode.com/svn/wiki/screenshots/prebuilt_form_importer_other_settings.png](http://indicia.googlecode.com/svn/wiki/screenshots/prebuilt_form_importer_other_settings.png)

Like all prebuilt forms running under Drupal, this section includes a setting _View access control_ allowing the form's access to be controlled via Drupal permissions. There is also a generic setting _Redirect to page after successful data entry_ which is not currently used.

**Type of data to import** allows you to select from a list of possible data types. You can also select an option "Use setting in URL" which allows you to specify a type parameter in the URL. For example if your import page is at `http://www.example.com/import`, `http://www.example.com/import?type=location` would allow you to import locations.

**Preset Settings** allows you to enter a list of settings and the preset values for them, meaning that there is less configuration for the user during import. For example you could specify the survey\_id so the user does not need to select the survey, or set sample:entered\_sref\_system=4326 to force uploaded spatial references to use GPS latitude and longitude (otherwise known as WGS84 or EPSG:4326).