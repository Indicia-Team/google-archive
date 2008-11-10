<?php

class Termlist_Controller extends Opal_Controller {
	public function __construct() {
		parent::__construct();
	}
	public function index(){
		url::redirect('termlist/page/1/5');
	}
	public function page($page_no,$limit) {
		$model = ORM::factory('termlist');
		// Generate a new termlist object
		$this->template->title = "Pagination";
		$this->template->message = 'Termlists grid'; 
		$termlist = new View('termlist');
		$termlist->termtable = 
			Gridview_Controller::factory($model,$page_no,$limit,3)->display();
		$this->template->content = $termlist;

	}
	// Auxilliary function for handling Ajax requests from the page method gridview component
	public function page_gv($page_no,$limit) {
		$model = ORM::factory('termlist');
		$this->auto_render = false;
		return Gridview_Controller::factory($model,$page_no,$limit,3)->display();
	}
	public function edit($id,$page_no,$limit) {
		$model = ORM::factory('termlist',$id);
		$this->template->title = "Create New Termlist";
		$view = new View('termlist_new');
		$view->termtable =
			Gridview_Controller::factory($model->children->current(),$page_no,$limit,4)->display();
		$view->model = $model;
		$this->template->content = $view;

	}
	// Auxilliary function for handling Ajax requests from the edit method gridview component
	public function edit_gv($id,$page_no,$limit) {
	}
}
