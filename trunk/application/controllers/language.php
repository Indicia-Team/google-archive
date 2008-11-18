<?php

class Language_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct('language', 'language', 'language/index');
		$this->columns = array(
			'iso'=>'',
			'language'=>'');
		$this->pagetitle = "Languages";
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
			$view = new View('language/language_edit');
			$view->model = $language;
			$view->metadata = $this->GetMetadataView($language);
			$this->template->title = $this->GetEditPageTitle($language, 'Language');
			$this->template->content = $view;
		}
	}
}
?>
