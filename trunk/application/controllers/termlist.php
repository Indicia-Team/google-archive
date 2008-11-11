<?php

class Termlist_Controller extends Indicia_Controller {
	public function __construct() {
		parent::__construct();
	}
	public function page($page_no,$limit) {
		$model = ORM::factory('termlist');
		// Generate a new termlist object
		$this->template->title = "Pagination";
		$this->template->message = 'Termlists grid'; 
		$termlist = new View('termlist');
		$termlist->termtable = 
			Gridview_Controller::factory($model,$page_no,$limit,3,null)->display();
		$this->template->content = $termlist;

	}
	// Auxilliary function for handling Ajax requests from the page method gridview component
	public function page_gv($page_no,$limit) {
		$model = ORM::factory('termlist');
		$this->auto_render = false;
		return Gridview_Controller::factory($model,$page_no,$limit,3,null)->display();
	}
	public function edit($id,$page_no,$limit) {
		$model = ORM::factory('termlist',$id);
		$this->template->title = "Create New Termlist";
		$view = new View('termlist_new');
		$grid =	Gridview_Controller::factory($model,
				$page_no,
				$limit,
				4);
		$grid->base_filter = array('parent_id' => $id);
		$view->termtable = $grid->display();
		$view->model = $model;
		$this->template->content = $view;

	}
	// Auxilliary function for handling Ajax requests from the edit method gridview component
	public function edit_gv($id,$page_no,$limit) {
		$model = ORM::factory('termlist',$id);
		$this->auto_render=false;
		$grid =	Gridview_Controller::factory($model,
				$page_no,
				$limit,
				4);
		$grid->base_filter = array('parent_id' => $id);
		return $grid->display();
	}
	public function save() {
	}
}
