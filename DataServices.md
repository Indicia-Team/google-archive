# Introduction #

Indicia's Data Services provide a programmatic interface to information held in the Indicia core database. The Data Services are a RESTful web service, with each entity represented by a URL.

All calls to the web services require a read nonce and auth\_token to be attached as described under [Digest Authentication](DigestAuthentication.md).

## Constructing the service URL ##

The Data Services are accessed via the URL of the site root + /index.php/services/data/ + the name of the required data entity, which is the singular version of the required table name, for example `http://www.mysite.com/index.php/services/data/termlist`

## Retrieving a single item of data ##

To retrieve a single item of data with a known id, append a forward slash then the item's id to the URL, for example `http://www.mysite.com/index.php/services/data/termlist/3` will retrieve the termlist with id 3.

## Retrieving a list of data ##

Retrieving a list of data can be achieved by ommitting the id from the URL. In this case, a number of parameters are available to control the output as follows. Each parameter can be provided as either a GET or a POST parameter. Typically you would use GET as this is a request for data rather than a post of data, but POST can be used in cases where this might result in an excessively long URL for example.

|_fieldname_|If a parameter is provided which matches a field in the output data, then the value provided is used to filter on that field. When filtering against a text attribute, use an asterisk character to represent a wildcard if an inexact match is required. Use NULL to filter for null values.|
|:----------|:--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
|orderby    |Allows the field that the results are to be sorted by to be specified. From version 0.8.2, this can be a comma separated list of field names to sort.                                                                                                                                        |
|sortdir    |Specifies the sort direction. Options are ASC or DESC. From version 0.8.2, this can be a comma separated list of ASC or DESC entries with the same number of entries as in orderby to define the order of each field. If there are less entries then the sort order for unspecified fields will be ASC.|
|limit      |Limits the returned results to _n_ items.                                                                                                                                                                                                                                                    |
|offset     |Starts the returned results offsetted by _n_ records.                                                                                                                                                                                                                                        |
|wantRecords|Defaults to 1. If 1, then the returned data includes the records.                                                                                                                                                                                                                            |
|wantColumns|Defaults to 0. If 1, then the returned data includes the column definitions.                                                                                                                                                                                                                 |
|wantCount  |Defaults to 0. If 1, then the returned data a count of the records in the results set.                                                                                                                                                                                                       |
|auth\_token|Used to provide the read authorisation token.                                                                                                                                                                                                                                                |
|nonce      |Used to provide the read authorisation nonce.                                                                                                                                                                                                                                                |
|query      |For advanced query building letting you pass a JSON object describing filters to apply to the query. Further details are given below                                                                                                                                                         |

If both wantColumns and wantRecords are 1, then the returned object is divided into a records and a columns part at the top level.

An example illustrating a service call is `http://www.mysite.com/index.php/services/data/termlists_term?termlist=sample&term=quad&orderby=term&sortdir=desc&limit=10&offset=10&xsl=default.xsl&auth_token=x&nonce=y`. This returns an XML document formatted using default.xsl, which lists all termlist terms from termlists that contain sample in their title, where the term contains quad. The results start at the 10th item in the list, are limited to a maximum of 10 entries, and are ordered by term descending.

## Using the query parameter ##

Where a simple field filter is not sufficient, you can use the query parameter to build more advanced queries against the data. This is achieved by passing a JSON object in the query parameter. The top level of the object is an associative array of the filter type name, each containing a set of the parameters required for the filter type. So, for a simple example of a WHERE clause definition, you might pass the following using PHP which generates the SQL given:
```
'query' => urlencode(json_encode(array('where'=>array('survey_id', 5))))
WHERE survey_id=5
```
In the case where you need 2 where clauses, you can do
```
'query' => urlencode(json_encode(array(
    'where'=>array(array('survey_id'=>5, 'entered_sref_system'=>'OSGB'))
)))
WHERE survey_id=5 AND entered_sref_system='OSGB'
```

The supported filter type names are as follows:

