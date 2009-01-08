<?php defined('SYSPATH') or die('No direct script access.');

class Occurrence_Attribute_Value_Model extends ORM {

	protected $belongs_to = array('created_by'=>'user', 'updated_by'=>'user', 'occurrence', 'ccurrence_attribute');

	protected $search_field='text_value';

	public function validate(Validation $array, $save = FALSE) {
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$array->pre_filter('trim');
		$array->add_rules('occurrence_attribute_id', 'required');
		$array->add_rules('occurrence_id', 'required');

		// We apply the validation rules specified in the occurrence attribute
		// table to the value given.
		if (array_key_exists('occurrence_attribute_id', $array->as_array())) {
			$id = $array->as_array();
			$id = $id['occurrence_attribute_id'];
			$oam = ORM::factory('occurrence_attribute', $id);
			switch ($oam->data_type) {
			case 'T':
			$vf = 'text_value';
			break;
			case 'F':
			$vf = 'float_value';
			break;
			case 'D':
			$vf = 'date_value';
			break;
			case 'V':
			// Vague date - presumably already validated?
			$vf = 'date_value';
			break;
			default:
			$vf = 'int_value';
			}
			// Require the field with the value in
			$array->add_rules($vf, 'required');
			// Now get the custom attributes
			$rules = explode(',', $oam->validation_rules);
			foreach ($rules as $a){
				$array->add_rules($vf, $a);
			}
		}
		return parent::validate($array, $save);
	}

}
