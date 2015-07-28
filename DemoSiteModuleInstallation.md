# Introduction #

Indicia comes with a number of demonstration pages, which showcase some of the features of a site module and give ideas as to how basic data entry/accession features may be built with the helper tools provided. This guide shows how to install and configure the demonstration pages following a fresh install.

# Details #

## Configuration Files ##

Site-specific details within the demo pages are stored as configuration files, to avoid requiring code changes to be made to adapt to differing installations. There are two config files that concern the demo pages: these are client\_helpers/helper\_config.php, and modules/demo/data\_entry\_config.php.

### Distribution ###

To avoid file clashes when working within the RCS, these are distributed as helper\_config.php.example and data\_entry\_config.php.example, with some default settings preconfigured. As such, one must first copy/move these files to the above named ones.

### helper\_config.php ###

This file is required both for the demo pages and for any other pages built using the helper tools in data\_entry\_helper.php. See [Setting up the Helper Configuration](SetupHelperConfig.md) for more information on how to edit this file so that it works with the demo pages.

### data\_entry\_config.php ###

This file is used for settings specific to the test\_data\_entry and species\_checklist example pages. It is not strictly necessary that any pages built using the helper tools use this file, but it is probably advisable to use something similar to increase code flexibility for anything that might be redistributed. The settings here are mostly concerned with tuning the data entry pages to your specific database installation. At present, they look like:

```
	$config['dafor']='occAttr:2';
	$config['dafor_termlist']=1;
	$config['det_date']='occAttr:1';
	$config['weather']='smpAttr:1';
	$config['temperature']='smpAttr:2';
	$config['surroundings']='smpAttr:3';
	$config['surroundings_termlist']=2;
	$config['site_usage']='smpAttr:4';
	$config['site_usage_termlist']=3;
	$config['google_api_key']='...';
	$config['species_checklist_taxon_list']='2';
	$config['species_checklist_occ_attributes'] = array(1,2);
```

(As is evident, for some reason two completely different methods of storing the config settings are used. I can't remember if there's a reason why.)

  * `dafor`, `det_date`, `weather`, `temperature`, `surroundings` and `site_usage` are all attributes - that is, user-definable fields for either the occurrence (dafor and det\_date) or the sample (weather, temperature, surroundings and site\_usage). The test\_data\_entry page uses these values in place of the id and name attributes for various controls. The first part of the value (e.g. 'smpAttr') identifies that this is a sample/occurrence attribute. The second is the id of the corresponding record within the sample/occurrence\_attributes table. This ensures that the value is stored in the correct place upon submission. These numbers should be correct if you installed Indicia from scratch and have not removed entries directly from the database, but if you need to confirm them you should log into the Indicia Core and use the Show/Hide Metadata link for each Occurrence Attribute and Sample Attribute to confirm them. Note that whilst the key here may be anything, the value must correspond to the specified format `(smp|occ)Attr:[0-9]+` to be correctly interpreted by the server.
  * `dafor_termlist`, `surroundings_termlist`, `site_usage_termlist` all point to the record id within the termlists table, for attributes of type 'T' where values must belong to a controlled termlist.
  * `species_checklist_taxon_list` is the ID of the species list to use for the Species Checklist demonstration page. You can check the value to enter here by loading the record for a Species List you wish to use in the Indicia Core and clicking the Show / Hide Metadata link.
  * `species_checklist_occ_attributes` is an array of IDs of attributes to include as columns in the Species Checklist demonstration page grid. As before, you can check these by loading the attributes you want to appear in the grid in the Core Module, then clicking the Show / Hide Metadata link.

Note that if you have installed a fresh copy of Indicia with a new database the above fields should all have the correct values set.

  * `google_api_key` are as helper\_config.
  * `species_checklist_taxon_list` specifies the id of the taxon\_list to use for the species checklist demo.
  * `species_checklist_occ_attributes` specifies an array of occurrence attributes to show within the species checklist.

## Geoserver ##

If you plan to expose geographical data from your own instance of the Indicia Core, you will need to [install and configure GeoServer](GeoServerInstall.md).

## Openlayers Proxying ##

