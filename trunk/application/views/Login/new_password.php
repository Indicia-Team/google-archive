<form class="cmxform"  name = "new_password" action="<?php echo url::site(); ?>new_password/save" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<?php if (isset($key)) { ?>
<input type="hidden" name="key" id="key" value="<?php echo html::specialchars($key); ?>" />
<?php } ?>
<fieldset>
<legend>Set Password</legend>
<ol>
<li>
  <label for="username">Username</label>
  <input type = "text" name = "username" id = "username" value="<?php echo $username; ?>" disabled="disabled"  class="narrow" >
  <?php echo html::error_message($model->getError('username')); ?>
</li>
<li>
  <label for="email_address">Email</label>
  <input type = "text" name = "email_address" id = "email_address" value="<?php echo $email_address; ?>" disabled="disabled" class="narrow" >
  <?php echo html::error_message($model->getError('email_address')); ?>
</li>
<li>
  <label for="password">Password</label>
  <input type = "password" name = "password" id = "password" value="" class="narrow" >
  <?php echo html::error_message($model->getError('password')); ?>
</li>
<li>
  <label for="password2">Repeat Password</label>
  <input type = "password" name = "password2" id = "password2" value="" class="narrow" >
</li>
<li>
  <label for="remember_me" >Remember me</label>
  <input type="checkbox" id="remember_me" name="remember_me" class="default" />
</li>
</fieldset>
  <input type = "submit" value = "Submit New Password" >
</form>
