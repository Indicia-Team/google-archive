# A Guide to the Indicia Forgotten Password Control #

## What it is for ##

The Forgotten Password Control allows a user on a client website to reset their password details held on the Indicia Warehouse. This is most suited to a website which uses the [Indicia Login Control](TheIndiciaLoginControl.md) as its authentication mechanism and which wishes to allow self-service password resets. The reset request generates an email from indicia core to the user's registered email address with a link to a password reset page.

## The demo page ##

There is a demonstration page giving an example of how this control can be used. The page can be accessed from the demo page index at `http://<your-host>/<your-indicia>/modules/demo/` in the 'Web-site User Control Demonstrations' section. The code for the demo page can also be viewed there.

In case you don't have access to a working indicia installation, here is a screen shot.

![http://indicia.googlecode.com/svn/wiki/forgotten_password_control_demo.jpg](http://indicia.googlecode.com/svn/wiki/forgotten_password_control_demo.jpg)

## What it does precisely ##

To reset a password with this control, a user must exist on indicia core, have a site role on the website they are logging-in from and not be banned. They must also supply the correct username or email address. The control provides a forgotten password 'widget' which you can put on your password reset page so that your users can enter their user-name or email address. The 'widget' appearance can be styled using CSS to suit your site requirements.

If the username or email address match a current user on your website, the control triggers an email to their regsitered email address and returns a 'true' response to your site so that you can display an appropriate message. The email contains a link back to the indicia core site with an authorisation key to allow a password reset for that user.

## How to use it ##

### Prerequisites ###

Before using this control, you need;

  1. a client indicia website which can access an instance of the indicia core/warehouse.
  1. some users set up on the indicia core and associated with your client site with at least the 'User' role.

### The Forgotten Password Control widget ###

This provides a standard forgotten password form which can be configured by CSS to match your site style. Using this saves the effort of laying out your own forgotten password form, but you don't have to use this widget, you can always produce your own web UI if you prefer to. There are examples of using this control on the demo page.

Using the control involves the following steps;

  1. require 'client\_helpers/user\_helper.php' (you may have to amend the relative path).
  1. include the indicia forgotten password control in your page where you want the HTML to be output (see following).
  1. style the control to match your site using your choice of CSS.

This is an example from the demo, for full list of options, see the interface documentation at the end of this document.

```
        <?php // the forgotten password control
        echo user_helper::forgotten_password_control(array(
            'action_url' => '',
            'show_fieldset' => true,
            'login_uri' => $base_url.'modules/demo/login_control.php',
        ));
        ?>
```

This example renders the following HTML output on your page.

```
<form id="indicia-forgotten-password-control" method="post" action="">
<fieldset>
<legend>Forgotten password user details</legend>
<p>You may enter either your user name or your email address in the field below, and an email will be sent to you. In this email will be a link to a webpage which will allow you to enter a new password. This ensures that only a person with access to your registered email account will be able to change your password.</p>
<label for="userid">User Name or Email Address:</label>
<input type="text" id="userid" name="userid" class=" "  value=""  />
<br/>
</fieldset>
<input type="submit" name="password_email_submit" id="password_email_submit" value="Request Forgotten Password Email" />
<br /><a href="http://localhost/indicia/modules/demo/login_control.php">Return to the login page.</a>
</form>
```

### Calling Indicia core to send the reset password email ###

The Forgotten Password Control form above will post the user input to the page specified by the action attribute on the `<form>` element. You need to set this to match the page that will process the forgotten password request.

On your processing page you need to make 2 calls to indicia core, one to get the standard authority tokens for calling indicia services, and the second to request the reset password email. You can set an option to control if the name should be treated as case sensitive or not. These are documented on the interface specification below.

The following shows example code.

```
          // ask core whether the service credentials are good for this website.
          $readAuth = user_helper::get_read_auth($website_id, $website_password);
          // make the call to indicia core to request the password reset email
          $response = user_helper::request_password_reset($_POST['userid'], $readAuth);
          // act on the result
          $result = $response['result'];
          if ($result) { // requested successfully
            // inform the user a mail is on the way to them
          } else { // not successful
            // inform the user of the error 
          }
```