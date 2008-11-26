<?php defined('SYSPATH') or die('No direct script access.');

class Forgotten_Password_Controller extends Indicia_Controller {
	
	public function index()
	{
		$email_config = Kohana::config('email');
		
		if ($this->auth->logged_in())
		{
			$this->template->title = 'Already Logged In';
			$this->template->content = new View('login/login_message');	
			$this->template->content->message = 'You are already logged in.<br />';
			$this->template->content->link_to_home = 'YES';
			$this->template->content->link_to_logout = 'YES';
			return;
		}
		$this->template->title = 'Forgotten Password Email Request';
		$this->template->content = new View('login/forgotten_password');	
		if (request::method() == 'post')
		{
			$post = new Validation($_POST);
			$post->pre_filter('trim', TRUE);
			$post->add_rules('UserID', 'required');
			$user = ORM::factory('user', array('username' => $_POST['UserID']));
			if ( ! $user->loaded )
			{
				$person = ORM::factory('person', array('email_address' => $_POST['UserID']));
				if ( ! $person->loaded )
				{
					$this->template->content->error_message = 'Not a valid Username or Email address';
					return;
				}
				$user = ORM::factory('user', array('person_id' => $person->id));
				if ( ! $user->loaded )
				{
					$this->template->content->error_message = $_POST['UserID'].' is not a registered user';
					return;
				}
			}
			else
			{
				$person = ORM::factory('person', $user->person_id);
			}
			if ( is_null($user->core_role_id) )
			{
				$this->template->content->error_message = $_POST['UserID'].' does not have permission to log on to this website';
				return;
			}
// Still to be done - save it to the user
			$swift = email::connect();
			$link_code = $this->auth->hash_password($user->username);
			$message = new Swift_Message($email_config['forgotten_passwd_title'],
		                             View::factory('templates/forgotten_password_email')->set(array('server' => $email_config['server_name'], 'new_password_link' => '<a href="'.url::site().'new_password/'.$link_code.'">'.url::site().'new_password/'.$link_code.'</a>')),
		                             'text/html');
			$recipients = new Swift_RecipientList();
			$recipients->addTo($person->email_address, $person->first_name.' '.$person->surname);

			try
			{
				$swift->send($message, $recipients, $email_config['address']);
			}
			catch (Swift_ConnectionException $e)
			{
				throw new Kohana_User_Exception('swift.general_error', $e->getMessage());
			}
			$this->template->title = 'Email Sent';
			$this->template->content = new View('login/login_message');	
			$this->template->content->message = 'An email providing a link which will allow your password to be reset has been sent to the specified email address, or if a Username was provided, to the registered email address for that User.<br />';			
		}
	}
}