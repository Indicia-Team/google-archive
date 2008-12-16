<?php

class Taxon_list_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct('taxon_list','taxon_list','taxon_list/index');
		$this->base_filter = array('deleted' => 'f');
		$this->columns = array(
			'title'=>'',
			'description'=>'');
		$this->pagetitle = "Taxon lists";
		$this->model = ORM::factory('taxon_list');
		$this->auth_filter = $this->gen_auth_filter;
	}
	public function edit($id,$page_no,$limit) {
		$this->model->find($id);

		// Configure the grid
		$grid =	Gridview_Controller::factory($this->model,
				$page_no,
				$limit,
				4);
		$grid->base_filter = array('deleted' => 'f', 'parent_id' => $id);
		$grid->columns =  $this->columns;
		$grid->actionColumns = array(
			'edit' => 'taxon_list/edit/£id£'
		);

		$vArgs = array(
			'table' => $grid->display()
		);

		$this->setView('taxon_list/taxon_list_edit', 'Species List', $vArgs);
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
		$grid->actionColumns = array(
			'edit' => 'taxon_list/edit/£id£'
		);
		return $grid->display();
	}
	public function create(){
		$parent = $this->input->post('parent_id', null);
		$this->model->parent_id = $parent;
		if ($parent != null) $this->model->website_id = $this->model->parent->website_id;

		$this->setView('taxon_list/taxon_list_edit', 'Species List');
	}
}
?>
