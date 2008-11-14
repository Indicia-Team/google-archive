<p>This page allows you to specify the details of a survey in which samples and records can be organised.</p>
<form class="cmxform" action="<?php echo url::site().'survey/save'; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<fieldset>
<legend>Survey details</legend>
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
<li>
<label for="website">Website</label>
<input id="website" name="website" value="<?php if ($model->website_id) echo html::specialchars($model->website->title); ?>" />
<?php echo html::error_message($model->getError('website_id')); ?>
</li>
</ol>
</fieldset>
<input type="submit" value="Save" />
</form>
