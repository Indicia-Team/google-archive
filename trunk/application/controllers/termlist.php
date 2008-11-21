<?php

class Termlist_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct('termlist','gv_termlist','termlist/index');
		$this->base_filter = array('deleted' => 'f');
		$this->columns = array(
			'title'=>'',
			'description'=>'',
			'website'=>''
			);
		$this->pagetitle = "Term lists";
	}
	public function edit($id,$page_no,$limit) {
		// Generate models
		$model = ORM::factory('termlist',$id);
		$gridmodel = ORM::factory('gv_termlist',$id);

		// Add grid component
		$grid =	Gridview_Controller::factory($gridmodel,
				$page_no,
				$limit,
				4);
		$grid->base_filter = $this->base_filter;
		$grid->base_filter['parent_id'] = $id;
		$grid->columns = array_intersect_key($grid->columns, array(
			'title'=>'',
			'description'=>''));
		$grid->actionColumns = array(
			'edit' => 'termlist/edit/$id£'
		);
		
		// Add metadata panel
		$metadata = new View('templates/metadata');
		$metadata->model = $model->find($id);
		
		// Add items to view
		$view = new View('termlist/termlist_edit');
		$view->model = $model->find($id);
		$view->metadata = $metadata;
		$view->table = $grid->display();

		// Add everything to the template
		$this->template->title = "Edit ".$model->title;
		$this->template->content = $view;

	}
	// Auxilliary function for handling Ajax requests from the edit method gridview component
	public function edit_gv($id,$page_no,$limit) {
		$this->auto_render=false;

		$model = ORM::factory('termlist',$id);
		$gridmodel = ORM::factory('gv_termlist',$id);

		$grid =	Gridview_Controller::factory($gridmodel,
				$page_no,
				$limit,
				4);
		$grid->base_filter = $this->base_filter;
		$grid->base_filter['parent_id'] = $id;
		$grid->columns = array_intersect_key($grid->columns, array(
			'title'=>'',
			'description'=>''));
		$grid->actionColumns = array(
			'edit' => 'termlist/edit/$id£'
		);
		return $grid->display();
	}
	public function save() {
		if (! empty($_POST['id'])) {
			$termlist = ORM::factory('termlist',$_POST['id']);
		} else {
			$termlist = ORM::factory('termlist');
		}
		/**
		 * We need to submit null for integer fields, because an empty string will fail.
		 */
		if ($_POST['parent_id'] == ''){
			$_POST['parent_id'] = null;
		}
		if ($_POST['website_id'] == ''){
			$_POST['website_id'] = null;
		}
		/**
		 * Were we instructed to delete the post?
		 */
		if ($_POST['submit'] == 'Delete'){
			$_POST['deleted'] = 'true';
		} else {
			$_POST['deleted'] = 'false';
		}
		$_POST = new Validation($_POST);
		if ($termlist->validate($_POST, true)) {
			url::redirect('termlist');
		} else {
			$this->template->title = "Edit ".$termlist->title;
			$metadata = new View('templates/metadata');
			$metadata->model = $termlist;
			$view = new View('termlist/termlist_edit');
			$view->metadata = $metadata;
			$view->model = $termlist;
			$view->table = null;
			$this->template->content = $view;
		}
	}
	public function create(){
		$parent = $this->input->post('parent_id', null);
		$metadata = new View('templates/metadata');
		$metadata->model = ORM::factory('termlist');

		// Create and assign variables to the view
		$view = new View('termlist/termlist_edit');
		$view->model = ORM::factory('termlist');
		$view->model->parent_id = $parent;
		$view->metadata = $metadata;
		$view->table = null;

		// Templating
		$this->template->title = "Create new termlist";
		$this->template->content = $view;
	}
}
?>
