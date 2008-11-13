<?php

class Termlist_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct(ORM::factory('gv_termlist'), new View('termlist'));
		$this->base_filter = array('deleted' => 'f');
		$this->columns = array(
			'title'=>'',
			'description'=>'',
			'website'=>'',
			'creator'=>'');
		$this->pagetitle = "Term lists";
	}
	public function edit($id,$page_no,$limit) {
		$model = ORM::factory('termlist',$id);
		$gridmodel = ORM::factory('gv_termlist',$id);
		$this->template->title = "Edit ".$model->title;
		$view = new View('termlist_edit');
		$metadata = new View('metadata');
		$grid =	Gridview_Controller::factory($gridmodel,
				$page_no,
				$limit,
				4);
		$grid->base_filter = array('parent_id' => $id);
		$grid->columns = array_intersect_key($grid->columns, array(
			'title'=>'',
			'description'=>''));
		$metadata->model = $model->find($id);
		$view->table = $grid->display();
		$view->model = $model->find($id);
		$view->metadata = $metadata;
		$this->template->content = $view;

	}
	// Auxilliary function for handling Ajax requests from the edit method gridview component
	public function edit_gv($id,$page_no,$limit) {
		$model = ORM::factory('termlist',$id);
		$gridmodel = ORM::factory('gv_termlist',$id);
		$this->auto_render=false;
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
			$view = new View('termlist_edit');
			$metadata = new View('metadata');
			$metadata->model = $termlist;
			$view->metadata = $metadata;
			$view->model = $termlist;
			$view->table = null;
			$this->template->content = $view;
		}
	}
	public function create(){
		$parent = $this->input->post('parent_id', null);
		$this->template->title = "Create new termlist";
		$view = new View('termlist_edit');
		$metadata = new View('metadata');
		$view->model = ORM::factory('termlist');
		$metadata->model = ORM::factory('termlist');
		$view->model->parent_id = $parent;
		$view->metadata = $metadata;
		$this->template->content = $view;
	}
}
?>
