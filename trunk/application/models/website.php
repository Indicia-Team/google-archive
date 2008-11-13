<?php defined('SYSPATH') or die('No direct script access.');

class Website_Model extends ORM {

	protected $has_many = array('termlist');
	protected $belongs_to = array('created_by'=>'user', 'updated_by'=>'user');
	protected $has_and_belongs_to_many = array('locations');

	public function validate(Validation $array, $save = FALSE) {
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$array->pre_filter('trim');
		$array->add_rules('title', 'required', 'length[1,100]');
		// Any fields that don't have a validation rule need to be copied into the model manually
		$this->description = $array['description'];
		return parent::validate($array, $save);
	}
}

?>
