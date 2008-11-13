<?php defined('SYSPATH') or die('No direct script access.');

class Login_Controller extends Indicia_Controller {

	public function index()
	{
		if (request::method() == 'post')
		{
			if ($this->auth->login($_POST['UserName'], $_POST['Password']))
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

	public function login_by_password()
	{
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
				$this->template->title = 'User Login by email';
				$this->template->content = new View('login_by_email');	
				$this->template->content->error_message = 'Invalid Email address/Password Combination';
			}
		}
		else
		{
			$this->template->title = 'User Login by email';
			$this->template->content = new View('login_by_email');	
		}

	}
	
}