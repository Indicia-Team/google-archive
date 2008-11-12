<?php

abstract class ORM extends ORM_Core {
	protected $errors = array();

	/* Provide an accessor so that the view helper can retrieve the errors for the model
	 * by field name
	 */
	public function getError($fieldname) {
		if (array_key_exists($fieldname, $this->errors)) {
			return $this->errors[$fieldname];
		} else {
			return '';
		}
	}

	/* Override the ORM validate method to store the validation errors
	 * in an array, making them accessible to the views.
	 */
	public function validate(Validation $array, $save = FALSE) {
		// Set up the created and updated metadata for the record
		if (!$this->id) {
			$this->created_on=date("Ymd H:i:s");
			$this->created_by = 1; // dummy user
		}
		// TODO: Check if updated metadata present in this entity, and also use correct user.
		$this->updated_on=date("Ymd H:i:s");
		$this->updated_by = 1; // dummy user
		if (parent::validate($array, $save)) {
			return TRUE;
		}
		else {
			// put the trimmed and processed data back into the model
			$this->load_values($array->as_array());
			$this->errors = $array->errors('form_error_messages');
			return FALSE;
		}
	}
}

?>
