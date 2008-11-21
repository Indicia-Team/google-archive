<?php if ($model->parent_id != null) { ?>
<h1>Subset of: 
<a href="<?php echo url::site() ?>termlist/edit/<?php echo $model->parent_id ?>" >
<?php echo ORM::factory("termlist",$model->parent_id)->title ?>
</a>
</h1>
<?php } ?>
<form class="cmxform"  name='editList' action="<?php echo url::site().'termlist/save' ?>" method="POST">
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
<select id="website_id" name="website_id">
	<option>&lt;Core Module&gt;</option>
<?php
	$websites = ORM::factory('website')->orderby('title','asc')->find_all();
	foreach ($websites as $website) {
		echo '	<option value="'.$website->id.'" ';
		if ($website->id==$model->website_id)
			echo 'selected="selected" ';
		echo '>'.$website->title.'</option>';
	}
?>
</select>
<?php echo html::error_message($model->getError('website_id')); ?>
</li>
</ol>
</fieldset>
<input type="submit" name="submit" value="Submit" />
<input type="submit" name="submit" value="Delete" />
<?php echo html::error_message($model->getError('deleted')); ?>
<?php echo $metadata ?>
</form>

<?php if ($model->id != '') { ?>
<form class="cmxform" action="<?php echo url::site().'termlists_term/page/'.$model->id ?>" >
<input type="submit" value="View Terms" />
</form>
<?php if ( $table != null) { ?>
	<br />
	<h2> Sublists </h2>
	<?php echo $table; ?>
<form class="cmxform" action="<?php echo url::site(); ?>/termlist/create" method="post">
	<input type="hidden" name="parent_id" value=<?php echo $model->id ?> />
	<input type="submit" value="New Sublist" />
	</form>
<?php }} ?>
