<?php

class Taxon_Group_Controller extends Indicia_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function page($page_no,$limit) {
		$model = ORM::factory('taxon_group');
		// Generate a new taxon_group object
		$this->template->title = "Taxon groups";
		$this->template->message = 'Taxon groups grid';
		$taxon_group = new View('taxon_group');
		$grid = Gridview_Controller::factory($model,$page_no,$limit,3,null);
		// Hide the first (id) column
		array_splice($grid->columns,0,1);
        $taxon_group->table = $grid->display();
		$this->template->content = $taxon_group;
	}

	// Auxilliary function for handling Ajax requests from the page method gridview component
	public function page_gv($page_no,$limit) {
		$model = ORM::factory('taxon_group');
		$this->auto_render = false;
		return Gridview_Controller::factory($model,$page_no,$limit,3,null)->display();
	}

	public function create() {
		$this->template->title = "Create New Taxon Group";
		$view = new View('taxon_group_edit');
		// Create a new taxon_group model to pass to the view
		$view->taxon_group = ORM::factory('taxon_group');
		$this->template->content = $view;
	}

	public function edit() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit taxon group without an ID";
		else
		{
			$taxon_group = new Taxon_Group_Model($this->uri->argument(1));
			$this->template->title = "Edit ".$taxon_group->title;
			$view = new View('taxon_group_edit');
			$view->taxon_group = $taxon_group;
			$this->template->content = $view;
		}
	}

	public function save() {
		if (! empty($_POST['id']))
			$taxon_group = new Taxon_Group_Model($_POST['id']);
		else
			$taxon_group = new Taxon_Group_Model();
		$_POST = new Validation($_POST);
		if ($taxon_group->validate($_POST, TRUE)) {
			url::redirect('taxon_group');
		} else {
			// errors are now embedded in the model
		    $this->template->title = "Edit ".$taxon_group->title;
			$view = new View('taxon_group_edit');
			$view->taxon_group = $taxon_group;
			$this->template->content = $view;
		}

	}

}

?>
