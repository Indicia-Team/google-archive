<?php

/**
 * Indicia, the OPAL Online Recording Toolkit.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 *
 * @package Core
 * @subpackage Controllers
 * @author	Indicia Team
 * @link http://code.google.com/p/indicia/
 * @license http://www.gnu.org/licenses/gpl.html GPL
 */

defined('SYSPATH') or die('No direct script access.');

/**
 * Controller class for the new password page.
 *
 * @package Core
 * @subpackage Controllers
 */
class New_Password_Controller extends Indicia_Controller {

  public function index()
  {
    if ( ! empty($_SESSION['auth_user']) AND is_object($_SESSION['auth_user'])
      AND ($_SESSION['auth_user'] instanceof User_Model) AND $_SESSION['auth_user']->loaded)
    {
      // Everything is okay so far
      $user = new User_Model($_SESSION['auth_user']->id);
      $person = ORM::factory('person', $user->person_id);
      $view = new View('login/new_password');
      // because of encryption of passwords, this must be done outside the model
      // password is cleared down.
      $view->password = '';
      $view->password2 = '';
      $view->email_key = '';
      $view->user_model = $user;
      $view->person_model = $person;
      if(is_null($user->password) or $user->password == '')
        $view->message = "This is the first login with this user, which has been initialised with an empty password and email address.<br />You must set both your password and email address now before you may access the system.";
      $this->template->title = 'Enter New Password';
      $this->template->content = $view;
    } else {
      $this->template->title = 'New Password Invocation Error';
      $this->template->content = new View('login/login_message');
      $this->template->content->message = 'You cannot set your password without being logged in.<br />';
    }
  }

  /*
   * The email function is called from the link sent out on the forgotten password
   */
  public function email($key = NULL)
  {
    if ($key == null)
    {
      $this->template->title = 'New Password Invocation Error';
      $this->template->content = new View('login/login_message');
      $this->template->content->message = 'You cannot set your password from an email without an associated ID string.<br />';
      return;
    }

    $user = new User_Model(array('forgotten_password_key' => $key));
    if ( ! $user->loaded )
    {
      $this->template->title = 'New Password Invocation Error';
      $this->template->content = new View('login/login_message');
      $this->template->content->message = 'The identification string embedded in this link is invalid.<br /><br />If this link has been followed from an email generated by this system, then the most likely causes of the error are:<br /> (1) that this link has already been used to alter the password, or<br /> (2) there has been a successful login since this email was sent.<br /><br />These links a single use only, and once they have been used the identification string is invalidated.<br /><br />If you wish to reset your password, please request another Forgotten Password email. <a href="'.url::site().'forgotten_password">Click here to request an email allowing you to reset your password</a>.';
      return;
    }

    $person = ORM::factory('person', $user->person_id);
    $view = new View('login/new_password');
    // because of encryption of passwords, this must be done outside the model
    // password is cleared down.
    $view->password = '';
    $view->password2 = '';
    $view->email_key = $key;
    $view->user_model = $user;
    $view->person_model = $person;
    $this->template->title = 'Enter New Password';
    $this->template->content = $view;

  }

  public function save() {
    $user = new User_Model($_POST['id']);
    if ( ! $user->loaded )
    {
      $this->template->title = 'New Password Invocation Error';
      $this->template->content = new View('login/login_message');
      $this->template->content->message = 'Invalid user id.';
      return;
    }
    $username = $user->username;
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $email_key = $_POST['email_key'];
    $person = ORM::factory('person', $user->person_id);

    if($email_key != '') {
      /* if the email_key field is filled in, then being called from a forgotten password email */
      if($user->forgotten_password_key != $email_key) {
        $this->template->title = 'New Password Invocation Error';
        $this->template->content = new View('login/login_message');
        $this->template->content->message = 'The forgotten password identification string embedded in this link is invalid for this user. This may be because there has been a valid login for this user between the point where the Set Password page was brought up and when the Submit button was pressed.';
        return;
      }
    } else if ( ! empty($_SESSION['auth_user']) AND is_object($_SESSION['auth_user'])
        AND ($_SESSION['auth_user'] instanceof User_Model) AND $_SESSION['auth_user']->loaded) {
      if($user->id != $_SESSION['auth_user']->id) {
        $this->template->title = 'New Password Invocation Error';
        $this->template->content = new View('login/login_message');
        $this->template->content->message = 'Inconsistent user id: POST vs logged in user.';
        return;
      }
    } else {
      $this->template->title = 'New Password Invocation Error';
      $this->template->content = new View('login/login_message');
      $this->template->content->message = 'Attempt to set password when not logged in.';
      return;
    }
    
    $user_validation = new Validation($_POST);
    $person_validation = new Validation($_POST);

    // override the user_id for person in submission
    $person_validation['id'] = $user->person_id;

    // Can't just and following together as I want both functions to run
    $userstatus = $user->password_validate($user_validation, false);
    $personstatus = $person->email_validate($person_validation, false);

    if ($userstatus and $personstatus) {
      $user->save();
      $person->save();
      // we need different paths for core users and web site users
      if (is_null($user->core_role_id)) {
        // just return a success confirmation, can't log them in as not a core user
        $this->template->title = 'Password reset successfully';
        $this->template->content = new View('login/login_message');
        $this->template->content->message = 'Your indicia password has been reset and you can now use the new password to <a href="' . url::site() . '/login">log in</a>.<br />';
      } else {
        // with the password updated, login and jump to the home page
        $this->auth->login($user->id, $password);
        url::redirect(arr::remove('requested_page', $_SESSION));
      }

    } else {
      // errors are now embedded in the model
      $view = new View('login/new_password');
      $user->load_values(array('username' => $username)); // repopulate for error condition after validate has removed it (is a disabled field so not present in POST)
      // have to reset passord as it gets encrypted
      $view->password = $password;
      $view->password2 = $password2;
      $view->email_key = $email_key;
      $view->user_model = $user;
      $view->person_model = $person;
      $this->template->title = 'Enter New Password';
      $this->template->content = $view;

    }
  }
  
  protected function page_authorised()
  {
    return true;
  }
}