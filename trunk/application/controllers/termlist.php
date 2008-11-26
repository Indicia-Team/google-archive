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
		$this->model = ORM::factory('termlist');
	}
	public function edit($id,$page_no,$limit) {

		if ($id == null) {
			print "Cannot edit a termlist without an id";
		} else {
			// Generate models
			$this->model->find($id);
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
				'edit' => 'termlist/edit/£id£'
			);

			$vArgs = array('table' => $grid->display());

			$this->setView('termlist/termlist_edit', 'Termlist', $vArgs);
		}
	}
	// Auxilliary function for handling Ajax requests from the edit method gridview component
	public function edit_gv($id,$page_no,$limit) {
		$this->auto_render=false;

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
	public function create(){
		$parent = $this->input->post('parent_id', null);
		$this->model->parent_id = $parent;
		if ($parent != null) $this->model->website_id = $this->model->parent->website_id;

		$vArgs = array('table' => null);
		$this->setView('termlist/termlist_edit', 'Termlist');
	}
}
?>
