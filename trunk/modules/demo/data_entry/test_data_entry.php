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
	var occAttrIndex = 2;
	$('#date').datepicker({constrainInput: false});
});
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
			// We have occurrence attributes that we have to wrap
			$occAttrs = data_entry_helper::wrap_attributes($_POST, 'occurrence');
			$smpAttrs = data_entry_helper::wrap_attributes($_POST, 'sample');

			$sampleMod = data_entry_helper::wrap($_POST, 'sample');
			$occurrenceMod = data_entry_helper::wrap($_POST, 'occurrence');
			$occurrenceMod['subModels'][] = array(
				'fkId' => 'sample_id',
				'model' => $sampleMod
			);
			$occurrenceMod['metaFields']['occAttributes']['value'] = $occAttrs;
			$submission = array('submission' => array('entries' => array(
				array ( 'model' => $occurrenceMod )
			)));
			$response = data_entry_helper::forward_post_to(
				'save', $submission);
			data_entry_helper::dump_errors($response);
		}

?>
<form method="post">
<?php
	// This PHP call demonstrates inserting authorisation into the form, for website ID
	// 1 and password 'password'
	echo data_entry_helper::get_auth(1,'password');
	$readAuth = data_entry_helper::get_read_auth(1, 'password');
?>
<input type='hidden' id='website_id' name='website_id' value='1' />
<label for='actaxa_taxon_list_id'>Taxon</label>
<?php echo data_entry_helper::autocomplete('taxa_taxon_list_id', 'taxa_taxon_list', 'taxon', 'id', $readAuth); ?>
<br/>
<label for="date">Date:</label>
<input type="text" size="30" value="click here" id="date" name="date"/>
<style type="text/css">.embed + img { position: relative; left: -21px; top: -1px; }</style>
<br />
<label for="entered_sref">Spatial Reference:</label>
<?php echo data_entry_helper::map_picker('entered_sref',
	array('osgb'=>'British National Grid','4326'=>'Latitude and Longitude (WGS84)')); ?>
<br />
<label for="location_name">Locality Description:</label>
<input name="location_name" size="50" /><br />
<label for="survey_id">Survey</label>
<?php echo data_entry_helper::select('survey_id', 'survey', 'title', 'id', $readAuth); ?>
<br />
<label for='acdeterminer_id'>Determiner</label>
<?php echo data_entry_helper::autocomplete('determiner_id', 'person', 'caption', 'id', $readAuth); ?>
<br />
<label for='comment'>Comment</label>
<textarea id='comment' name='comment'></textarea>
<br />
<fieldset>
<legend>Occurrence attributes</legend>
<label for='occAttr|3'>Abundance DAFOR</label>
<?php echo data_entry_helper::select('occAttr|3', 'termlists_term', 'term', 'id', $readAuth + array('termlist_id' => 1)); ?>
<br />
<label for='occAttr|1'>Determination Date</label>
<input type='text' name='occAttr|1' /><br />
</fieldset>
<fieldset>
<legend>Sample attributes</legend>
<label for='smpAttr|1'>Weather</label>
<input type='text' name='smpAttr|1' /><br />
<label for='smpAttr|2'>Temperature (Celsius)</label>
<input type='text' name='smpAttr|2' /><br />
<label for='smpAttr|3'>Surroundings</label>
<?php echo data_entry_helper::radio_group('smpAttr|3', 'termlists_term', 'term', 'id', $readAuth + array('termlist_id' => 2)); ?> </br>
<label for='smpAttr|4'>Site Usage</label>
<?php echo data_entry_helper::listbox('smpAttr|4', 'termlists_term', 'term', 4, true, 'id', $readAuth + array('termlist_id' => 3)); ?>
</fieldset>
<input type="submit" value="Save" />
</form>
