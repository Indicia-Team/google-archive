<form class="cmxform"  name='editList' action="<?php echo url::site().'occurrence/save' ?>" method="POST">
<fieldset>
<legend>Occurrence Details</legend>
<ol>
<li>
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
</fieldset>
<?php echo $metadata ?>
<input type="submit" name="submit" value="Submit" />
<input type="submit" name="submit" value="Delete" />

