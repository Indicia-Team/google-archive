<?php

class Survey_Controller extends Gridview_Base_Controller {

	public function __construct() {
		parent::__construct('survey', 'gv_survey', 'survey/index');
		$this->columns = array(
			'title'=>'',
			'description'=>'',
			'website'=>'');
		$this->pagetitle = "Surveys";
		$this->model = ORM::factory('survey');
		$this->auth_filter = $this->gen_auth_filter;
	}

	/**
	 * Action for survey/create page.
	 * Displays a page allowing entry of a new survey.
	 */
	public function create() {
		$this->setView('survey/survey_edit', 'Survey');
	}

	public function edit() {
		if ($this->uri->total_arguments()==0)
			print "cannot edit survey without an ID";
		else
		{
			$survey = new Survey_Model($this->uri->argument(1));
			$this->template->title = $this->GetEditPageTitle($survey, 'Survey');
			$view = new View('survey/survey_edit');
			$view->model = $survey;
			$view->metadata = $this->GetMetadataView($survey);
			$this->template->content = $view;
		}
	}

	public function save() {
		if (! empty($_POST['id']))
			$survey = new Survey_Model($_POST['id']);
		else
			$survey = new Survey_Model();
		$_POST = new Validation($_POST);
		if ($survey->validate($_POST, TRUE)) {
			url::redirect('survey');
		} else {
			// errors are now embedded in the model
		    $this->template->title = $this->GetEditPageTitle($survey, 'Survey');
			$view = new View('survey/survey_edit');
			$view->model = $survey;
			$view->metadata = $this->GetMetadataView($survey);
			$this->template->content = $view;
		}

	}
}

?>
