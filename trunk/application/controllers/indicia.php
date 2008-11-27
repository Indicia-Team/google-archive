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
	  * Return the metadata sub-template for the edit page of any model. Returns nothing
	  * if there is no ID (so no metadata).
	  */
	 protected function GetMetadataView($model) {
	 	if ($this->model->id) {
		 	$metadata = new View('templates/metadata');
			$metadata->model = $model;
			return $metadata;
	 	} else {
	 		return '';
	 	}
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

	/**
	 * Wraps a standard $_POST type array into a save array suitable for use in saving
	 * records.
	 *
	 * @param array $array Array to wrap
	 * @param bool $fkLink=false Link foreign keys?
	 *
	 * @return array Wrapped array
	 */
	protected function wrap( $array, $fkLink = false, $id = null) {
		if ($id == null) $id = $this->model->object_name;
		// Initialise the wrapped array
		$sa = array(
			'id' => $id,
			'fields' => array(),
			'fkFields' => array(),
			'subModels' => array()
		);

		// Iterate through the array
		foreach ($array as $a => $b) {
			// Check whether this is a fk placeholder
			if (substr($a,0,3) == 'fk_'
				&& $fkLink) {
					// Generate a foreign key instance
					$sa['fkFields'][$a] = array(
						// Foreign key id field is table_id
						'fkIdField' => substr($a,3)."_id",
						'fkTable' => substr($a,3),
						'fkSearchField' =>
						ORM::factory(substr($a,3))->get_search_field(),
						'fkSearchValue' => $b);
				} else {
					// This should be a field in the model.
					// Add a new field to the save array
					$sa['fields'][$a] = array(
						// Set the value
						'value' => $b);
				}
		}

		return $sa;
	}

	/**
	 * Sets the model submission, saves the submission array.
	 */
	protected function submit($submission){
		$this->model->submission = $submission;
		if (($id = $this->model->submit()) != null) {
			// Record has saved correctly
			$this->submit_succ($id);
		} else {
			// Record has errors - now embedded in model
			$this->submit_fail();
		}
	}

	/**
	 * Returns to the index view for this controller.
	 */
	protected function submit_succ($id) {
		syslog(LOG_DEBUG, "Submitted record ".$id." successfully.");
		url::redirect($this->model->object_name);
	}

	/**
	 * Returns to the edit page to correct errors - now embedded in the model
	 */
	protected function submit_fail() {
		$mn = $this->model->object_name;
		$this->setView($mn."/".$mn."_edit", ucfirst($mn));
	}


	/**
	 * Saves the post array by wrapping it and then submitting it.
	 */
	public function save(){
		if (! empty($_POST['id'])) {
			$this->model = ORM::factory($this->model->object_name, $_POST['id']);
		}

		/**
		 * Were we instructed to delete the post?
		 */
		if ($_POST['submit'] == 'Delete'){
			$_POST['deleted'] = 't';
		} else {
			$_POST['deleted'] = 'f';
		}

		// Wrap the post object and then submit it
		$this->submit($this->wrap($_POST));

	}

}
