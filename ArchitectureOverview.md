# Architecture Overview #

![http://indicia.googlecode.com/svn/wiki/architecture_overview.png](http://indicia.googlecode.com/svn/wiki/architecture_overview.png)

The main core of Indicia consists of a webserver running a PostgreSQL database with PostGIS support enabled. The Webserver code runs on PHP version 5 and is primarily based on the Kohana framework.

Each Indicia core can support multiple online recording websites. The websites themselves can be hosted anywhere and will typically use PHP and JavaScript to communicate with the Indicia Core via web services and AJAX.

For mapping support, the typical approach is to install GeoServer to provide OGC compliant web services which expose the Indicia core's data. These webservices can then be integrated with mapping libraries such as OpenLayers or direct into GIS applications.