<?php

class Termlist_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct(ORM::factory('termlist'), new View('termlist'));
		$this->base_filter = array('deleted' => 'f');
		$this->columns = array(
			'title'=>'',
			'description'=>'');
		$this->pagetitle = "Term lists";
	}
	public function edit($id,$page_no,$limit) {
		$model = ORM::factory('termlist',$id);
		$this->template->title = "Edit ".$model->title;
		$view = new View('termlist_edit');
		$grid =	Gridview_Controller::factory($model,
				$page_no,
				$limit,
				4);
		$grid->base_filter = array('parent_id' => $id);
		$grid->columns = array_intersect_key($grid->columns, array(
			'title'=>'',
			'description'=>''));
		$view->table = $grid->display();
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
		}
		$_POST = new Validation($_POST);
		if ($termlist->validate($_POST, true)) {
			url::redirect('termlist');
		} else {
			$this->template->title = "Edit ".$termlist->title;
			$view = new View('termlist_edit');
			$view->termlist = $termlist;
			$this->template->content = $view;
		}
	}
	public function create(){
		$parent = $this->input->post('parent_id', null);
		$this->template->title = "Create new termlist";
		$view = new View('termlist_edit');
		$view->model = ORM::factory('termlist');
		$view->model->parent_id = $parent;
		$this->template->content = $view;
	}
}
?>
