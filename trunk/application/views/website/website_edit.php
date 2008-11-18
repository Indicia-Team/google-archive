<p>This page allows you to specify the details of a website that will use the services provided by this Indicia Core Module instance.</p>
<form class="cmxform" action="<?php echo url::site().'website/save'; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<fieldset>
<legend>Website details</legend>
<ol>
<li>
<label for="title">Title</label>
<input id="title" name="title" value="<?php echo html::specialchars($model->title); ?>" />
<?php echo html::error_message($model->getError('title')); ?>
</li>
<li>
<label for="description">Description</label>
<textarea rows="7" id="description" name="description"><?php echo html::specialchars($model->description); ?></textarea>
<?php echo html::error_message($model->getError('description')); ?>
</li>
</ol>
</fieldset>
<?php echo $metadata ?>
<input type="submit" value="Save" />
</form>
