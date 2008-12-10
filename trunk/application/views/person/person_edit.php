<p>This page allows you to specify a persons details.</p>
<form class="cmxform" action="<?php echo url::site().'person/save'; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<?php echo $return_url ?>
<fieldset>
<legend>Person's Details</legend>
<ol>
<li>
<label for="title_id">Title</label>
<select id="title_id" name="title_id">
	<option>&lt;Please select&gt;</option>
<?php
	$titles = ORM::factory('title')->orderby('id','asc')->find_all();
	foreach ($titles as $title) {
		echo '	<option value="'.$title->id.'" ';
		if ($title->id==$model->title_id)
			echo 'selected="selected" ';
		echo '>'.$title->title.'</option>';
	}
?>
</select>
<?php echo html::error_message($model->getError('title_id')); ?>
</li>
<li>
<label for="first_name">First name</label>
<input id="first_name" name="first_name" value="<?php echo html::specialchars($model->first_name); ?>" />
<?php echo html::error_message($model->getError('first_name')); ?>
</li>
<li>
<label for="surname">Surname</label>
<input id="surname" name="surname" value="<?php echo html::specialchars($model->surname); ?>" />
<?php echo html::error_message($model->getError('surname')); ?>
</li>
<li>
<label for="initials">Initials</label>
<input id="initials" name="initials" value="<?php echo html::specialchars($model->initials); ?>" />
<?php echo html::error_message($model->getError('initials')); ?>
</li>
<li>
<label for="address">Address</label>
<textarea rows="4" id="address" name="address"><?php echo html::specialchars($model->address); ?></textarea>
<?php echo html::error_message($model->getError('address')); ?>
</li>
<li>
<label for="email_address">Email Address</label>
<input id="email_address" name="email_address" value="<?php echo html::specialchars($model->email_address); ?>" />
<?php echo html::error_message($model->getError('email_address')); ?>
</li>
<li>
<label for="website_url">Website URL</label>
<textarea rows="3" id="website_url" name="website_url"><?php echo html::specialchars($model->website_url); ?></textarea>
<?php echo html::error_message($model->getError('website_url')); ?>
</li>
</ol>
</fieldset>
<?php echo $metadata ?>
<input type="submit" name="submit" value="Submit" />
</form>
