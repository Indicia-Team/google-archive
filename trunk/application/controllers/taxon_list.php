<?php

class Taxon_list_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct('taxon_list','taxon_list','taxon_list/index');
		$this->base_filter = array('deleted' => 'f');
		$this->columns = array(
			'title'=>'',
			'description'=>'');
		$this->pagetitle = "Taxon lists";
	}
	public function edit($id,$page_no,$limit) {
		$model = ORM::factory('taxon_list',$id);

		// Configure the grid
		$grid =	Gridview_Controller::factory($model,
				$page_no,
				$limit,
				4);
		$grid->base_filter = array('deleted' => 'f', 'parent_id' => $id);
		$grid->columns =  $this->columns;

		// Configure the metadata panel
		$metadata = new View('templates/metadata');
		$metadata->model = $model->find($id);

		// Configure and assign variables to the view
		$view = new View('taxon_list/taxon_list_edit');
		$view->table = $grid->display();
		$view->model = $model->find($id);

		// Templating
		$view->metadata = $metadata;
		$this->template->title = "Edit ".$model->title;
		$this->template->content = $view;

	}
	// Auxilliary function for handling Ajax requests from the edit method gridview component
	public function edit_gv($id,$page_no,$limit) {
		$this->auto_render=false;
		$model = ORM::factory('taxon_list',$id);
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
			$metadata = new View('templates/metadata');
			$metadata->model = $taxon_list;

			$view = new View('taxon_list/taxon_list_edit');
			$view->metadata = $metadata;
			$view->model = $taxon_list;

			$this->template->title = "Edit ".$taxon_list->title;
			$this->template->content = $view;
		}
	}
	public function create(){
		$parent = $this->input->post('parent_id', null);
		$metadata = new View('templates/metadata');
		$metadata->model = ORM::factory('taxon_list');
		$view = new View('taxon_list/taxon_list_edit');
		$view->metadata = $metadata;
		$view->model = ORM::factory('taxon_list');
		$view->model->parent_id = $parent;
		$this->template->title = "Create new taxon_list";
		$this->template->content = $view;
	}
}
?>
