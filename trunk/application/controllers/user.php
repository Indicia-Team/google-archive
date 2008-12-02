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
			'Edit User Details' => 'user/edit_from_person/£person_id£',
			'Edit Person Details' => 'person/edit_from_user/£person_id£',
			'Send Forgotten Password Email' => 'forgotten_password/send_from_user/£person_id£',
		);
		
	}
	
	protected function password_field($password = '')
	{
		return $password != '' ? '<label for="password">Password</label><input type="password" id="password" name="password" value="'.html::specialchars($password).'" />' : '';
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
        if ($id == null)
        {
            // we need a general error controller
			print "Cannot edit user through edit_from_person() without an associated Person ID";
        }
		else
		{
			$this->model = new User_Model(array('person_id' => $id));
			if ( $this->model->loaded )
				$this->setView('user/user_edit', 'User', array('password_field' => ''));
			else {
				$login_config = Kohana::config('login');
				$person = ORM::factory('person', $id);
				$this->setView('user/user_edit', 'User',
					array('password_field' => $this->password_field($login_config['default_password'])));
				$this->template->content->model->person_id = $id;
				$this->template->content->model->username = $person->first_name.'.'.$person->surname;
			}
				
		}
	}

	protected function submit_fail() {
		$this->setView('user/user_edit', 'User',
			array('password_field' => isset($_POST['password']) ? $this->password_field($_POST['password']) : ''));
	}
	
}

?>
