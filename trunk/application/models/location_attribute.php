<?php defined('SYSPATH') or die('No direct script access.');

class Location_Attribute_Model extends ORM {

	protected $belongs_to = array('created_by'=>'user', 'updated_by'=>'user', 'termlist');

	protected $has_many = array(
		'location_attributes_values',
		);

	protected $has_and_belongs_to_many = array('websites');

	protected $search_field='caption';

	public function validate(Validation $array, $save = FALSE) {
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$array->pre_filter('trim');
		return parent::validate($array, $save);
	}

}
