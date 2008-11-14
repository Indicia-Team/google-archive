<?php

class term_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct(ORM::factory('gv_term'), new View('term'));
		$this->base_filter = array(
			'deleted' => 'f',
			'parent_id' => null,
			'preferred' => 't');
		$this->columns = array(
			'term'=>'',
			'language'=>'');
		$this->pagetitle = "Terms";
	}
	/**
	 * Override the default page functionality to filter by termlist.
	 */
	public function page($termlist_id, $page_no, $limit){
		$this->base_filter['termlist_id'] = $termlist_id;
		$this->pagetitle = "Terms in ".ORM::factory('termlist',$termlist_id)->title;
		parent::page($page_no, $limit);
	}

	public function page_gv($termlist_id, $page_no, $limit){
		$this->base_filter['termlist_id'] = $termlist_id;
	}

	public function edit($id,$page_no,$limit) {
		// Generate models
		$model = ORM::factory('term',$id);
		$gridmodel = ORM::factory('gv_term',$id);

		// Add grid component
		$grid =	Gridview_Controller::factory($gridmodel,
				$page_no,
				$limit,
				4);
		$grid->base_filter = $this->base_filter;
		$grid->columns = $this->columns;
		
		// Add metadata panel
		$metadata = new View('metadata');
		$metadata->model = $model->find($id);
		
		// Add items to view
		$view = new View('term_edit');
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

		$model = ORM::factory('term',$id);
		$gridmodel = ORM::factory('gv_term',$id);

		$grid =	Gridview_Controller::factory($gridmodel,
				$page_no,
				$limit,
				4);
		$grid->base_filter = array('parent_id' => $id);
		$grid->columns = array_intersect_key($grid->columns, array(
			'title'=>'',
			'description'=>''));
		return $grid->display();
	}
	public function save() {
		if (! empty($_POST['id'])) {
			$term = ORM::factory('term',$_POST['id']);
		} else {
			$term = ORM::factory('term');
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
		if ($term->validate($_POST, true)) {
			url::redirect('term');
		} else {
			$this->template->title = "Edit ".$term->title;
			$metadata = new View('metadata');
			$metadata->model = $term;
			$view = new View('term_edit');
			$view->metadata = $metadata;
			$view->model = $term;
			$view->table = null;
			$this->template->content = $view;
		}
	}
	public function create(){
		$parent = $this->input->post('parent_id', null);
		$metadata = new View('metadata');
		$metadata->model = ORM::factory('term');

		// Create and assign variables to the view
		$view = new View('term_edit');
		$view->model = ORM::factory('term');
		$view->model->parent_id = $parent;
		$view->metadata = $metadata;

		// Templating
		$this->template->title = "Create new term";
		$this->template->content = $view;
	}
}
?>
