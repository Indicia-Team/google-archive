<html>
<head>
<?php
	include '../../client_helpers/map_helper.php';
	$myMap = new Map('http://localhost:8080/geoserver/');
	$myMap->addIndiciaWFSLayer('Samples', 'topp:states');
?>
<title>Map helper test</title>
</head>
<body>
<?php echo $myMap->render(); ?>
</body>
</html>
