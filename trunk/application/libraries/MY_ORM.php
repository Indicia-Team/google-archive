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
