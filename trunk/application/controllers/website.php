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
		$view = new View('website_new');
		$this->template->content = $view;
	}

}

?>
