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

	// Method for adding further occurrence attributes
	$('p#addOccAttr').click(function() {
		$('div#occAttr0').clone(true).insertBefore(this)
			.attr('id', 'occAttr'+occAttrIndex);
		$('div#occAttr'+occAttrIndex+' label')
			.attr('for',function(arr){
				return $(this).attr('for').replace('0', occAttrIndex);
			});
		$('div#occAttr'+occAttrIndex+' select')
			.attr('id', function(arr){
				return $(this).attr('id').replace('0', occAttrIndex);
			});
		$('div#occAttr'+occAttrIndex+' input')
			.attr('name', function(arr){
				return $(this).attr('name').replace('0', occAttrIndex);
			});
		occAttrIndex++;
	});

	// We need to do stuff to occurrence attributes
	$('div.occAttr select').change(function(){
		// Get the attribute index
		var index = this.id.charAt(this.id.indexOf("|") - 1);
		// Get the signature string
		var sSig = this.value;
		// Split the signature
		var aSig = sSig.split("|");
		// Update the value to be the first part of the signature (the record id)
		$('div#occAttr'+index+' input.occAttrId').val(aSig[0]);
		// Update the type of field submitted.
		$('div#occAttr'+index+' input.occAttrType').attr('name', function(arr){
			var a = $(this).attr('name').split("|");
			switch (aSig[1]) {
			case 'T':
			a[1] = 'text_value';
			break;
			case 'I':
			a[1] = 'int_value';
			break;
			case 'F':
			a[1] = 'float_value';
			}
			return a.join('|')
		});
			
	});
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
			
			$oap = array();
			foreach ($_POST as $key => $value) {
				if (strpos('occAttr', $key)){
					$a = explode($key, "|");
					$i = substr($a[0], strlen($a[0]));
					if ($i != 0) {
						$oap[$i][$a[1]] = $value;
					}
				}
			}	
			$occAttrs = array();
			foreach ($oap as $oa){
				$occAttrs[] = data_entry_helper::wrap($oa, 'occurrence_attribute');
			}

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
	$readAuth = data_entry_helper::get_read_auth(1, 'password');
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
<?php echo data_entry_helper::select('survey_id', 'http://localhost/indicia/index.php/services/data', 'survey', 'title', 'id', $readAuth); ?>
<br />
<fieldset>
<legend>Occurrence Data</legend>
<input type='hidden' id='website_id' name='website_id' value='1' />
<label for='acdeterminer_id'>Determiner</label>
<?php echo data_entry_helper::autocomplete('determiner_id', 'http://localhost/indicia/index.php/services/data', 'person', 'caption', 'id', $readAuth); ?>
<br />
<label for='actaxa_taxon_list_id'>Taxon</label>
<?php echo data_entry_helper::autocomplete('taxa_taxon_list_id', 'http://localhost/indicia/index.php/services/data', 'taxa_taxon_list', 'taxon', 'id', $readAuth); ?>
<br/>
<label for='comment'>Comment</label>
<textarea id='comment' name='comment'></textarea>
<br />
</fieldset>
<fieldset>
<legend>Occurrence attributes</legend>
<div class='occAttr' id='occAttr1'>
<label for="occAttr1|occurrence_attribute_sig">Attribute</label>
<?php echo data_entry_helper::select('occAttr1|occurrence_attribute_sig', 'http://localhost/indicia/index.php/services/data', 'occurrence_attribute', 'caption', 'signature', $readAuth); ?>
<input type='hidden' value='test' class='occAttrId' name='occAttr1|occurrence_attribute_id'/>
<input type='text' value='' class='occAttrValue' name='occAttr1|text_value' />
</div>
<p id='addOccAttr'>Add Occurrence Attribute</p>
</fieldset>
<input type="submit" value="Save" />
</form>
<div class='occAttr' id='occAttr0'>
<label for="occAttr0|occurrence_attribute_sig">Attribute</label>
<?php echo data_entry_helper::select('occAttr0|occurrence_attribute_sig', 'http://localhost/indicia/index.php/services/data', 'occurrence_attribute', 'caption', 'signature', $readAuth); ?>
<input type='hidden' value='1' class='occAttrId' name='occAttr0|occurrence_attribute_id'/>
<input type='text' value='' class='occAttrValue' name='occAttr0|text_value' />
</div>
