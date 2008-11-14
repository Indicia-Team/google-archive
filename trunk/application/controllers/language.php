<?php

class Language_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct(ORM::factory('language'), new View('language'));
		$this->columns = array(
			'iso'=>'',
			'language'=>'');
		$this->pagetitle = "Languages";
	}
	public function edit($id) {
		$model = ORM::factory('language',$id);

		// Configure the metadata panel
		$metadata = new View('metadata');
		$metadata->model = $model->find($id);

		// Configure and assign variables to the view
		$view = new View('language_edit');
		$view->model = $model->find($id);

		// Templating
		$view->metadata = $metadata;
		$this->template->title = "Edit ".$model->language;
		$this->template->content = $view;

	}

	public function save() {
		if (! empty($_POST['id'])) {
			$language = ORM::factory('language',$_POST['id']);
		} else {
			$language = ORM::factory('language');
		}
		if ($_POST['submit'] == 'Delete'){
			$_POST['deleted'] = 'true';
		} else {
			$_POST['deleted'] = 'false';
		}
		$_POST = new Validation($_POST);
		if ($language->validate($_POST, true)) {
			url::redirect('language');
		} else {
			$metadata = new View('metadata');
			$metadata->model = $language;

			$view = new View('language_edit');
			$view->metadata = $metadata;
			$view->model = $language;

			$this->template->title = "Edit ".$language->language;
			$this->template->content = $view;
		}
	}
	public function create(){
		$metadata = new View('metadata');
		$metadata->model = ORM::factory('language');
		$view = new View('language_edit');
		$view->metadata = $metadata;
		$view->model = ORM::factory('language');
		$this->template->title = "Create new language";
		$this->template->content = $view;
	}
}
?>
