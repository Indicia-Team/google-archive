<html>
<head>
<?php
	include '../../client_helpers/map_helper.php';
	$myMap = new Map('http://192.171.199.208:8080/geoserver/');
	$myMap->addIndiciaLayer('Samples', 'indicia_samples', 'topp');
?>
<title>Map helper test</title>
</head>
<body>
<?php echo $myMap->render(); ?>
</body>
</html>
