<?php

abstract class ORM extends ORM_Core {
	protected $errors = array();

	// The default field that is searchable is called title. Override this when a different field name is used.
	// Used to match against, for example when importing csv values.
	protected $search_field='title';


	/**
	 * Provide an accessor so that the view helper can retrieve the errors for the model by field name.
	 */
	public function getError($fieldname) {
		if (array_key_exists($fieldname, $this->errors)) {
			return $this->errors[$fieldname];
		} else {
			return '';
		}
	}

	/**
	 * Override the ORM validate method to store the validation errors in an array, making
	 * them accessible to the views.
	 */
	public function validate(Validation $array, $save = FALSE) {
		$this->set_metadata();
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

	/**
	 * For a model that is about to be saved, sets the metadata created and updated field values.
	 */
	public function set_metadata() {
		// Set up the created and updated metadata for the record
		if (!$this->id) {
			$this->created_on=date("Ymd H:i:s");
			$this->created_by_id = 1; // dummy user
		}
		// TODO: Check if updated metadata present in this entity, and also use correct user.
		$this->updated_on=date("Ymd H:i:s");
		$this->updated_by_id = 1; // dummy user
	}

	/**
	 * Do a default search for an item using the search_field setup for this model.
	 */
	public function lookup($search_text)
	{
		return $this->where($this->search_field, $search_text)->find();
	}

	/**
	 * Return a displayable caption for the item, defined as the content of the field with the
	 * same name as search_field.
	 */
	public function caption()
	{
		return $this->__get($this->search_field);
	}

}

?>
