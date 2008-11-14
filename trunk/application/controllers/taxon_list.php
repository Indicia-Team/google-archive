<?php

class Taxon_list_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct(ORM::factory('taxon_list'), new View('taxon_list'));
		$this->base_filter = array('deleted' => 'f');
		$this->columns = array(
			'title'=>'',
			'description'=>'');
		$this->pagetitle = "Taxon lists";
	}
	public function edit($id,$page_no,$limit) {
		$model = ORM::factory('taxon_list',$id);
		$this->template->title = "Edit ".$model->title;
		$view = new View('taxon_list_edit');
		$metadata = new View('metadata');
		$grid =	Gridview_Controller::factory($model,
				$page_no,
				$limit,
				4);
		$grid->base_filter = array('deleted' => 'f', 'parent_id' => $id);
		$grid->columns =  $this->columns;
		$view->table = $grid->display();
		$metadata->model = $model->find($id);
		$view->model = $model->find($id);
		$view->metadata = $metadata;
		$this->template->content = $view;

	}
	// Auxilliary function for handling Ajax requests from the edit method gridview component
	public function edit_gv($id,$page_no,$limit) {
		$model = ORM::factory('taxon_list',$id);
		$this->auto_render=false;
		$grid =	Gridview_Controller::factory($model,
				$page_no,
				$limit,
				4);
		$grid->base_filter = array('deleted' => 'f', 'parent_id' => $id);
		$grid->columns = array_intersect_key($grid->columns, array(
			'title'=>'',
			'description'=>''));
		return $grid->display();
	}
	public function save() {
		if (! empty($_POST['id'])) {
			$taxon_list = ORM::factory('taxon_list',$_POST['id']);
		} else {
			$taxon_list = ORM::factory('taxon_list');
		}
		if ($_POST['parent_id'] == ''){
			$_POST['parent_id'] = null;
		}
		if ($_POST['website_id'] == ''){
			$_POST['website_id'] = null;
		}
		if ($_POST['submit'] == 'Delete'){
			$_POST['deleted'] = 'true';
		} else {
			$_POST['deleted'] = 'false';
		}
		$_POST = new Validation($_POST);
		if ($taxon_list->validate($_POST, true)) {
			url::redirect('taxon_list');
		} else {
			$this->template->title = "Edit ".$taxon_list->title;
			$view = new View('taxon_list_edit');
			$metadata = new View('metadata');
			$metadata->model = $taxon_list;
			$view->metadata = $metadata;
			$view->model = $taxon_list;
			$this->template->content = $view;
		}
	}
	public function create(){
		$parent = $this->input->post('parent_id', null);
		$this->template->title = "Create new taxon_list";
		$view = new View('taxon_list_edit');
		$metadata = new View('metadata');
		$metadata->model = ORM::factory('taxon_list');
		$view->metadata = $metadata;
		$view->model = ORM::factory('taxon_list');
		$view->model->parent_id = $parent;
		$this->template->content = $view;
	}
}
?>
