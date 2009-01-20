<html>
<head>
<?php
		include 'data_entry_helper.php';
		include 'data_entry_config.php';
?>
<title>Indicia external site data entry test page</title>
<link rel="stylesheet" href="../../../media/css/ui.datepicker.css" type="text/css" media="screen">
<link rel="stylesheet" href="demo.css" type="text/css" media="screen">
<link rel="stylesheet" href="../../../media/css/jquery.autocomplete.css" />

<script type="text/javascript" src="../../../media/js/jquery-1.2.6.js"></script>
<script type="text/javascript" src="../../../media/js/ui.core.js"></script>
<script type="text/javascript" src="../../../media/js/ui.datepicker.js"></script>
<script type="text/javascript" src="../../../media/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="../../../media/js/json2.js"></script>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $config['google_api_key'] ?>"
      type="text/javascript"></script>
      <script type="text/javascript">
$(document).ready(function() {
	$('.date').datepicker({dateFormat : 'yy-mm-dd', constrainInput: false});
});
</script>
</head>
<body>
<h1>Indicia Data entry test</h1>
<p>Note that this page requires the PHP curl extension to send requests to the Indicia server.</p>
<?php
		// PHP to catch and submit the POST data from the form - we need to wrap
		// some things manually in order to get the supermodel in.
		if ($_POST) {
			// Replace the site usage array with a comma sep list
			if (array_key_exists($config['site_usage'], $_POST)) {
				if (is_array($_POST[$config['site_usage']])){
					$_POST[$config['site_usage']] = implode(',',$_POST[$config['site_usage']]);
				}
			}

			// We have occurrence attributes that we have to wrap
			$occAttrs = data_entry_helper::wrap_attributes($_POST, 'occurrence');
			$smpAttrs = data_entry_helper::wrap_attributes($_POST, 'sample');

			$sampleMod = data_entry_helper::wrap($_POST, 'sample');
			$sampleMod['metaFields']['smpAttributes']['value'] = $smpAttrs;

			$occurrenceMod = data_entry_helper::wrap($_POST, 'occurrence');
			$occurrenceMod['superModels'][] = array(
				'fkId' => 'sample_id',
				'model' => $sampleMod
			);
			$occurrenceMod['metaFields']['occAttributes']['value'] = $occAttrs;
			
			// Send the image
			if ($name = data_entry_helper::handle_media('occurrence_image')) {
				// Add occurrence image model
				// TODO Get a caption for the image
				$oiFields = array(
					'path' => $name,
					'caption' => 'An image in need of a caption');
				$oiMod = data_entry_helper::wrap($oiFields, 'occurrence_image');
				$occurrenceMod['subModels'][] = array(
					'fkId' => 'occurrence_id',
					'model' => $oiMod);
			}

			$submission = array('submission' => array('entries' => array(
				array ( 'model' => $occurrenceMod )
			)));
			$response = data_entry_helper::forward_post_to(
				'save', $submission);
			data_entry_helper::dump_errors($response);
		}

?>
<form method="post" enctype="multipart/form-data" >

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
<?php echo data_entry_helper::date_picker('date'); ?>
<br />
<label for="place_search">Search for place on map:</label>
<?php echo data_entry_helper::geoplanet_search(); ?>
<br/>
<label for="entered_sref">Spatial Reference:</label>
<?php echo data_entry_helper::map_picker('entered_sref',
	array('osgb'=>'British National Grid','4326'=>'Latitude and Longitude (WGS84)'),
	null,
	'true'); ?>
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
<?php echo data_entry_helper::image_upload('occurrence_image'); ?>
<fieldset>
<legend>Occurrence attributes</legend>
<label for='<?php echo $config['dafor']; ?>'>Abundance DAFOR</label>
<?php echo data_entry_helper::select($config['dafor'], 'termlists_term', 'term', 'id', $readAuth + array('termlist_id' => $config['dafor_termlist'])); ?>
<br />
	<label for='<?php echo $config['det_date']; ?>'>Determination Date</label>
<input type='text' name='<?php echo $config['det_date']; ?>' id='<?php echo $config['det_date']; ?>'/><br />
</fieldset>
<fieldset>
<legend>Sample attributes</legend>
<label for='<?php echo $config['weather']; ?>'>Weather</label>
<input type='text' name='<?php echo $config['weather']; ?>' id='<?php echo $config['weather']; ?>'/><br />
<label for='<?php echo $config['temperature']; ?>'>Temperature (Celsius)</label>
<input type='text' name='<?php echo $config['temperature']; ?>' id='<?php echo $config['temperature']; ?>'/><br />
<label for='<?php echo $config['surroundings']; ?>'>Surroundings</label>
<?php echo data_entry_helper::radio_group($config['surroundings'], 'termlists_term', 'term', 'id', $readAuth + array('termlist_id' => $config['surroundings_termlist'])); ?> </br>
<label for='<?php echo $config['site_usage']; ?>[]'>Site Usage</label>
<?php echo data_entry_helper::listbox($config['site_usage'].'[]', 'termlists_term', 'term', 4, true, 'id', $readAuth + array('termlist_id' => $config['site_usage_termlist'])); ?>
</fieldset>
<input type="submit" value="Save" />
</form>
</body>
<?php echo data_entry_helper::dump_javascript(); ?>
</html>
