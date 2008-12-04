<?php

class login {

	public function __construct()
	{
		// Hook into routing
		Event::add('system.routing', array($this, 'check'));
	}

	/**
	 * When visiting any page on the site, check if the user is already logged in,
	 * or they are visiting a page that is allowed when logged out. Otherwise,
	 * redirect to the login page. If visiting the login page, check the browser
	 * supports cookies.
	 */
	public function check()
	{
		// Always logged in
		$auth = new Auth();
		$uri = new URI();

		// check for setup request
		//
		if($uri->segment(1) == 'setup')
		{
			// get kohana paths
			//
			$ipaths = Kohana::include_paths();

			// check if indicia_setup module folder exists
			//
			clearstatcache();
			foreach($ipaths as $path)
			{
				if((preg_match("/indicia_setup/",$path)) && file_exists($path))
				{
					return;
				}
			}
		}
		elseif (! $auth->logged_in() AND
				! $auth->auto_login() AND
				$uri->segment(1) != 'login' AND
				$uri->segment(1) != 'logout' AND
				$uri->segment(1) != 'new_password' AND
				$uri->segment(1) != 'forgotten_password')
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