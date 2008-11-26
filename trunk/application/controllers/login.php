<?php defined('SYSPATH') or die('No direct script access.');

class Login_Controller extends Indicia_Controller {

// As it stands, login does not check passwords, roles, and remeber me doesn't work.
// Things to do (in order):
// 1 Create Lost Password functionality
// 2 Enable password checking
// 3 Enable remember me functionality
	
	public function index()
	{
		$login_config = Kohana::config('login');
		if ( $login_config['login_by_email'] == 'YES')
		{
				$this->login_by_email();
				return;
		}
		if ($this->auth->logged_in())
		{
			$this->template->title = 'Already Logged In';
			$this->template->content = new View('login/login_message');	
			$this->template->content->message = 'You are already logged in.<br />';
			$this->template->content->link_to_home = 'YES';
			$this->template->content->link_to_logout = 'YES';
			return;
		}
		$this->template->title = 'User Login';
		$this->template->content = new View('login/login_by_username');	
		$this->template->content->error_message = '';
		$this->template->content->admin_contact = $login_config['admin_contact'];
		if (request::method() == 'post')
		{			
			if ($this->auth->login(array('username' => $_POST['UserName']), $_POST['Password']))
			{
// I don't trust the results!! There is something funny going on where the
// number of rows in a query is not being reported correctly - an invalid username returns
// a valid login with the first real user.
// THIS IS A DOUBLE CHECK. IF THE USERNAME DOESN'T MATCH, FORCE A LOG OFF.
				if ($_POST['UserName'] == $_SESSION['auth_user']->username)
				{
					url::redirect(arr::remove('requested_page', $_SESSION));
					return;
				}
				$this->auth->logout(TRUE);
			}
			$this->template->content->error_message = 'Invalid Username/Password Combination, or insufficient privileges';
		}
	}

	public function login_by_email()
	{
		$login_config = Kohana::config('login');
		if ($this->auth->logged_in())
		{
			$this->template->title = 'Already Logged In';
			$this->template->content = new View('login/login_message');	
			$this->template->content->message = 'You are already logged in.';
			$this->template->content->link_to_home = 'YES';
			$this->template->content->link_to_logout = 'YES';
			return;
		}
		$this->template->title = 'User Login';
		$this->template->content = new View('login/login_by_email');	
		$this->template->content->error_message = '';
		$this->template->content->admin_contact = $login_config['admin_contact'];
		if ( $login_config['login_by_email'] != 'YES')
		{
			$this->template->content->link_to_username = 'YES';
		}
		
		if (request::method() == 'post')
		{
			# this is name complete as needs to convert from email address to username
			# or to extend auth model
			$person = ORM::factory('person', array('email_address' => $_POST['Email']));
			
			if ($this->auth->login(array('person_id' => $person->id), $this->auth->hash_password($_POST['Password'])))
			{
					url::redirect(arr::remove('requested_page', $_SESSION));
					return;
			}
			$this->template->content->error_message = 'Invalid Email address/Password Combination, or insufficient privileges';
		}
	}
	
}