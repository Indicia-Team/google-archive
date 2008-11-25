<?php if ( ! empty($error_message) )
{
	echo $error_message;
}
?>
<form name = "login" action="<?php echo url::site(); ?>login/login_by_email" method="post">
  <label for="Email">Email</label>
  <input type = "text" name = "Email" id = "Email" value="" ><br />
  <label for="Password">Password</label>
  <input type = "password" name = "Password" id = "Password" value="" ><br />
  <label for="remember_me" >Remember me</label>
  <input type="checkbox" id="remember_me" name="remember_me"  /><br />
  <input type = "submit" value = "Login" >
</form>
<?php if ( ! empty($link_to_username) )
{ ?>
  <br />You may <a href="/login">click here to log in using your Username</a>.
<?php } ?>
<br />If you have forgotten your password, <a href="<?php echo url::site(); ?>forgotten_password">click here to request an email allowing you to reset your password</a>.