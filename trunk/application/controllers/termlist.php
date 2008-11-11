<?php

class Termlist_Controller extends Indicia_Controller {
	public function __construct() {
		parent::__construct();
	}
	public function page($page_no,$limit) {
		$model = ORM::factory('termlist');
		// Generate a new termlist object
		$this->template->title = "Existing Termlists";
		$this->template->message = 'Termlists grid'; 
		$termlist = new View('termlist');
		$grid =	Gridview_Controller::factory($model,$page_no,$limit,3,null);
		$grid->base_filter = array('deleted' => 'f');
		array_splice($grid->columns,0,1);
		$termlist->termtable = $grid->display();
		$this->template->content = $termlist;

	}
	// Auxilliary function for handling Ajax requests from the page method gridview component
	public function page_gv($page_no,$limit) {
		$model = ORM::factory('termlist');
		$this->auto_render = false;
		$grid =	Gridview_Controller::factory($model,$page_no,$limit,3,null);
		$grid->base_filter = array('deleted' => 'f');
		array_splice($grid->columns,0,1);
		return $grid->display();
	}
	public function edit($id,$page_no,$limit) {
		$model = ORM::factory('termlist',$id);
		$this->template->title = "Create New Termlist";
		$view = new View('termlist_edit');
		$grid =	Gridview_Controller::factory($model,
				$page_no,
				$limit,
				4);
		$grid->base_filter = array('parent_id' => $id);
		array_splice($grid->columns,0,1);
		$view->termtable = $grid->display();
		$view->model = $model->find($id);
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
		array_splice($grid->columns,0,1);
		return $grid->display();
	}
	public function save() {
	}
	public function create(){
		$parent = $this->input->post('parent_id', null);
		$this->template->title = "Create new termlist";
		$view = new View('termlist_edit');

	}
}
