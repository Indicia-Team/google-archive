<?php

class Indicia_Controller extends Template_Controller {
	// Template view name
	public $template = 'templates/template';

	public function __construct() {
		parent::__construct();

		$this->db = Database::instance();
		$this->auth = new Auth;
		$this->session = new Session;

		$this->template->menu = array
		(
			'Home' => array(),
			'Lookup Lists' => array(
				'Species Lists'=>'taxon_list',
				'Taxon Groups'=>'taxon_group',
				'Term Lists'=>'termlist',
				'Locations'=>'location',
				'Surveys'=>'survey',
				'People'=>'person',
				),
			'Custom Attributes' => array(
				'Occurrence Attributes'=>'occurrence_attribute',
				'Sample Attributes'=>'sample_attribute',
				'Location Attributes'=>'location_attribute',
				),
			'Admin' => array(
				'Users'=>'user',
				'Websites'=>'website',
				'Languages'=>'language',
				),
			'Me' => array(
				'Logout'=>'logout',
				),
		);
	}

	/**
	 * Retrieve a suitable title for the edit page, depending on whether it is a new record
	 * or an existing one.
	 */
	protected function GetEditPageTitle($model, $name) {
		if ($model->id)
			return "Edit $name ".$model->caption();
		else
			return "New $name";
	}

	/**
	 * Return the metadata sub-template for the edit page of any model.
	 */
	 protected function GetMetadataView($model) {
	 	$metadata = new View('templates/metadata');
		$metadata->model = $model;
		return $metadata;
	 }
	/**
	* set view
	*
	* @param string $name View name
	* @param string $pagetitle Page title
	*/	
	protected function setView( $name, $pagetitle = '', $viewArgs = array() ) {
		// on error rest on the website_edit page
        	// errors are now embedded in the model
	        $view                    = new View( $name );
	        $view->metadata          = $this->GetMetadataView(  $this->model );
	        $this->template->title   = $this->GetEditPageTitle( $this->model, $pagetitle );
		$view->model             = $this->model;

		foreach ($viewArgs as $arg => $val) {
			$view->set($arg, $val);
		}
	        $this->template->content = $view;
    }
}
