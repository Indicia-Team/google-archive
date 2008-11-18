<?php

class Taxon_Group_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct('taxon_group', 'taxon_group', 'taxon_group/index');
		$this->columns = array(
			'title'=>'');
		$this->pagetitle = "Taxon Groups";
		$this->session = Session::instance();
	}

	/**
	 * Action for taxon_group/create page/
	 * Displays a page allowing entry of a new taxon group.
	 */
	public function create() {
		$model = ORM::factory('taxon_group');
		$view = new View('taxon_group/taxon_group_edit');
		$view->model = $model;
		$view->metadata = $this->GetMetadataView($model);
		$this->template->title = $this->GetEditPageTitle($model, 'Taxon Group');
		$this->template->content = $view;
	}

	public function edit() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit taxon group without an ID";
		else
		{
			$taxon_group = new Taxon_Group_Model($this->uri->argument(1));
			$this->template->title = $this->GetEditPageTitle($taxon_group, 'Taxon Group');
			$view = new View('taxon_group/taxon_group_edit');
			$view->model = $taxon_group;
			$view->metadata = $this->GetMetadataView($taxon_group);
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
			$view = new View('taxon_group/taxon_group_edit');
			$view->model = $taxon_group;
			$this->template->content = $view;
		}

	}

}

?>
