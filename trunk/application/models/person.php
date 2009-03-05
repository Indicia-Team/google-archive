<?php defined('SYSPATH') or die('No direct script access.');

class Person_Model extends ORM {

	protected $has_one = array('user');
	protected $belongs_to = array('created_by'=>'user', 'updated_by'=>'user', 'title');

	protected $search_field='surname';

	public function validate(Validation $array, $save = FALSE) {
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$array->pre_filter('trim');
		$array->add_rules('first_name', 'required', 'length[1,30]');
		$array->add_rules('surname', 'required', 'length[1,30]');
		if($array['email_address'] == NULL)
			$this->email_address = NULL;
		else
			$array->add_rules('email_address', 'email', 'length[1,50]', 'unique[people,email_address,'.$array->id.']');
        $array->add_rules('website_url', 'length[1,500]', 'url');
        // Any fields that don't have a validation rule need to be copied into the model manually
		if (isset($array['title_id'])) $this->title_id = (is_numeric ($array['title_id']) ? $array['title_id'] : NULL);
		$this->address = $array['address'];
		$this->initials = $array['initials'];

		return parent::validate($array, $save);
	}

	public function email_validate(Validation $array, $save = FALSE) {
		// this function is used when updating the email address through the new_password screen
		// This would happen on initial login for the admin user - the setup procedure does not include the email address for the 
		// the admin user
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$array->pre_filter('trim');
		$array->add_rules('email_address', 'required', 'email', 'length[1,50]', 'unique[people,email_address,'.$array->id.']');

		return parent::validate($array, $save);
	}
	
	public function preSubmit() {
		if (isset($this->submission['fields']['email_address']))
			if ($this->submission['fields']['email_address']['value'] == '')
				$this->submission['fields']['email_address']['value'] = NULL;
		if (isset($this->submission['fields']['title_id']))
			if (!is_numeric($this->submission['fields']['title_id']['value']))
				$this->submission['fields']['title_id']['value'] = NULL;
		return parent::preSubmit();
	}
	
	/**
	 * Return a displayable caption for the item.
	 * For People, this should be a combination of the Firstname and Surname.
	 */
	public function caption()
	{
		return ($this->first_name.' '.$this->surname);
	}
}
