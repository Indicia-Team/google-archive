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
<input type="submit" name="submit" value="Submit" />
<input type="submit" name="submit" value="Delete" />
</fieldset>
<?php echo $metadata ?>
</form>
<?php if ($model->id != '' && $table != null) { ?>
	<h2> Sublists </h2>
	<?php echo $table; ?>
<form class="cmxform" action="<?php echo url::site(); ?>/taxon_list/create" method="post">
	<input type="hidden" name="parent_id" value=<?php echo $model->id ?> />
	<input type="submit" value="New Sublist" />
	</form>
<?php } ?>
