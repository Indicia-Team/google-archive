<?php echo html::script(array(
	'media/js/jquery.ajaxQueue.js',
	'media/js/jquery.bgiframe.min.js',
	'media/js/thickbox-compressd.js',
	'media/js/jquery.autocomplete.js',
	'media/js/OpenLayers.js',
	'media/js/spatial-ref.js',
	'http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'
), FALSE); ?>
<script type='text/javascript'>	
init_map('<?php echo url::base(); ?>', <?php if ($model->id) echo "'$model->geom'"; else echo 'null'; ?>,
		'entered_sref', 'entered_geom', true);
</script>
<form class="cmxform"  name='editList' action="<?php echo url::site().'occurrence/save' ?>" method="POST">
<?php print form::hidden('id', html::specialchars($model->id)); ?>
<?php print form::hidden('survey_id', $model->survey_id); ?>
<fieldset>
<legend>Sample Details</legend>
<ol>
<li>
<label for='vague_date'>Date:</label>
<?php print form::input('vague_date', $model->vague_date);  ?>
</li>
<li>
<label for="entered_sref">Spatial Ref:</label>
<input id="entered_sref" class="narrow" name="entered_sref"
	value="<?php echo html::specialchars($model->entered_sref); ?>"
	onblur="exit_sref();"
	onclick="enter_sref();"/>
<select class="narrow" id="entered_sref_system" name="entered_sref_system">
<?php foreach (kohana::config('sref_notations.sref_notations') as $notation=>$caption) {
	if ($model->entered_sref_system==$notation)
		$selected=' selected="selected"';
	else
		$selected = '';
	echo "<option value=\"$notation\"$selected>$caption</option>";}
?>
</select>
<input type="hidden" name="entered_geom" id="entered_geom" />
<?php echo html::error_message($model->getError('entered_sref')); ?>
<?php echo html::error_message($model->getError('entered_sref_system')); ?>
<p class="instruct">Zoom the map in by double-clicking then single click on the location's centre to set the
spatial reference. The more you zoom in, the more accurate the reference will be.</p>
<div id="map" class="smallmap" style="width: 600px; height: 350px;"></div>
</li>
<li>
<label for='location_name'>Location Name:</label>
<?php 
print form::input('location_name', $model->location_name);
echo html::error_message($model->getError('confidential'));
?>
</li>
<li>
<label for='sample_method'>Sample Method:</label>
<?php
print form::input('external_key', $model->external_key);
echo html::error_message($model->getError('external_key'));
?>
</li>
</ol>
</fieldset>
<?php echo $metadata ?>
<?php echo $occurrences ?>
<input type="submit" name="submit" value="Submit" />
<input type="submit" name="submit" value="Delete" />