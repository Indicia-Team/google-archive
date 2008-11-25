<?php

class Person_Controller extends Gridview_Base_Controller {

	public function __construct() {
		parent::__construct('person', 'gv_person', 'person/index');
		$this->columns = array(
			'first_name'=>''
			,'surname'=>''
			,'initials'=>''
			,'email_address'=>''
			,'username'=>''
			,'is_core_user'=>''
		);
		$this->pagetitle = "People";
	}

	protected function create_user_button($extras='')
	{
		return form::submit('submit', 'Create User Details', $extras);
	}
	
	protected function edit_user_button($extras='')
	{
		return form::submit('submit', 'Edit User Details', $extras);
	}
	
	protected function return_url($return_url)
	{
		return '<input type="hidden" name="return_url" id="return_url" value="'.html::specialchars($return_url).'" />';
	}

	protected function disable_button($disable_button)
	{
		return $disable_button ? '<input type="hidden" name="disable_button" id="disable_button" value="YES" />' : '';
	}
	
	/**
	 * Action for person/create page.
	 * Displays a page allowing entry of a new person.
	 */
	public function create() {
		$person = ORM::factory('person');
		$view = new View('person/person_edit');
		$view->model = $person;
		$view->metadata = $this->GetMetadataView($person);
		$this->template->title = $this->GetEditPageTitle($person, 'Person');
		$view->return_url = ''; // will jump back to the gridview on submit
		// There are issues with the ID not being backfilled into the model when a new record has been created.
		// For this reason the Create User details is disabled.
		$view->user_details_button = $this->create_user_button('disabled="disabled"');
		$view->disable_button = $this->disable_button(TRUE);
		$this->template->content = $view;
	}

	/**
	 * Action for person/edit page.
	 * Displays a page allowing modification of an existing person.
	 * This functrion is envoked in 2 different ways:
	 * 1) From the gridview
	 * 2) Direct URL
	 */
	public function edit() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit person without a Person ID";
		else
		{
			$person = new Person_Model($this->uri->argument(1));
			$view = new View('person/person_edit');
			$view->model = $person;
			$view->metadata = $this->GetMetadataView($person);
			$this->template->title = $this->GetEditPageTitle($person, 'Person');
			$view->return_url = ''; // will jump back to the gridview on submit
			$user = ORM::factory('user', array('person_id' => $person->id));
			$view->user_details_button = $user->loaded ? $this->edit_user_button() : $this->create_user_button();
			$view->disable_button = $this->disable_button(FALSE);
			$this->template->content = $view;
		}
	}

	/**
	 * Subsiduary Action for person/edit page.
	 * Displays a page allowing modification of an existing person.
	 * This is called from a User Record.
	 * When called from User we want:
	 * A) To return back to the User form on submission for that person
	 * B) We don't want to allow the drilling back to the user - ie we need to disable the relevant button.
	 */
	public function edit_from_user() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit person through edit_from_user() without a Person ID";
		else
		{
			$person = new Person_Model($this->uri->argument(1));
			$view = new View('person/person_edit');
			$view->model = $person;
			$view->metadata = $this->GetMetadataView($person);
			$this->template->title = $this->GetEditPageTitle($person, 'Person');
			$user = ORM::factory('user', array('person_id' => $person->id));
			$view->return_url = $user->loaded ? $this->return_url('user/edit/'.$user->id): '';
			$view->user_details_button = $this->edit_user_button('disabled="disabled"');				
			$view->disable_button = $this->disable_button(TRUE);
			$this->template->content = $view;
		}
	}

	
	public function save() {
		if (! empty($_POST['id']))
			$person = new Person_Model($_POST['id']);
		else
			$person = new Person_Model();
			
		$_POST = new Validation($_POST);
		if ($person->validate($_POST, TRUE)) {
			
			if(isset($_POST['return_url'])) 
				url::redirect($_POST['return_url']);
			else if ($_POST['submit'] == 'Edit User Details'){
				url::redirect('user/edit_from_person/'.$person->id);
			} else if ($_POST['submit'] == 'Create User Details'){
				url::redirect('user/create/'.$person->id);
			} else
				// For a successful submission, just redisplay the gridview
				url::redirect('person');
				
		} else {
			// errors are now embedded in the model
			$view = new View('person/person_edit');
			$view->model = $person;
			$view->metadata = $this->GetMetadataView($person);
			$this->template->title = $this->GetEditPageTitle($person, 'Person');
			$view->return_url = isset($_POST['return_url']) ? $this->return_url($_POST['return_url']) : '';
			if ( isset($_POST['disable_button'] ) ) {
				$extras='disabled="disabled"';
				$view->disable_button = $this->disable_button(TRUE);
			} else {
				$extras='';
				$view->disable_button = $this->disable_button(FALSE);
			}
			$view->user_details_button = $this->create_user_button($extras);				
			if ( isset($_POST['id'])) {
				$user = ORM::factory('user', array('person_id' => $person->id));
				if ( $user->loaded )
					$view->edit_user_button = $this->edit_user_button($extras);
			}
			$this->template->content = $view;
		}
	}

}

?>
