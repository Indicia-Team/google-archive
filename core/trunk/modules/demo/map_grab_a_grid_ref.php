<?php
require 'data_entry_config.php';
$geoplanetApiKey = $config['geoplanet_api_key'];;
$googleApiKey = $config['google_api_key'];
$multimapApiKey = $config['multimap_api_key'];
$geoserverUrl = $config['geoserver_url'];
$featureType = $config['feature_type'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>Map helper test</title>
<link rel="stylesheet" href="demo.css" type="text/css" media="screen">
<script type='text/javascript' src='../../media/js/jquery.js' ></script>
<script type='text/javascript' src='../../media/js/json2.js' ></script>
<script type='text/javascript' src='../../media/js/OpenLayers.js' ></script>
<script type='text/javascript' src='../../media/js/jquery.indiciaMap.js' ></script>
<script type='text/javascript' src='../../media/js/jquery.indiciaMap.js' ></script>
<script type='text/javascript' src='../../media/js/jquery.indiciaMap.grabAGridRef.js' ></script>
<script src="http://maps.google.com/maps?file=api&v=2&key=<?php echo $googleApiKey; ?>" type="text/javascript"></script>
<script type="text/javascript" src="http://developer.multimap.com/API/maps/1.2/<?php echo $multimapApiKey; ?>" ></script>
<script type='text/javascript'>
(function($){
$(document).ready(function()
{
$('#map').indiciaMap({
    presetLayers : ['multimap_landranger', 'google_physical', 'google_satellite'],
    width: "700px",
    height: "700px",
    initial_zoom: 6,
    initial_lat: 7260000
  }).
  grabAGridRef();
});
})(jQuery);
</script>
</head>
<body>
<div id="wrap">
<h1>JavaScript Classes Map</h1>
<p>Zoom the map in to see the OS Landranger layer appear</p>
<div id='map' />
</div>
</body>
</html>
