<?php if ($model->parent_id != null) { ?>
<h1>Subset of: 
<a href="<?php echo url::site() ?>taxon_list/edit/<?php echo $model->parent_id ?>" >
<?php echo ORM::factory("taxon_list",$model->parent_id)->title ?>
</a>
</h1>
<?php } ?>
<form class="cmxform"  name='editList' action="<?php echo url::site().'taxon_list/save' ?>" method="POST">
<fieldset>
<legend>List Details</legend>
<ol>
<li>
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo html::specialchars($model->parent_id); ?>" />
<label for="title">Title</label>
<input id="title" name="title" value="<?php echo html::specialchars($model->title); ?>"/>
<?php echo html::error_message($model->getError('title')); ?>
</li>
<li>
<label for="description">Description</label>
<textarea rows=7 id="description" name="description"><?php echo html::specialchars($model->description); ?></textarea>
<?php echo html::error_message($model->getError('description')); ?>
</li>
<li>
<label for="website">Owned by</label>
<input id="website" readonly='readonly' value="<?php echo (($model->website_id != null) ? (html::specialchars($model->website->title)) : ''); ?>"/>
<input id="website_id" name="website_id" type="hidden" value="<?php echo html::specialchars($model->website_id); ?>" />
<?php echo html::error_message($model->getError('website_id')); ?>
</li>
</ol>
<input type="submit" value="Submit" />
<input type="button" value="Delete" />
</fieldset>
<fieldset>
<legend>Metadata</legend>
<ol>
<li>
<label for="created">Created:</label>
<input id="created_on" name="created_on" readonly='readonly' value="<?php echo html::specialchars($model->created_on); ?>" />
</li>
<li>
<label for="created_by">Created by:</label>
<input type="hidden" id="created_by_id" name="created_by_id" value="<?php echo html::specialchars($model->created_by_id); ?>" />
<input readonly='readonly' value="<?php echo (($model->created_by_id != null) ? (html::specialchars($model->created_by->person->surname)) : ''); ?>" />
</li>
<li>
<label for="last_update">Last Updated:</label>
<input id="last_update" name="created_on" readonly='readonly' value="<?php echo html::specialchars($model->updated_on); ?>" />
</li>
<li>
<label for="updated_by">Updated by:</label>
<input type="hidden" name="updated_by_id" id="updated_by_id" value="<?php echo html::specialchars($model->updated_by_id); ?>" />
<input readonly='readonly' value="<?php echo (($model->updated_by_id != null) ? (html::specialchars($model->updated_by->person->surname)) : ''); ?>" />
</li>
</ol>
</fieldset>
</form>
<?php if ($model->id != '') { ?>
	<h2> Sublists </h2>
	<?php echo $table; ?>
	<form class="cmxform" action="/index.php/taxon_list/create" method="post">
	<input type="hidden" name="parent_id" value=<?php echo $model->id ?> />
	<input type="submit" value="New Sublist" />
	</form>
<?php } ?>
