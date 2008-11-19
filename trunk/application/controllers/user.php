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
	public function create() {
		$model = ORM::factory('user');
		$view = new View('user/user_edit');
		$view->model = $model;
		$view->metadata = $this->GetMetadataView($model);
		$this->template->title = $this->GetEditPageTitle($model, 'User');
		$this->template->content = $view;
	}

	public function edit() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit user without an ID";
		else
		{
			$user = new User_Model($this->uri->argument(1));
			$view = new View('user/user_edit');
			$view->metadata = $this->GetMetadataView($user);
			$this->template->title = $this->GetEditPageTitle($user, 'User');
			$view->model = $user;
			$this->template->content = $view;
		}
	}

	public function save() {
		if ($_POST['submit'] == 'Cancel'){
			url::redirect('user');
		}
		if (! empty($_POST['id']))
			$user = new User_Model($_POST['id']);
		else
			$user = new User_Model();
		$_POST = new Validation($_POST);
		if ($user->validate($_POST, TRUE)) {
			url::redirect('user');
		} else {
			// errors are now embedded in the model
		    $view = new View('user/user_edit');
			$view->metadata = $this->GetMetadataView($user);
			$this->template->title = $this->GetEditPageTitle($user, 'User');
			$view->model = $user;
			$this->template->content = $view;
		}
	}
}

?>
