<?php

class Website_Controller extends Indicia_Controller {
	public function __construct() {
		parent::__construct();
	}
	public function page($page_no,$limit) {
		$model = ORM::factory('website');
		// Generate a new termlist object
		$this->template->title = "Websites";
		$this->template->message = 'Websites grid';
		$website = new View('website');
		$grid = Gridview_Controller::factory($model,$page_no,$limit,3,null);
		// Hide the first (id) column
		array_splice($grid->columns,0,1);
        $website->table = $grid->display();
		$this->template->content = $website;
	}

	// Auxilliary function for handling Ajax requests from the page method gridview component
	public function page_gv($page_no,$limit) {
		$model = ORM::factory('website');
		$this->auto_render = false;
		return Gridview_Controller::factory($model,$page_no,$limit,3,null)->display();
	}

	public function create() {
		$this->template->title = "Create New Website";
		$view = new View('website_edit');
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
			$view = new View('website_edit');
			$view->website = $website;
			$this->template->content = $view;
		}
	}

	public function save() {
		if (! empty($_POST['id']))
			$website = new Website_Model($_POST['id']);
		else
			$website = new Website_Model();
		$website->title = $_POST['title'];
		$website->description = $_POST['description'];
		if ($website->validate()) {
			$website->save();
			url::redirect('website');
		} else {
			// errors are now embedded in the model
		    $this->template->title = "Edit ".$website->title;
			$view = new View('website_edit');
			$view->website = $website;
			$this->template->content = $view;
		}

	}

}

?>
