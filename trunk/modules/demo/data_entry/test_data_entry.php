<html>
<head>
<title>Indicia external site data entry test page</title>
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
})
</script>


</head>
<body>
<h1>Indicia Data entry test</h1>
<p>Note that this page requires the PHP curl extension to send requests to the Indicia server.</p>
<?php
		include 'data_entry_helper.php';
		// PHP to catch and submit the POST data from the form - we need to wrap
		// some things manually in order to get the submodel in.
		if ($_POST) {
			$sampleMod = data_entry_helper::wrap($_POST, 'sample');
			$occurrenceMod = data_entry_helper::wrap($_POST, 'occurrence');
			$occurrenceMod['subModels'][] = array(
				'fkId' => 'sample_id',
				'model' => $sampleMod
			);
			$submission = array('submission' => array('entries' => array(
				array ( 'model' => $occurrenceMod )
			)));
			$response = data_entry_helper::forward_post_to(
				'http://localhost/indicia/index.php/services/data',
				'save', $submission
		);
		data_entry_helper::dump_errors($response);
	}

?>
<form method="post">
<?php
	// This PHP call demonstrates inserting authorisation into the form, for website ID
	// 1 and password 'password'
	echo data_entry_helper::get_auth(1,'password');
?>
<label for="date">Date:</label>
<input type="text" size="30" value="click here" id="date" name="date"/>
<style type="text/css">.embed + img { position: relative; left: -21px; top: -1px; }</style>
<br />
<label for="entered_sref">Spatial Reference:</label>
<input name="entered_sref" /><br />
<input type="hidden" value="osgb" name="entered_sref_system">
<label for="location_name">Locality Description:</label>
<input name="location_name" size="50" /><br />
<label for="survey_id">Survey</label>
<?php echo data_entry_helper::select('survey_id', 'http://localhost/indicia/index.php/services/data', 'survey', 'title', 'id'); ?>
<br />
<fieldset>
<legend>Occurrence Data</legend>
<input type='hidden' id='website_id' name='website_id' value='1' />
<label for='acdeterminer_id'>Determiner</label>
<?php echo data_entry_helper::autocomplete('determiner_id', 'http://localhost/indicia/index.php/services/data', 'person', 'caption', 'id'); ?>
<br />
<label for='actaxa_taxon_list_id'>Taxon</label>
<?php echo data_entry_helper::autocomplete('taxa_taxon_list_id', 'http://localhost/indicia/index.php/services/data', 'taxa_taxon_list', 'taxon', 'id'); ?>
<br/>
<label for='comment'>Comment</label>
<textarea id='comment' name='comment'></textarea>
<br />
</fieldset>
<input type="submit" value="Save" />
</form>
