<p>This page allows you to specify a users details.</p>
<form class="cmxform" action="<?php echo url::site().'user/save'; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<?php if (isset($model->person_id)) { ?>
<input type="hidden" name="person_id" id="person_id" value="<?php echo html::specialchars($model->person_id); ?>" />
<?php } ?>
<fieldset>
<legend>User's Details</legend>
<ol>
<li>
<label for="username">Username</label>
<input id="username" name="username" value="<?php echo html::specialchars($model->username); ?>" />
<?php echo html::error_message($model->getError('username')); ?>
</li>
<li>
<label for="interests">Interests</label>
<textarea rows="3" id="interests" name="interests"><?php echo html::specialchars($model->interests); ?></textarea>
<?php echo html::error_message($model->getError('interests')); ?>
</li>
<li>
<label for="location_name">Location Name</label>
<textarea rows="2" id="location_name" name="location_name"><?php echo html::specialchars($model->location_name); ?></textarea>
<?php echo html::error_message($model->getError('location_name')); ?>
</li>
<li>
<label for="email_visible">Show Email Address</label>
<?php echo form::checkbox('email_visible', TRUE, isset($model->email_visible) AND ($model->email_visible == 't') ) ?>
</li>
<li>
<label for="view_common_names">Show Common Names</label>
<?php echo form::checkbox('view_common_names', TRUE, isset($model->view_common_names) AND ($model->view_common_names == 't') ) ?>
</li>
<li>
<label for="core_role_id">Role within CORE Module</label>
<select id="core_role_id" name="core_role_id">
	<option>None</option>
<?php
	$core_roles = ORM::factory('core_role')->orderby('title','asc')->find_all();
	foreach ($core_roles as $core_role) {
		echo '	<option value="'.$core_role->id.'" ';
		if ($core_role->id==$model->core_role_id)
			echo 'selected="selected" ';
		echo '>'.$core_role->title.'</option>';
	}
?>
</select>
<?php echo html::error_message($model->getError('core_role_id')); ?>
</li>
</ol>
</fieldset>
<?php if (isset($password_field) and $password_field != '') { echo '<fieldset><legend>Password Control</legend><ol><li>'.$password_field.html::error_message($model->getError('password')).'</li></ol></fieldset>'; } ?>
<?php echo $metadata ?>
<input type="submit" name="submit" value="Submit" />
</form>
