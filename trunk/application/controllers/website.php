<?php

class Website_Controller extends Gridview_Base_Controller {

	public function __construct() {
		parent::__construct('website', 'website', 'website/index');
		$this->columns = array(
			'title'=>'',
			'description'=>'');
		$this->pagetitle = "Websites";
	}

	/**
	 * Action for website/create page.
	 * Displays a page allowing entry of a new website.
	 */
	public function create() {
		$model = ORM::factory('website');
		$view = new View('website/website_edit');
		$view->model = $model;
		$view->metadata = $this->GetMetadataView($model);
		$this->template->title = $this->GetEditPageTitle($model, 'Website');
		$this->template->content = $view;
	}

	public function edit() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit website without an ID";
		else
		{
			$website = new Website_Model($this->uri->argument(1));
			$view = new View('website/website_edit');
			$view->metadata = $this->GetMetadataView($website);
			$this->template->title = $this->GetEditPageTitle($website, 'Website');
			$view->model = $website;
			$this->template->content = $view;
		}
	}

	public function save() {
		if (! empty($_POST['id']))
			$website = new Website_Model($_POST['id']);
		else
			$website = new Website_Model();
		$_POST = new Validation($_POST);
		if ($website->validate($_POST, TRUE)) {
			url::redirect('website');
		} else {
			// errors are now embedded in the model
		    $view = new View('website/website_edit');
			$view->metadata = $this->GetMetadataView($website);
			$this->template->title = $this->GetEditPageTitle($website, 'Website');
			$view->model = $website;
			$this->template->content = $view;
		}

	}

}

?>
