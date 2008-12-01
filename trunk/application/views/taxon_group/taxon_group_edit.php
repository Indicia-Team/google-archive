<p>This page allows you to specify the details of a taxon group..</p>
<form class="cmxform" action="<?php echo url::site().'taxon_group/save'; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<fieldset>
<legend>Taxon Group details</legend>
<ol>
<li>
<label for="title">Title</label>
<input id="title" name="title" value="<?php echo html::specialchars($model->title); ?>" />
<?php echo html::error_message($model->getError('title')); ?>
</li>
</ol>
</fieldset>
<?php echo $metadata ?>
<input type="submit" value="Save" name="submit" />
</form>