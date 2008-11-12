<?php defined('SYSPATH') or die('No direct script access.');

class Website_Model extends ORM {
	private $errors = array();

	protected $has_many = array('termlists');

	public function validate() {
		// create a new Validation object using the as_array information
		$values = new Validation($this->as_array());
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$values->pre_filter('trim');
		$values->add_rules('title', 'required', 'length[1,100]', 'standard_text');
		$values->add_rules('description', 'standard_text');
		if ($values->validate()) {
			// put the trimmed and processed data back into the model
			$this->load_values($values->as_array());
			return TRUE;
		}
		else {
			$this->errors = $values->errors('form_error_messages');
			return FALSE;
		}
	}

	public function getError($fieldname) {
		if (array_key_exists($fieldname, $this->errors)) {
			return $this->errors[$fieldname];
		} else {
			return '';
		}
	}
}

?>
