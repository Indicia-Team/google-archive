<?php defined('SYSPATH') or die('No direct script access.');

class Website_Model extends ORM {

	protected $has_many = array('termlist');

	public function validate(Validation $array, $save = FALSE) {
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$array->pre_filter('trim');
<<<<<<< .mine
		$array->add_rules('title', 'required', 'length[1,100]');
=======
		$array->add_rules('title', 'required', 'length[1,100]');
>>>>>>> .r52
		return parent::validate($array, $save);
	}
}

?>
