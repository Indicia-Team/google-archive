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

	/**
	 * Action for website/create page.
	 * Displays a page allowing entry of a new website.
	 */
//	public function create() {
//		$model = ORM::factory('user');
//		$view = new View('user/user_edit');
//		$view->model = $model;
//		$view->metadata = $this->GetMetadataView($model);
//		$this->template->title = $this->GetEditPageTitle($model, 'User');
//		$this->template->content = $view;
//	}

	public function edit() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit user without an ID";
		else
		{
			$user = new User_Model($this->uri->argument(1));
			$view = new View('user/user_edit');
			$view->metadata = $this->GetMetadataView($user);
			$this->template->title = $this->GetEditPageTitle_local($user, 'User');
			$view->model = $user;
			$view->return_url = '';
			$this->template->content = $view;
		}
	}
	
	public function pedit() {
		if ($this->uri->total_arguments()==0)
			print "cannot use pedit user without an ID";
		else
		{
			$user = new User_Model(array('person_id' => $this->uri->argument(1)));
			$view = new View('user/user_edit');
			$view->metadata = $this->GetMetadataView($user);
			$this->template->title = $this->GetEditPageTitle_local($user, 'User');
			$view->model = $user;
			// when called from Person need to embed the person ID and the returning URL
			// into the form so they are available when changes are submitted succesfully.
			// When this is the case, they fields are hidden, and the button to edit the
			// person details is disabled.
			$view->model->person_id = $this->uri->argument(1);
			$view->return_url = 'person/edit/'.$this->uri->argument(1);
			$this->template->content = $view;
		}
	}
	
	public function save() {
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
				url::redirect('person/uedit/'.$user->person_id);
			} else {
				// For a successful submission, just redisplay the gridview
				url::redirect('user');
			}
				
		} else {
			// errors are now embedded in the model
		    $view = new View('user/user_edit');
			$view->metadata = $this->GetMetadataView($user);
			$this->template->title = $this->GetEditPageTitle_local($user, 'User');
			$view->model = $user;
			if(empty($_POST['return_url'])) 
				$view->return_url = '';
			else
			{
			// when called from Person need to embed the person ID and the returning URL
			// into the form so they are available when changes are submitted succesfully.
				$view->model->person_id = $_POST['person_id'];
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
