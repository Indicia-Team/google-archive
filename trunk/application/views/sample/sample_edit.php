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
<label for='date_start'>Start Date:</label>
<?php print form::input('date_start', $model->date_start); 
print form::hidden('taxa_taxon_list_id', $model->taxa_taxon_list_id); 
echo html::error_message($model->getError('taxa_taxon_list_id')); ?>
</li>
<li>
<label for='date'>Date:</label>
<?php print form::input('date');
echo html::error_message($model->taxa_taxon_list->taxon->getError('taxon')); ?>
</li>
<li>
<label for='determiner'>Determiner:</label>
<?php print form::input('determiner', $model->determiner->first_name.' '.$model->determiner->surname);
print form::hidden('determiner_id', $model->determiner_id);
echo html::error_message($model->getError('determiner_id')); ?>
</li>
<li>
<label for='confidential'>Confidential?:</label>
<?php 
print form::checkbox('confidential', 'true', $model->confidential);
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