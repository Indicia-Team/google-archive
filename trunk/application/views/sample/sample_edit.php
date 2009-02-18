<?php echo html::script(array(
	'media/js/jquery.ajaxQueue.js',
	'media/js/jquery.bgiframe.min.js',
	'media/js/thickbox-compressd.js',
	'media/js/jquery.autocomplete.js'
), FALSE); ?>
<form class="cmxform"  name='editList' action="<?php echo url::site().'occurrence/save' ?>" method="POST">
<?php print form::hidden('id', html::specialchars($model->id)); ?>
<?php print form::hidden('survey_id', $model->website_id); ?>
<fieldset>
<legend>Sample Details</legend>
<ol>
<li>
<label for='vague_date'>Date:</label>
<?php print form::input('vague_date', $model->vague_date);  ?>
</li>
<li>
<label for='entered_sref'>Spatial Reference:</label>
<?php print form::input('entered_sref', $model->entered_sref);
echo html::error_message($model->getError('entered_sref')); ?>
</li>
<li>
<label for='location_name'>Location Name:</label>
<?php 
print form::input('location_name', $model->location_name);
echo html::error_message($model->getError('confidential'));
?>
</li>
<li>
<label for='external_key'>External Key:</label>
<?php
print form::input('external_key', $model->external_key);
echo html::error_message($model->getError('external_key'));
?>
</li>
<li>
<label for='record_status'>Verified:</label>
<?php
print form::dropdown('record_status', array('I' => 'In Progress', 'C' => 'Completed', 'V' => 'Verified'), $model->record_status);
echo html::error_message($model->getError('record_status'));
?>
</li>
<?php if ($model->record_status == 'V'): ?>
<li>
Verified on <?php echo $model->verified_on; ?> by <?php echo $model->verified_by->username; ?>
</li>
<?php endif; ?>
</ol>
</fieldset>
<?php echo $metadata ?>
<?php echo $comments ?>
<input type="submit" name="submit" value="Submit" />
<input type="submit" name="submit" value="Delete" />