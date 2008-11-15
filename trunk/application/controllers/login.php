<?php defined('SYSPATH') or die('No direct script access.');

class Login_Controller extends Indicia_Controller {

// As it stands, login does not check passwords, roles, and remeber me doesn't work.
// Things to do (in order):
// 1 Enable role checking
// 2 Enable Login by email
// 3 Create Lost Password functionality
// 4 Enable password checking
// 5 Enable remember me functionality
// 6 Put in hooks to auto redirect to login if not logged in
	
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
			$this->template->content = new View('login_message');	
			$this->template->content->message = 'You are already logged in.';
			$this->template->content->link_to_home = 'YES';
			$this->template->content->link_to_logout = 'YES';
			return;
		}
		$this->template->title = 'User Login';
		$this->template->content = new View('login_by_username');	
		$this->template->content->error_message = 'At this time validation only occurs on username: no checks done on Password or Roles. Remember me does not work, nor does forgotten password.';
		if (request::method() == 'post')
		{
			if ($this->auth->login(array('username' => $_POST['UserName']), $_POST['Password']))
			{
				url::redirect();
				return;
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
			$this->template->content = new View('login_message');	
			$this->template->content->message = 'You are already logged in.';
			$this->template->content->link_to_home = 'YES';
			$this->template->content->link_to_logout = 'YES';
			return;
		}
		$this->template->title = 'User Login';
		$this->template->content = new View('login_by_email');	
		$this->template->content->error_message = 'At this time this is not implemented';
		if ( $login_config['login_by_email'] != 'YES')
		{
			$this->template->content->link_to_username = 'YES';
		}
		
		if (request::method() == 'post')
		{
			# this is name complete as needs to convert from email address to username
			# or to extend auth model
			if ($this->auth->login($_POST['UserName'], $_POST['Password']))
			{
				url::redirect();
				return;
			}
			$this->template->content->error_message = 'Invalid Email address/Password Combination, or insufficient privileges';
		}
	}
	
}