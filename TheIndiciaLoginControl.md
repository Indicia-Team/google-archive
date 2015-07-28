# A Guide to the Indicia Login Control #

## What it is for ##

Indicia does not impose a method of user authentication onto the websites that you build with it. Often you will be integrating Indicia into an existing system with an existing login mechanism, or you may even want to allow guest users to use the recording facilities. However it is possible to use Indicia's own user management facilities to control access to your website when this is appropriate.

The Login Control allows a client website to authenticate a user against the user details held on the indicia core/warehouse. This is most suited to a website which does not have an existing user authentication mechanism and which wishes to obtain the correct indicia user\_id for the current user so that any submissions by that user can be correctly attributed in the indicia warehouse.

## The demo page ##

There is a demonstration page giving an example of how this control can be used. The page can be accessed from the demo page index at `http://<your-host>/<your-indicia>/modules/demo/` in the 'Web-site User Control Demonstrations' section. The code for the demo page can also be viewed there.

In case you don't have access to a working indicia installation, here is a screen shot.

![http://indicia.googlecode.com/svn/wiki/login_control_demo.jpg](http://indicia.googlecode.com/svn/wiki/login_control_demo.jpg)

## What it does precisely ##

To login with this control, a user must exist on indicia core, not be flagged as deleted, have a site role on the website they are logging-in from and not be banned. They must also supply the correct password. The login control provides a login 'widget' which you can put on your welcome page so that your users can enter their user-name and password. If you prefer, it can be set for email address and password instead. It can also show options to remember the user, register a new user, or send a 'forgotten password' email. If the 'remember me' option is allowed, this request will be passed to your website code, but it is up to your local website to implement this function using the mechenism of your choice.

If the user credentials are successfully authenticated, the control returns the indicia internal user id to the calling web site so that any submissions can be made using that user id. Optionally, you can also ask the login process to return an array of user profile data which includes names, the site role and much else, assuming it has been recorded on core. A full list of profile items is included in the interface documentation section below.

The forgotten password email function is also available and this link is functional on the demo. The user registration function is planned but not yet implemented. The 'widget' appearance can be styled using CSS to suit your site requirements.

The user credentials are encrypted when sent from your site to indicia core, so the password is kept secure. Of course, if you are using http between your site and the user's browser, the password will be sent over that link in plain text and is not secure. If security is important, you should use https for your login page.

## How to use it ##

### Prerequisites ###

Before using this control, you need:
  1. a client indicia website which can access an instance of the indicia core/warehouse.
  1. some users set up on the indicia core and associated with your client site with at least the 'User' role.

### The Login Control widget ###

This provides a standard login form which can be configured by CSS to match your site style. Using this saves the effort of laying out your own login form, but you don't have to use this widget, you can always produce your own web UI if you prefer to. There are examples of using this control on the demo page.

Using the control involves the following steps:

  1. require 'client\_helpers/user\_helper.php' (you may have to amend the relative path).
  1. include the indicia login control in your page where you want the HTML to be output (see following).
  1. style the control to match your site using your choice of CSS.

```
        <?php // the login control using user name (the default)
          echo user_helper::login_control(array(
            'action_url' => '',
            'login_text' => 'Please enter your username and password.<br />'
              .'Remember the password is case sensitive.',
            'show_fieldset' => true,
            'legend_label' => 'Login to MyDemoSite',
            'show_rememberme' => true,
            'register_uri' => $base_url.'modules/demo/register_user_control.php',
            'forgotten_uri' => $base_url.'modules/demo/forgotten_password_control.php',
          ));
        ?>

```

This example renders the following HTML output on your page.

```
<form id="indicia-login-control" method="post" action="">
<fieldset>
<legend>Login to MyDemoSite</legend>
<p>Please enter your username and password.<br />Remember the password is case sensitive.</p>
<label for="username">Username:</label>
<input type="text" id="username" name="username" class=" "  value=""  />
<br/>
<label for="password">Password:</label>
<input type="password" id="password" name="password" class=" "  value=""  />
<br/>
<label for="remember_me">Remember me:</label>
<input type="hidden" name="remember_me" value="0"/><input type="checkbox" id="remember_me" name="remember_me" value="1" class=" "  />
<br/>
<a href="http://localhost/indicia/modules/demo/register_user_control.php">Register to use this site.</a>&nbsp;
<a href="http://localhost/indicia/modules/demo/forgotten_password_control.php">Request forgotten password.</a>
</fieldset>
<input type="submit" name="login_submit" id="login_submit" value="Login" />
</form>
```

### Calling Indicia core to authenticate the user ###

The Login Control form above will post the user input to the page specified by the action attribute on the `<form>` element. You need to set this to match the page that will process the login request.

On your processing page you need to make 2 calls to indicia core, one to get the standard authority tokens for calling indicia services, and the second to authenticate the user. You can set option to if the user is identified by name or email, if the name should be treated as case sensitive or not, and whether to return profile data. These are documented on the interface specification below.

The following shows example code.

```
              // ask core whether the credentials are good for this website.
              $readAuth = user_helper::get_read_auth($website_id, $website_password);
              // set options for case insensitive name comparison and to request the user profile data
              $options = array('namecase' => false, 'getprofile' => true);
              // make the call to indicia core to authenticate the user. This call is encrypted.
              $response = user_helper::authenticate_user($_POST['username'], $_POST['password'],
                $readAuth, $website_password, $options);
              // act on the result
              $user_id = $response['user_id'];
              if ($user_id > 0) { // authenticated successfully
                // continue to logged in part of site, getting profile data if wanted
                $profile = $response['profile'];
                // or if we hadn't got profile on login, we could make the call on the next line.
                // $profile = user_helper::get_user_profile($user_id, $readAuth);
              } else { // not authenticated
                // display an error and return to the login page;
              }
```