<?php if ( ! empty($error_message) )
{
	echo $error_message;
}
?>
<form name = "login" action="<?php echo url::site(); ?>login">
  <label for="UserName">User Name</label>
  <input type = "text" name = "UserName" id = "UserName" value="" ><br />
  <label for="Password">Password</label>
  <input type = "password" name = "Password" id = "Password" value="" ><br />
  <label for="remember_me" >Remember me</label>
  <input type="checkbox" id="remember_me" name="remember_me"  /><br />
  <input type = "submit" value = "Login" >
</form>
<br />If you have forgotten your username, <a href="/login/login_by_email.html">click here to log in using your email address</a>.<br />
If you have forgotten your password, <a href="/forgotten_password.html">click here to request an email allowing you to reset your password</a>.