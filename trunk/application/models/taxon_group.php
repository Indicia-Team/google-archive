<?php defined('SYSPATH') or die('No direct script access.');

class Taxon_Group_Model extends ORM {

	protected $has_many = array('term');

	public function validate(Validation $array, $save = FALSE) {
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$array->pre_filter('trim');
		$array->add_rules('title', 'required', 'length[1,100]');
		return parent::validate($array, $save);
	}
}

?>
