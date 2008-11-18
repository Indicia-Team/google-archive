<?php

class Website_Controller extends Gridview_Base_Controller {

	public function __construct() {
		parent::__construct('website', 'website', 'website/index');
		$this->columns = array(
			'title'=>'',
			'description'=>'');
		$this->pagetitle = "Websites";
	}

	public function create() {
		$this->template->title = "Create New Website";
		$view = new View('website/website_edit');
		// Create a new website model to pass to the view
		$view->website = ORM::factory('website');
		$this->template->content = $view;
	}

	public function edit() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit website without an ID";
		else
		{
			$website = new Website_Model($this->uri->argument(1));
			$this->template->title = "Edit ".$website->title;
			$view = new View('website/website_edit');
			$view->website = $website;
			$this->template->content = $view;
		}
	}

	public function save() {
		if (! empty($_POST['id']))
			$website = new Website_Model($_POST['id']);
		else
			$website = new Website_Model();
		$_POST = new Validation($_POST);
		if ($website->validate($_POST, TRUE)) {
			url::redirect('website');
		} else {
			// errors are now embedded in the model
		    $this->template->title = "Edit ".$website->title;
			$view = new View('website/website_edit');
			$view->website = $website;
			$this->template->content = $view;
		}

	}

}

?>
