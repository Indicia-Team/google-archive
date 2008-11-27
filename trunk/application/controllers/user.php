<?php

class User_Controller extends Gridview_Base_Controller {

	public function __construct() {
		parent::__construct('user', 'gv_user', 'user/index');
		$this->columns = array(
			'username'=>''
		    ,'core_role'=>''
			);
		$this->pagetitle = "Users";
	}

	protected function edit_person_button($extras='')
	{
		return form::submit('submit', 'Edit Person Details', $extras);
	}
	
	protected function return_url($return_url)
	{
		return '<input type="hidden" name="return_url" id="return_url" value="'.html::specialchars($return_url).'" />';
	}
	
	protected function disable_button($disable_button)
	{
		return $disable_button ? '<input type="hidden" name="disable_button" id="disable_button" value="YES" />' : '';
	}
	
	protected function password_field($password = '')
	{
		return $password != '' ? '<label for="password">Password</label><input type="password" id="password" name="password" value="'.html::specialchars($password).'" />' : '';
	}

	/**
	 * Action for user/create page.
	 * Displays a page allowing entry of a new user.
	 * Called only from within Persons, and passes a person ID (as a Foreign Key)
	 */
	public function create() {
		if ($this->uri->total_arguments()==0)
			print "Cannot create user without an associated Person ID";
		else
		{
			$login_config = Kohana::config('login');
		
			$user = ORM::factory('user');
			$person = ORM::factory('person', $this->uri->argument(1));
			$view = new View('user/user_edit');
			$user->username = $person->first_name.'.'.$person->surname;
			$view->model = $user;
			$view->metadata = $this->GetMetadataView($user);
			$this->template->title = $this->GetEditPageTitle($user, 'User');
			$view->model->person_id = $this->uri->argument(1);
			$view->return_url = $this->return_url('person/edit/'.$this->uri->argument(1));
			$view->password_field = $this->password_field($login_config['default_password']);
			$view->person_details_button = $this->edit_person_button('disabled="disabled"');
			$view->disable_button = $this->disable_button(TRUE);
			$this->template->content = $view;
		}
	}

	/**
	 * Action for user/edit page.
	 * Displays a page allowing modification of an existing user.
	 * This functrion is envoked in 2 different ways:
	 * 1) From the gridview
	 * 2) Direct URL
	 */
	public function edit() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit user without an ID";
		else
		{
			$user = new User_Model($this->uri->argument(1));
			$view = new View('user/user_edit');
			$view->model = $user;
			$view->metadata = $this->GetMetadataView($user);
			$this->template->title = $this->GetEditPageTitle($user, 'User');
			$view->return_url = '';
			$view->password_field = '';
			$view->person_details_button = $this->edit_person_button();
			$view->disable_button = $this->disable_button(FALSE);
			$this->template->content = $view;
		}
	}
	
	/**
	 * Subsiduary Action for user/edit page.
	 * Displays a page allowing modification of an existing user.
	 * This is called from a Person Record.
	 * When called from Person we want:
	 * A) To return back to the Person form on submission for that user
	 * B) We don't want to allow the drilling back to the person - ie we need to disable the relevant button.
	 */
	public function edit_from_person() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit user through edit_from_person() without a Person ID";
		else
		{
			$user = new User_Model(array('person_id' => $this->uri->argument(1)));
			$view = new View('user/user_edit');
			$view->model = $user;
			$view->metadata = $this->GetMetadataView($user);
			$this->template->title = $this->GetEditPageTitle($user, 'User');
			$view->return_url = $user->loaded ? $this->return_url('person/edit/'.$this->uri->argument(1)): '';
			$view->password_field = '';
			$view->person_details_button = $this->edit_person_button('disabled="disabled"');				
			$view->disable_button = $this->disable_button(TRUE);
			$this->template->content = $view;
		}
	}
	
	public function save() {
//		var_dump($_POST);
//		throw new Kohana_Exception('pagination.undefined_group', 'default');
		if (! empty($_POST['id']))
			$user = new User_Model($_POST['id']);
		else
			$user = new User_Model();
		
		$_POST = new Validation($_POST);
		if ($user->validate($_POST, TRUE)) {
			
			if(!empty($_POST['return_url'])) 
				url::redirect($_POST['return_url']);
			else if ($_POST['submit'] != 'Submit'){
				// the other button has been pressed, to edit person details for this user.
				url::redirect('person/edit_from_user/'.$user->person_id);
			} else {
				// For a successful submission, just redisplay the gridview
				url::redirect('user');
			}
				
		} else {
			// errors are now embedded in the model
		    $view = new View('user/user_edit');
			$view->model = $user;
		    $view->metadata = $this->GetMetadataView($user);
			$this->template->title = $this->GetEditPageTitle_local($user, 'User');
			$view->password_field = isset($_POST['password']) ? $this->password_field($_POST['password']) : '';
			$view->return_url = isset($_POST['return_url']) ? $this->return_url($_POST['return_url']) : '';
			if ( isset($_POST['disable_button'] ) ) {
				$view->person_details_button = $this->edit_person_button('disabled="disabled"');				
				$view->disable_button = $this->disable_button(TRUE);
			} else {
				$view->person_details_button = $this->edit_person_button();				
				$view->disable_button = $this->disable_button(FALSE);
			}							
			if(isset($_POST['person_id'])) $view->model->person_id = $_POST['person_id'];
			$this->template->content = $view;
		}
	}
}

?>
