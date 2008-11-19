<?php

class Person_Controller extends Gridview_Base_Controller {

	public function __construct() {
		parent::__construct('person', 'gv_person', 'person/index');
		$this->columns = array(
			'first_name'=>''
			,'surname'=>''
			,'initials'=>''
			,'email_address'=>''
			,'is_user'=>''
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
		$this->template->title = $this->GetEditPageTitle($model, 'Person');
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
			$this->template->title = $this->GetEditPageTitle($person, 'Person');
			$view->model = $person;
			$this->template->content = $view;
		}
	}

	public function save() {
		if ($_POST['submit'] == 'Cancel'){
			url::redirect('person');
		}
		if (! empty($_POST['id']))
			$person = new Person_Model($_POST['id']);
		else
			$person = new Person_Model();
		$_POST = new Validation($_POST);
		if ($person->validate($_POST, TRUE)) {
			url::redirect('person');
		} else {
			// errors are now embedded in the model
		    $view = new View('person/person_edit');
			$view->metadata = $this->GetMetadataView($person);
			$this->template->title = $this->GetEditPageTitle($person, 'Person');
			$view->model = $person;
			$this->template->content = $view;
		}
	}

}

?>
