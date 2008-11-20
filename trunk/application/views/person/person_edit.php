<p>This page allows you to specify a persons details.</p>
<form class="cmxform" action="<?php echo url::site().'person/save'; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<?php if (!empty($return_url)) {
?>
<input type="hidden" name="return_url" id="return_url" value="<?php echo html::specialchars($return_url); ?>" />
<?php } ?>
<fieldset>
<legend>Person's Details</legend>
<ol>
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
<?php if ( ! empty($enable_create_button) )
{ ?>
<input type="submit" name="submit" value="Create User Details" />
<?php } else { ?>
<input type="submit" name="submit" value="Create User Details" disabled="disabled" />
<?php } ?>
<?php if ( ! empty($enable_edit_button) )
{ ?>
<input type="submit" name="submit" value="Edit User Details" />
<?php } else { ?>
<input type="submit" name="submit" value="Edit User Details" disabled="disabled" />
<?php } ?>
</form>
