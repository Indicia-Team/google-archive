<?php defined('SYSPATH') or die('No direct script access.');

class Login_Controller extends Indicia_Controller {

// Asi it stands, login does not check passwords, roles, and remeber me doesn't work.
// Things to do (in order):
// 1 Put in an already Logged on check to login forms
// 2 Create a Logout controller.
// 3 Enable role checking
// 4 Enable Login by email
// 5 Create Lost Password functionality
// 6 Enable password checking
// 7 Enable remember me functionality
	
	public function index()
	{
		$login_config = Kohana::config('login');
		if ( $login_config['login_by_email'] == 'YES')
		{
				$this->login_by_email();
		}
		else
		{
			if (request::method() == 'post')
			{
				if ($this->auth->login(array('username' => $_POST['UserName']), $_POST['Password']))
				{
					url::redirect();
				}
				else
				{
					$this->template->title = 'User Login';
					$this->template->content = new View('login_by_username');	
					$this->template->content->error_message = 'Invalid Username/Password Combination';
				}
			}
			else
			{
				$this->template->title = 'User Login';
				$this->template->content = new View('login_by_username');	
			}
		}
	}

	public function login_by_email()
	{
		$login_config = Kohana::config('login');
		if (request::method() == 'post')
		{
			# this is name complete as needs to convert from email address to username
			# or to extend auth model
			if ($this->auth->login($_POST['UserName'], $_POST['Password']))
			{
				url::redirect();
			}
			else
			{
				$this->template->title = 'User Login';
				$this->template->content = new View('login_by_email');	
				$this->template->content->error_message = 'Invalid Email address/Password Combination';
				if ( $login_config['login_by_email'] != 'YES')
				{
					$this->template->content->link_to_username = 'YES';
				}
			}
		}
		else
		{
			$this->template->title = 'User Login';
			$this->template->content = new View('login_by_email');	
			if ( $login_config['login_by_email'] != 'YES')
			{
				$this->template->content->link_to_username = 'YES';
			}
		}

	}
	
}