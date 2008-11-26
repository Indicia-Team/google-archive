Indicia is a toolkit that simplifies the construction of new websites which allow data entry, mapping and reporting of wildlife records. Indicia is an Open Source project funded by the Open Air Laboratories Network and managed by the Centre for Ecology and Hydrology.<br /><br />
In order to gain access to this Indicia system you must log on. If you do not have an account please contact the administrator <?php echo $admin_contact; ?> who can create one for you.<br /><br />
In order to disable the automatic log on, set the enable_hooks in config.php to FALSE.<br /><br />
At this time validation only occurs on username and core_role: no checks done on Password. Remember me does not work, nor does forgotten password.<br /><br />
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