|where|Takes either 2 parameters, the fieldname and the value, or a single string parameter which is the join SQL, or an associative array of fieldnames and values. Multiple conditions are joined using an AND. If you need to support complex where syntax (e.g. wrapping parenthesis around 2 OR'ed statements) then this can be achieved by supplying a single string parameter containing the full SQL required for the WHERE clause.|
|:----|:-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
|orwhere|Identical to the where filter type, but conditions are joined using an OR.                                                                                                                                                                                                                                                                                                                                                          |
|in   |Takes 2 parameters, a fieldname and an array of values to generate an IN (...) SQL clause.                                                                                                                                                                                                                                                                                                                                          |
|notin|Identical to in but generates a NOT IN clause.                                                                                                                                                                                                                                                                                                                                                                                      |
|like |Takes 2 parameters, a fieldname and a value to generate a LIKE SQL statement.                                                                                                                                                                                                                                                                                                                                                       |
|orlike|Identical to the like filter type, but conditions are joined using an OR.                                                                                                                                                                                                                                                                                                                                                           |

More may be added in future.

## Output Attributes ##

Calls to the Data Services return a minimal amount of information for the entity by default, typically just the entity's id and caption field. The fields returned are those defined in the set of datasbase views called list_**where** is the table name. To return more details, specify a GET parameter "view=gv" or "view=detail", which returns either the columns in the main grid for the entity, or a detailed view of the entity respectively. Since additional query joins are required using these views will reduce performance._

## Output Mode ##

Calls to the URLs normally return a formatted XML document describing the results. Provide a GET parameter called mode with one of the following values to override the output format:
  * json - for JavaScript Object Notation (JSON) format
  * csv - for Comma Separated Values file format
  * tsv - for Tab Separated Values file format. (version 0.9 onwards)
  * nbn - for a tab delimited file compatible with the NBN Exchange format.
  * kml - for an XML based Keyhole Markup Language file compatible with geo mapping tools, e.g. GoogleEarth. (Version 0.9 onwards)
  * gpx - for an XML based GPS Exchange Format file. (Version 0.9 onwards)
Other formats can be supported by adding views that generate the formatted output to the modules/indicia\_svc\_data/views/services/data/_entity name_ folder.

### KML (version 0.9 onwards) ###

Each record produced will generate a Placemark in the KML file.

The record will be scanned for any geometry fields: they are identified as being named "geom" or ending with "`_`geom", and must contain the geometry in Well Known Text (WKT) format. All such geometries are used to generate the Placemark geometry, even if there is more than one in the record or if they are complex.

In order to meet the KML specification, the geometry field(s) supplied by the report must be long/lat decimal degrees (WGS84, EPSG:4326). Indicia has to assume that the geometry is provided in this format as it is not currently possible to identify the coordinate system automatically in order to do a conversion. This requirement precludes the use of direct table data download as KML as the views used for these output the coordinates using the Coordinate system used inside Indicia (usually EPSG:3857).

The name of each Placemark is created by applying the following rules:
  * If there is a "name" field in the record, this is used, otherwise
  * If there is a "location\_name" field in the record, this is used, otherwise
  * If there is an "id" field in the record, this "records.`<`id`>`" is used, otherwise
  * "records.`<`n`>`" is used

Any field called "date" will be used to TimeStamp the record.

All fields apart from the geometry, name or location\_name (when used to generated the Placemark name) are stored in the ExtendedData for the Placemark.

### GPX (version 0.9 onwards) ###

Each record produced will generate one or more waypoints or routes, depending on the complexity of the geometry (or geometries) associated with the record. Tracks are not generated.

The record will be scanned for any geometry fields: they are identified as being named "geom" or ending with "`_`geom", and must contain the geometry in Well Known Text (WKT) format. All such geometries are used to generate waypoints or routes, even if there is more than one in the record or if they are complex. All points are represented as a waypoint, all lines as routes, and the outside perimeter of any polygon is also represented as a route.

GPX has the same restrictions as KML with respect to the generation of geometry fields, i.e. they must be WGS84 (EPSG:4326). Hence the use of direct table data download as GPX is also precluded.

The name of each waypoint or route is created by applying the same rules as for KML.

All fields apart from the geometries, name or location\_name (when used to generated the name) are stored in the description for the waypoint or route.

### JSONP ###

For cross-site retrieval using JSONP, an optional callback method can be specified by providing ?callback=_methodname_ in the GET string.

## Attaching XSL to XML output ##

By passing a GET parameter called xsl to the URL, an XSL transformation can be linked to by the returned XML document. Either a fully qualified path to the XSL document is required, or if just a file name is given then the XSL document must exist in the folder \media\services\stylesheets within the website root. An example file called default.xsl is provided. For example `http://www.mysite.com/index.php/services/data/termlist?xsl=default.xsl` retrieves the XML document listing all termlists into the browser and formats it on the client using the XSL file to appear as an HTML table.