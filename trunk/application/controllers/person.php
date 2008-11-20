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

	/**
	 * Action for website/create page.
	 * Displays a page allowing entry of a new website.
	 */
	public function create() {
		$model = ORM::factory('person');
		$view = new View('person/person_edit');
		$view->model = $model;
		$view->metadata = $this->GetMetadataView($model);
		$view->return_url = '';
		$view->enable_create_button = 'YES';
		$this->template->title = $this->GetEditPageTitle_local($model, 'Person');
		$this->template->content = $view;
	}

	public function edit() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit person without an ID";
		else
		{
			$person = new Person_Model($this->uri->argument(1));
			$view = new View('person/person_edit');
			$view->metadata = $this->GetMetadataView($person);
			$this->template->title = $this->GetEditPageTitle_local($person, 'Person');
			$view->model = $person;
			$view->return_url = '';
			$user = ORM::factory('user', array('person_id' => $person->id));
			if ( $user->loaded )
				$view->enable_edit_button = 'YES';
			else
				$view->enable_create_button = 'YES';
			$this->template->content = $view;
		}
	}

	public function uedit() {
		if ($this->uri->total_arguments()==0)
			print "cannot use uedit person without an ID";
		else
		{
			$person = new Person_Model($this->uri->argument(1));
			$view = new View('person/person_edit');
			$view->metadata = $this->GetMetadataView($person);
			$this->template->title = $this->GetEditPageTitle_local($person, 'Person');
			$view->model = $person;
			// when called from User need to embed the returning URL
			// into the form so it is available when changes are submitted succesfully.
			// When this is the case, the fields are hidden, and the buttons to edit or create
			// the user details are disabled.
			$view->return_url = 'user/edit/'.$this->uri->argument(1);
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
			if(!empty($_POST['return_url'])) 
				url::redirect($_POST['return_url']);
			else if ($_POST['submit'] != 'Submit'){
				// one of the other buttons has been pressed, either to create new or edit existing user details for this person.
				if ( ! $person->id )
				{
				// if we've got here the process it insert a new person has not filled in the id into the model.
				// in this case we assume that we loaded the last person, ie the one with the highest ID.
					$result=$this->db->query('select max(id) from people');
					$person->id = $result[0]->max;
				}
				url::redirect('user/pedit/'.$person->id);
			} else
				// For a successful submission, just redisplay the gridview
				url::redirect('person');
		} else {
			// errors are now embedded in the model
		    $view = new View('person/person_edit');
			$view->metadata = $this->GetMetadataView($person);
			$this->template->title = $this->GetEditPageTitle_local($person, 'Person');
			$view->model = $person;
			if(empty($_POST['return_url']))
			{
				$view->return_url = '';
				if (! empty($_POST['id'])) {
					$user = ORM::factory('user', array('person_id' => $_POST['id']));
					if ( $user->loaded )
						$view->enable_edit_button = 'YES';
					else
						$view->enable_create_button = 'YES';
				} else {	
					$view->enable_create_button = 'YES';
				}
			}
			else
			{
			// when called from User need to embed the returning URL
			// into the form so it is available when changes are submitted succesfully.
			// When this is the case, the fields are hidden, and the buttons to edit or create
			// the user details are disabled.
				$view->return_url = $_POST['return_url'];
			}		
			$this->template->content = $view;
		}
	}

	/**
	 * Retrieve a suitable title for the gridview page.
	 * This overrides a function in indicia.php (name is misleading as its not used in the edit page...)
	 */
	protected function GetEditPageTitle($model, $name) {
		return "View $name";
	}

	/**
	 * Retrieve a suitable title for the edit page, depending on whether it is a new record
	 * or an existing one.
	 */
	protected function GetEditPageTitle_local($model, $name) {
		if ($model->id)
			return "Edit $name ".$model->caption();
		else
			return "New $name";
	}
}

?>
