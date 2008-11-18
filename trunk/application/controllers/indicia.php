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
		);
	}
}
