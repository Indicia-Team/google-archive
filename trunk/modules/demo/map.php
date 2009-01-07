<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>OpenLayers Example</title>
    <link rel="stylesheet" href="../theme/default/style.css" type="text/css" />
    <link rel="stylesheet" href="style.css" type="text/css" />
    <script src="../../media/js/OpenLayers.js"></script>
    <script src='http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'></script>
    <script type="text/javascript">
        // making this a global variable so that it is accessible for
        // debugging/inspecting in Firebug
        var map = null;
        var format = 'image/png';


        function init(){

            map = new OpenLayers.Map('map');

            var ol_wms = new OpenLayers.Layer.WMS(
                "OpenLayers WMS",
                "http://labs.metacarta.com/wms/vmap0",
                {layers: 'basic'}
            );

            var jpl_wms = new OpenLayers.Layer.WMS(
                "NASA Global Mosaic",
                "http://t1.hypercube.telascience.org/cgi-bin/landsat7",
                {layers: "landsat7"}
            );

            var velayer = new OpenLayers.Layer.VirtualEarth(
            	"Virtual Earth",
            	{'type': VEMapStyle.Aerial}
            );


			// Samples layer
            var samples = new OpenLayers.Layer.WMS(
                "Samples from Indicia", "http://192.171.199.208:8080/geoserver/wms",
                {
                    layers: 'opal:indicia_samples',
                    srs: 'EPSG:4326',
                    transparent: true,
                    format: format
                },
                {singleTile: true, ratio: 1,isBaseLayer:false, opacity:0.5}
            );

            map.addLayers([velayer, jpl_wms, ol_wms, samples]);
            map.addControl(new OpenLayers.Control.LayerSwitcher());
            map.setCenter(new OpenLayers.LonLat(-2,50.77));
            map.zoomTo(11);
        }
    </script>
  </head>

  <body onload="init()">
    <h1 id="title">Indicia Spatial Data Example</h1>
    <div id="tags"></div>
    <p id="shortdesc">
        This page demonstrates a simple map built using the OpenLayers JavaScript library and GeoServer. The data shown is contained in Indicia's
        samples table as PostGIS geometry data. Geoserver provides a link to this data in the form of an OGC WMS web service.
    </p>
    <div id="map" class="smallmap" style="width: 850px; height: 600px;"></div>
    <div id="docs"></div>
  </body>

</html>

