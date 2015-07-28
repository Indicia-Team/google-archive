# Introduction #

The Spatial service provides functionality for converting spatial references between systems, and into the internal WKT format required for saving them spatially to the database.

It can be accessed at:
<website root>/services/spatial

# Methods #

## sref\_to\_wkt ##

Converts a spatial reference to the internal WKT format used by Indicia to save spatial data or to display a polygon on a map. The service accepts the following GET parameters in the http request:
  1. sref - the sref to convert.
  1. system - the system to convert from (e.g. osgb or 4326).

The service responds with the WKT text.

**Example call**

`http://www.mysite.com/indicia/index.php/services/spatial/sref_to_wkt?sref=SU0101&system=osgb`

**Response**

`POLYGON((-221058.906923845 6587399.33063781,-221058.603470076 6588983.79367751,-219478.225509034 6588983.3371599,-219478.832416466 6587398.87435342,-221058.906923845 6587399.33063781))`

## wkt\_to\_sref ##

Converts a WKT reference to a spatial reference. The service accepts the following GET parameters in the http request:
  1. wkt - the wkt to convert.
  1. system - the system to convert from (e.g. osgb or 4326).
  1. precision - optional. The precision of the returned reference for a grid reference. E.g. pass 6 for a 6 figure grid reference.


**Example call**

`http://localhost/indicia/index.php/services/spatial/wkt_to_sref?system=osgb&precision=4&wkt=POINT(-221100%206587800)`

**Response**

`SU0101`