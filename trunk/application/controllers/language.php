<?php

class Language_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct('language', 'language', 'language/index');
		$this->columns = array(
			'iso'=>'',
			'language'=>'');
		$this->pagetitle = "Languages";
		$this->model = ORM::factory('language');
	}

	/**
	 * Action for language/create page/
	 * Displays a page allowing entry of a new language.
	 */
	public function create(){
		$model = ORM::factory('language');
		$view = new View('language/language_edit');
		$view->model = $model;
		$view->metadata = $this->GetMetadataView($model);
		$this->template->title = $this->GetEditPageTitle($model, 'Language');
		$this->template->content = $view;
	}

	public function edit($id) {
		$model = ORM::factory('language',$id);

		// Configure and assign variables to the view
		$view = new View('language/language_edit');
		$view->model = $model->find($id);

		// Templating
		$view->metadata = $this->GetMetadataView($model);
		$this->template->title = $this->GetEditPageTitle($model, 'Language');
		$this->template->content = $view;

	}
}
?>
