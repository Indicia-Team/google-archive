# Georeference Lookup Drivers #

The data\_entry\_helper::georeference\_lookup control allows place names to be entered and resolved to provide a point on a map. To do this, the place name is sent to an external web-service which is asked to identify possible matches for the place name. If a single match is found, then the map is panned and zoomed to show that match. If multiple possible matches are found, then the user is presented a list and asked to select.

Since there are many web-services that can provide georeferencing services, Indicia uses a simple driver architecture to allow the developer using the control to select the one that best fits their needs. The following notes explain the key functional aspects of a georeference lookup driver.

  * The driver is a JavaScript class in a file in the folder media/js/drivers/georeference, with the file name matching the name of the driver.
  * The class is called Georeferencer and exposes at least a single function called georeference which takes a searchtext parameter.
  * The class constructor takes a mapdiv parameter (the IndiciaMapPanel div instance) which gives access to the mapdiv.georefSettings and mapdiv.map.
  * The class constructor also takes a callback parameter, which is a method that must be called to pass the results of georeferencing back to Indicia.
  * When calling the callback, the driver must pass back the mapdiv as the first parameter, followed by an array of results. Each array entry contains the following structure:
```
{
  name : place.label,
  display : place.listlabel,
  epsg: nnnn, // the number of the EPSG code of the projection of the place data
  centroid: {
    x: n.nn, // the x or longitude coordiate, in the projection of epsg, of the place centre
    y: n.nn, // the y or latitude coordiate, in the projection of epsg, of the place centre
  },
  boundingBox: {
    southWest: {
      x: n.nn, // the x or longitude coordiate, in the projection of epsg, of the south west corner of the place 
      y: n.nn // the y or latitude coordiate, in the projection of epsg, of the south west corner of the place
    },
    northEast: {
      x: n.nn, // the x or longitude coordiate, in the projection of epsg, of the north east corner of the place 
      y: n.nn // the y or latitude coordiate, in the projection of epsg, of the south west north east corner of the place
    }
  }
}
```
  * In some cases, the web-service used will be accessed via an AJAX request to a URL. Note that you can use the jQuery AJAX methods to simplify this, e.g. $.getJSON. However, if this requires cross-domain AJAX and the web-service does not support JSONP, then it is possible to send the request to a proxy. The path to the proxy is provided to the driver in mapdiv.georefOpts.proxy. The proxy's first parameter should be url which is set to the url of the webservice being called. For an example of this, see the geoportal\_lu driver.
  * To select the driver, the georeference\_lookup control must be called within an option 'driver' => 'driver\_name', where driver\_name matches the JavaScript class file name without the .js suffix.
  * mapdiv.georefOpts contains any options passed to the georeference\_lookup control which start with georef. Although a driver is free to choose its own settings, the following settings should be selected for consistency wherever possible:
    * georefLang - the language code for georeferencing using a format which depends on the driver
    * georefPreferredArea - the area in which to focus the georeferencing results
    * georefCountry - the country in which to focus the georeferencing results
  * mapdiv.georefOpts also contains any settings loaded from the helper\_config.php file where the setting name starts with the name of the driver. For example, the geoplanet\_api\_key is passed through to the geoplanet driver.
  * If the driver needs any special resources included (typically a JavaScript link in addition to the driver js file itself), then the resource must be declared by editing the `data_entry_helper::_RESOURCES` method and adding the resource there. The resource entry must be named georeference