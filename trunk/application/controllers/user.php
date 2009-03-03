<?php

class User_Controller extends Gridview_Base_Controller {

	public function __construct() {
		parent::__construct('user', 'gv_user', 'user/index');
		$this->columns = array(
			'name'=>''
			,'username'=>''
		    ,'core_role'=>''
			);
		$this->pagetitle = "Users";
		$this->model = new User_Model();
		$this->actionColumns = array(
			'Edit User Details' => 'user/edit_from_person/�person_id�',
			'Edit Person Details' => 'person/edit_from_user/�person_id�',
			'Send Forgotten Password Email' => 'forgotten_password/send_from_user/�person_id�',
		);
		
	}
	
	protected function password_fields($password = '', $password2 = '')
	{
		return '<li><label for="password">Password</label><input id="password" name="password" value="'.html::specialchars($password).'" /><span class="form_error">'.$this->model->getError('password').'</span></li><li><label for="password">Repeat Password</label><input id="password2" name="password2" value="'.html::specialchars($password2).'" /></li>';
	}

	// Due to the way the Users gridview is displayed (ie driven off the person table)
	// there is no specific create function, as the edit function handles this when there
	// is no user record for the specified person id.
	
	/**
	 * Subsiduary Action for user/edit page.
	 * Displays a page allowing modification of an existing user or creation of a new user
	 * driven by ther person id.
	 */
	public function edit_from_person($id = NULL) {
		if (!$this->page_authorised())
		{
			$this->access_denied();
		}
		else if ($id == null)
        {
	   		$this->setError('Invocation error: missing argument', 'You cannot edit user through edit_from_person() without an associated Person ID');
        }
        else
		{
			$this->model = new User_Model(array('person_id' => $id));
	    	$websites = ORM::factory('website')->find_all();
			if ( $this->model->loaded ) {
				$this->setView('user/user_edit', 'User', array('password_field' => ''));
				foreach ($websites as $website) {
					$users_website = ORM::factory('users_website', array('user_id' => $this->model->id, 'website_id' => $website->id));
					$this->model->users_websites[$website->id]=
							array(
								'id' => $website->id
								,'name' => 'website_'.$website->id
								,'title' => $website->title
								,'value' => ($users_website->loaded ? $users_website->site_role_id : null)
								);	
				}
			} else {
				// new user
				$login_config = Kohana::config('login');
				$person = ORM::factory('person', $id);
 				if ($person->email_address == null)
		        {
	   				$this->setError('Invocation error: missing email address', 'You cannot create user details for a person who has no email_address');
		        }
				else
				{
					$this->setView('user/user_edit', 'User',
						array('password_field' => $this->password_fields($login_config['default_password'], $login_config['default_password'])));
					$this->template->content->model->person_id = $id;
					$this->template->content->model->username = $person->first_name.'.'.$person->surname;
					foreach ($websites as $website)
						$this->model->users_websites[$website->id]=
								array(
									'id' => $website->id
									,'name' => 'website_'.$website->id
									,'title' => $website->title
									,'value' => null
									);
				}
			}
		}
	}

	protected function submit($submission){
        $this->model->submission = $submission;
        if (($id = $this->model->submit()) != null) {
            // Record has saved correctly
            // now save the users_websites records.
	    	$websites = ORM::factory('website')->find_all();
			foreach ($websites as $website) {
				$users_websites = ORM::factory('users_website',
						array('user_id' => $id, 'website_id' => $website->id));
        		$save_array = array(
	        			'id' => $users_websites->object_name
        				,'fields' => array('user_id' => array('value' => $id)
        									,'website_id' => array('value' => $website->id)
        									)
        				,'fkFields' => array()
        				,'superModels' => array());
				if ($users_websites->loaded || is_numeric($submission['fields']['website_'.$website->id]['value'])) {
					if ($users_websites->loaded)
							$save_array['fields']['id'] = array('value' => $users_websites->id);
					$save_array['fields']['site_role_id'] = array('value' => (is_numeric($submission['fields']['website_'.$website->id]['value']) ? $submission['fields']['website_'.$website->id]['value'] : null));
					$users_websites->submission = $save_array;
					$users_websites->submit();
				}
			}
            $this->submit_succ($id);
        } else {
            // Record has errors - now embedded in model
            $this->submit_fail();
        }
    }
	
	protected function submit_fail() {
		$this->setView('user/user_edit', 'User',
			array('password_field' => array_key_exists('password', $_POST) ? $this->password_fields($_POST['password'], $_POST['password2']) : ''));

		// copy the values of the websites into the users_websites array
	    $websites = ORM::factory('website')->find_all();
		foreach ($websites as $website) {
			$this->model->users_websites[$website->id]=
				array('id' => $website->id
					,'name' => 'website_'.$website->id
					,'title' => $website->title
					,'value' => (is_numeric($_POST['website_'.$website->id]) ? $_POST['website_'.$website->id] : NULL)
				);
		}
	}
	
	
	protected function page_authorised ()
	{
		return $this->auth->logged_in('CoreAdmin');
	}
}

?>