While most openlayers functionality should work out of the box, certain features, such as the WFS calls used to access data in the Indicia Core, use Ajax calls to different sites. Security restrictions do not in general allow this, and so in order for this to work the recommended method is to configure a limited proxy host on your server. This way, these calls are sent via your own machine, bypassing the security restrictions.

Two methods of proxying are suggested. The first will likely prove more difficult for a user running on a test machine not already configured as a web server, but should be easier for a shared host with cgi-bin configured. The second may not be possible on a shared host, but is probably easier for an example installation on a local machine. Pick whichever suits you.

### Using a proxy script ###

Installing a proxy script is quite simple. First, check if you have a cgi-bin directory under your http root. If not, you will need to create one with read and execute permissions for the http user. You will also need to add a ScriptAlias directive in your httpd.conf pointing /cgi-bin/ to the cgi-bin directory:

```
ScriptAlias /cgi-bin/ "/srv/http/cgi-bin"
```

Then, save [this script](http://indicia.googlecode.com/files/proxy.cgi) into this directory.

If you're installing on a shared host, this should all be configured, and you should simply be able to add the script into your cgi-bin. Check with your hosting provider if you're not sure where this is.

You can test if this is working by navigating to http://localhost/cgi-bin/proxy.cgi. If all works correctly, you should see the openlayers homepage. If not, something has gone wrong. If you're happy fixing this yourself, do so. If not, it seems to make more sense at this point to point you at the forums (http://forums.nbn.org.uk/viewforum.php?id=19) rather than trying to provide an exhaustive debugging facility.

(Note that the above script uses and requires ruby. If instead you prefer python, there's a python version at http://trac.openlayers.org/browser/trunk/openlayers/examples/proxy.cgi, and there appears to be a version in C# at http://n2.nabble.com/C--version-of-Proxy-for-OpenLayers:-Proxy.ashx-url%3D-td2307110.html (This script hasn't been tested). Also, I have no idea if this should work on Windows.)

#### Allowed Hosts ####

In order to provide some security to the proxy mechanism (and avoid it being used by others to route their traffic) the allowedHosts variable in the proxy.cgi script (ruby or python versions) declares a list of sites that this proxy will proxy for. By default, this is configured with openlayers.org, localhost and the indicia demo server. If you have another server you need to access, you must add this to the allowedHosts variable.

#### Openlayers ProxyHost ####

Using the map\_helper, there are two variables to consider to set up proxying. `$proxy` should point to the address you want to reroute cross-site calls to: in the case of the above script and install, this should be `http://localhost/cgi-bin/proxy.cgi?url=`.

The proxy behaviour in Openlayers will default to on, and can be set using `$useProxy`, e.g. `$myMap->useProxy = false` will turn this behaviour off.

### Using mod\_proxy ###

Another approach involves using Apache's mod\_proxy and configuring specific forwarding for the sites you wish to access (usually, this will be only the geoserver install of the indicia server.) This way is easier to configure, but requires that you have permission to edit your apache configuration and restart the server, so you may be unable to do this on shared hosts.

Edit your httpd.conf to uncomment the following lines:

```
LoadModule proxy_module modules/mod_proxy.so
LoadModule proxy_ajp_module modules/mod_proxy_ajp.so
LoadModule proxy_balancer_module modules/mod_proxy_balancer.so
LoadModule proxy_connect_module modules/mod_proxy_connect.so
LoadModule proxy_ftp_module modules/mod_proxy_ftp.so
LoadModule proxy_http_module modules/mod_proxy_http.so
```

and add the following to the end of the file:

```
ProxyPass /geoserver/wms http://localhost:8080/geoserver/wms
ProxyPassReverse /geoserver/wms http://localhost:8080/geoserver/wms
ProxyPass /geoserver/wfs http://localhost:8080/geoserver/wfs
ProxyPassReverse /geoserver/wfs http://localhost:8080/geoserver/wfs
```

where 'localhost:8080/geoserver' should be the url of the geoserver install you're using (if you're running it directly on your own machine, the above should be correct).

Within the map page, set your `$indiciaCore` location to http://localhost/geoserver, and disable proxying by setting `$useProxy = false`. Apache should proxy the geoserver requests transparently.