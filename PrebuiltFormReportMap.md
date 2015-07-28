# Using the Report Map prebuilt form #

The Report Map prebuilt form is similar to the Report Grid form and it is recommended that you try out a grid first before trying a map (see [Using the Report Grid rebuilt form](PrebuiltFormReportGrid.md)). It uses a report definition file on the Warehouse to define at least the input parameters required by the report. The actual output is drawn on a map and can be extracted from the report output (if it includes geometry data) or alternatively can be loaded from a layer exposed by [GeoServer](http://geoserver.org). For more information on GeoServer see [Installing GeoServer for Indicia](GeoServerInstall.md).

To use this prebuilt form from Drupal, select Create Content then Indicia Forms from the menu as for any other prebuilt form. Specify a title for the page then a little down the page enter your website ID and password. The Report Map form is available from the Select Form drop-down after choosing the Reporting category first.
Once you have selected the Report Map form click the Load Settings Form button. Drupal will load the settings form for the report grid which contains a number of options, each with help text explaining what they are for. The Report Map shares some of its settings with the Report Grid prebuilt form under the **Report Settings** section. The key ones are described at [Using the Report Grid rebuilt form](PrebuiltFormReportGrid.md) so are not repeated here. The key thing to note though is that the report definition XML file must include mappable data (described on the [Reporting](Reporting.md) Wiki page. If you are not writing your own report files then you can try the report called _map\_occurrences\_for\_survey_ as an example spatially enabled report file. Another point to note is that a report can support a spatial query, by letting you draw a polygon on a map which then acts as one of the input parameters. For example the report could return all occurrences which intersect with the drawn polygon. This functionality is also described on the [Reporting](Reporting.md) Wiki page under the description of parameter datatypes.

Other settings you do need to know about for report based mapping follow the Report Settings section. Under **Initial Map View** you will find settings to define the default centre and zoom level of the map as well as the dimensions of the map in pixels. **Base Map Layers** lets you pick from a list of predefined map layers (make sure you have SetupHelperConfig setup the required API keys] for these). These sections are both fairly standard for Indicia forms. The section called **Report Map Settings** is where you define whether to include a legend or layer picker and whether they should be placed before or after the map. Because Indicia does not control the exact layout of your website template you should use CSS to fine-tune the placement of the boxes.

## Advanced high performance mapping ##

The settings you have seen so far are enough to be able to save the page and try out a report based map. However, because the map will load data from the Indicia data services which is based on sending text rather than images, this depends on a description of each square to draw on the map being returned from the Indicia Warehouse, then the browser has to interpret this text and draw the squares on the map. This is not the most efficient way of mapping large numbers of distribution points so you may find the performance is limiting beyond a few thousand dots on the map, especially on older browsers. Fortunately there is a way around this though it does require a little expertise in using GeoServer and PostGIS SQL as well as the Indicia XML report format. What we need to do is use a WMS (Web Mapping Service) layer that can be drawn on the server and sent to the client as a compressed image which is much more efficient.

Here are the steps required to do this:

You need to create a view in PostgreSQL which outputs the same records as your report would in its unfiltered state. This view will be used by GeoServer to create a layer for mapping. It must include at least the geometry field you are going to map as well as fields which correspond to the parameters that are filterable in the report. For this example, the report SQL is as follows:
```
SELECT su.title AS survey, l.name AS location, s.date_start, s.date_end, s.date_type, 
s.geom, s.entered_sref, s.entered_sref_system, 
t.taxon, o.website_id, o.id, s.recorder_names
   FROM indicia.occurrences o
   JOIN indicia.samples s ON o.sample_id = s.id AND s.deleted = false
   LEFT JOIN indicia.locations l ON s.location_id = l.id
   LEFT JOIN indicia.taxa_taxon_lists ttl ON o.taxa_taxon_list_id = ttl.id
   LEFT JOIN indicia.taxa t ON ttl.taxon_id = t.id
   LEFT JOIN indicia.surveys su ON s.survey_id = su.id AND su.deleted = false
  WHERE o.deleted = false
  AND su.id = #survey_id# 
  AND st_intersects(s.geom, st_geomfromtext('#searchArea#',900913))
```

So we need a view which outputs at least the sample geom and survey\_id. My recommendation would be to put this whole query in a view excluding the survey\_id filter so that both the report and the GeoServer layer can run off the same query. So, the view creation code might look like:

```
CREATE VIEW myschema.basic_occurrence_list AS
SELECT su.title AS survey, l.name AS location, s.date_start, s.date_end, s.date_type, 
s.geom, s.entered_sref, s.entered_sref_system, 
t.taxon, o.website_id, o.id, s.recorder_names, su.id as survey_id
   FROM indicia.occurrences o
   JOIN indicia.samples s ON o.sample_id = s.id AND s.deleted = false
   LEFT JOIN indicia.locations l ON s.location_id = l.id
   LEFT JOIN indicia.taxa_taxon_lists ttl ON o.taxa_taxon_list_id = ttl.id
   LEFT JOIN indicia.taxa t ON ttl.taxon_id = t.id
   LEFT JOIN indicia.surveys su ON s.survey_id = su.id AND su.deleted = false
  WHERE o.deleted = false
```

This means the report SQL can be changed to
```
SELECT survey, location, date_start, date_end, date_type, 
geom, entered_sref. entered_sref_system, 
taxon, website_id, id, recorder_names, survey_id
FROM myschema.basic_occurrence_list
WHERE survey_id=#survey_id# AND st_intersects(s.geom, '#searchArea#')
```

Make sure that the user which the Warehouse is using to run reports on is able to select from this view.

Now, in GeoServer, you must create a layer based on the output of this view. For details of how to do this, see [Installing GeoServer for Indicia](GeoServerInstall.md).

Once you have the layer up and running you can edit your map report page in Drupal and expand the WMS Mapping section of the form parameters. Here are the settings you need to specify:

**GeoServer Layer**: Assuming you created a layer called basic\_occurrence\_list in a workspace called indicia, then enter indicia:basic\_occurrence\_list in this box.

**GeoServer Layer Style**: This can be left blank at least until you get into the custom styling aspect of GeoServer.

**CQL Filter Template**: This is the tricky part. As it stands you have a PostgreSQL view which defines the output of the report excluding the filtering and an XML Report definition which adds the required filtering to the view according to the input parameters. The layer exposed on GeoServer lists all the content of the report but also excludes the filtering aspect - it will return all possible records. We need to instruct Indicia and GeoServer how to interpret the input parameters so as to filter the layer output to output exactly the same records as the filtered report. The OGC WMS specification does not include filtering of the underlying data but fortunately GeoServer has proprietary extensions allowing you to specify a filter in one of several ways. We are going to use a CQL filter (Common Query Language) since it is a relatively simple format. What we need to do here is rewrite the filter part of our report’s SQL statement in CQL. So, in our example the SQL filter is
```
survey_id=#survey_id# 
AND st_intersects(s.geom, st_geomfromtext('#searchArea#',900913))
```

Fortunately CQL is very similar and in fact a bit simpler as it does not require conversion of the WKT geometry text, so this can be rewritten
```
INTERSECTS(geom, #searchArea#) AND survey_id=#survey#
```

Enter this into the CQL Filter Template field on the form configuration and save the report map form. You should find it continues to work though faster and the default style for the report output may be different. If you want to learn more about CQL filters for GeoServer a good starting point is [CQL and ECQL](http://docs.geoserver.org/latest/en/user/tutorials/cql/cql_tutorial.html)

Changing the output style is described on the [Installing GeoServer for Indicia](GeoServerInstall.md) page.