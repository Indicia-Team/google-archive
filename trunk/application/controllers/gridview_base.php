<?php

abstract class Gridview_Base_Controller extends Indicia_Controller {
	public function __construct($model, View $view) {
		$this->model = $model;
		$this->pageNoUriSegment = 3;
		$this->base_filter = array();
		$this->columns = $model->table_columns;
		$this->pagetitle = "Abstract gridview class - override this title!";
		$this->view = $view;
		parent::__construct();
	}

	public function page($page_no, $limit) {
		$grid =	Gridview_Controller::factory($this->model,
			$page_no,
			$limit,
			$this->pageNoUriSegment);
		$grid->base_filter = $this->base_filter;
		$grid->columns = array_intersect_key($grid->columns, $this->columns);

		// Add table to view
		$this->view->table = $grid->display();

		// Templating
		$this->template->title = $this->pagetitle;
		$this->template->content = $this->view;
	}

	public function page_gv($page_no, $limit) {
		$this->auto_render = false;
		$grid =	Gridview_Controller::factory($this->model,
			$page_no,
			$limit,
			$this->pageNoUriSegment);
		$grid->base_filter = $this->base_filter;
		$grid->columns = array_intersect_key($grid->columns, $this->columns);
		return $grid->display();
	}
}
