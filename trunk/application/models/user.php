<?php defined('SYSPATH') or die('No direct script access.');

class User_Model extends ORM {

	protected $belongs_to = array('person', 'core_role',
		'created_by'=>'user', 'updated_by'=>'user');
	protected $has_many = array(
		'termlist'=>'created_by','termlist'=>'updated_by',
		'website'=>'created_by','website'=>'updated_by',
		'location'=>'created_by','location'=>'updated_by',
		);

	protected $search_field='username';

	public function validate(Validation $array, $save = FALSE) {
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		// Any fields that don't have a validation rule need to be copied into the model manually
		// note that some of the fields are optional.
		// Checkboxes only appear in the POST array if they are checked, ie TRUE. Have to convert to PgSQL boolean values, rather than PHP
		$array->pre_filter('trim');

		$array->add_rules('username', 'required', 'length[7,30]');
		if (isset($array['password'])) $array->add_rules('password', 'required', 'length[7,30]');
		$this->interests = $array['interests'];
		$this->location_name = $array['location_name'];
		if (isset($array['core_role_id'])) $this->core_role_id = (is_numeric ($array['core_role_id']) ? $array['core_role_id'] : NULL);
		$this->email_visible = (isset($array['email_visible']) ? 't' : 'f');
		$this->view_common_names = (isset($array['view_common_names']) ? 't' : 'f');
		if (isset($array['person_id'])) $this->person_id = $array['person_id'];

		return parent::validate($array, $save);
	}

	public function presubmit() {
		if (!is_numeric($this->submission['fields']['core_role_id']['value']))
			$this->submission['fields']['core_role_id']['value'] = NULL;
		return parent::presubmit();
	}
	
	public function password_validate(Validation $array, $save = FALSE) {
		$array->pre_filter('trim');
		$array->add_rules('password', 'required', 'length[7,30]', 'matches[password2]');
		$this->forgotten_password_key = NULL;
		 
		return parent::validate($array, $save);
	}
	
	public function __set($key, $value)
	{
		if ($key === 'password')
		{
			// Use Auth to hash the password
			$value = Auth::instance()->hash_password($value);			
		}

		parent::__set($key, $value);
	}
	
}
