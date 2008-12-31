<?php

 // List of supported spatial reference notations, each of which is defined in a module
 // of the same name.
$config['sref_notations'] = array
(
	'osgb'=>'British National Grid',
	'4326'=>'Latitude and Longitude (WGS84)',
);

// Set the internally stored geoms to use spherical mercator projection
$config['internal_srid']=900913;

?>
