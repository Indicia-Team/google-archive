<html>
<head>
<title>Indicia external site species checklist test page</title>
<link rel="stylesheet" href="../../../media/css/ui.datepicker.css" type="text/css" media="screen">
<link rel="stylesheet" href="demo.css" type="text/css" media="screen">
<link rel="stylesheet" href="../../../media/css/jquery.autocomplete.css" />

<script type="text/javascript" src="../../../media/js/jquery-1.2.6.js"></script>
<script type="text/javascript" src="../../../media/js/ui.core.js"></script>
<script type="text/javascript" src="../../../media/js/ui.datepicker.js"></script>
<script type="text/javascript" src="../../../media/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="../../../media/js/json2.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('#date').datepicker({constrainInput: false});
});
</script>
</head>
<body>
<h1>Indicia Species Checklist Test</h1>
<?php
include 'data_entry_helper.php';
include 'data_entry_config.php';
// Catch and submit POST data.
if ($_POST){
	// We're mainly submitting to the sample model
	$sampleMod = data_entry_helper::wrap($_POST, 'sample');
	$occurrences = data_entry_helper::wrap_species_checklist($_POST);

	// Add the occurrences in as submodels
	$sampleMod['subModels'] = $occurrences;

	// Wrap submission and submit
	$submission = array('submission' => array('entries' => array(
		array ( 'model' => $sampleMod ))));
	print_r($submission);
	$response = data_entry_helper::forward_post_to(
		'save', $submission);
	data_entry_helper::dump_errors($response);
}

?>
<form method='post'>
<?php
	// This PHP call demonstrates inserting authorisation into the form, for website ID
	// 1 and password 'password'
	echo data_entry_helper::get_auth(1,'password');
	$readAuth = data_entry_helper::get_read_auth(1, 'password');
?>
<input type='hidden' id='website_id' name='website_id' value='1' />
<label for="date">Date:</label>
<input type="text" size="30" value="click here" id="date" name="date"/>
<style type="text/css">.embed + img { position: relative; left: -21px; top: -1px; }</style>
<br />
<label for="entered_sref">Spatial Reference:</label>
<?php echo data_entry_helper::map_picker('entered_sref',
	array('osgb'=>'British National Grid','4326'=>'Latitude and Longitude (WGS84)')); ?>
<br />
<?php echo data_entry_helper::species_checklist(2, array(1,2), $readAuth); ?>
<br />
<input type='submit' value='submit' />
</form>
</body>
</html>
