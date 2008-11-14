<?php defined('SYSPATH') or die('No direct script access.');

class Login_Controller extends Indicia_Controller {

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