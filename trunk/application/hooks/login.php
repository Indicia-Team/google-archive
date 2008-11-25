<?php

class login {

	public function __construct()
	{
		// Hook into routing
		Event::add('system.routing', array($this, 'check'));
	}

	public function check()
	{
		// Always logged in
		$auth = new Auth();
		$uri = new URI();

		if ($uri->segment(1) != 'login' AND $uri->segment(1) != 'logout' AND $uri->segment(1) != 'new_password' AND $uri->segment(1) != 'forgotten_password' AND ! $auth->logged_in())
		{
			$_SESSION['requested_page'] = $uri->string();
			url::redirect('login');
		}
		else if ($uri->segment(1) == 'login')
		{
			// Make sure they can read cookies.
			if ( ! cookie::get('kohanasession', FALSE))
				throw new Kohana_Exception('indicia.no_cookies');
		}
	}
}

new login;