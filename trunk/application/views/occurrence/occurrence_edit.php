<form class="cmxform"  name='editList' action="<?php echo url::site().'occurrence/save' ?>" method="POST">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<fieldset>
<legend>Occurrence Details</legend>
<ol>
<li>
<label for='taxon'>Taxon:</label>
<input type='text' name='taxon' id='taxon' value='<?php echo $model->taxa_taxon_list->taxon->taxon; ?>' />
<?php echo html::error_message($model->taxa_taxon_list->taxon->getError('taxon')); ?>
</li>
</ol>
</fieldset>
<?php echo $metadata ?>
<input type="submit" name="submit" value="Submit" />
<input type="submit" name="submit" value="Delete" />