<?php defined('SYSPATH') or die('No direct script access.');

class User_Model extends ORM {

	protected $belongs_to = array('person', 'core_role',
		'created_by'=>'user', 'updated_by'=>'user');
	protected $has_many = array(
		'termlist'=>'created_by','termlist'=>'updated_by',
		'website'=>'created_by','website'=>'updated_by',
		'location'=>'created_by','location'=>'updated_by',
		);

	public function validate(Validation $array, $save = FALSE) {
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$array->pre_filter('trim');
		
		$array->add_rules('username', 'required', 'length[5,30]');
		
		// Any fields that don't have a validation rule need to be copied into the model manually
		$this->interests = $array['interests'];
		$this->location_name = $array['location_name'];
		// only copy person id if it is filled in. This is to allow for case when called via
		// drill through from people.
		if (!empty($array['person_id'])) $this->person_id = $array['person_id'];
		// Checkboxes only appear in the POST array if they are checked, ie TRUE. Have to convert to PgSQL boolean values, rather than PHP
		$this->email_visible = (isset($array['email_visible']) ? 't' : 'f');
		$this->view_common_names = (isset($array['view_common_names']) ? 't' : 'f');
		    
		return parent::validate($array, $save);
	}

	/**
	 * Return a displayable caption for the item.
	 */
	public function caption()
	{
		return ($this->username);
	}
}
