<?php defined('SYSPATH') or die('No direct script access.');

class Person_Model extends ORM {

	protected $has_one = array('user');
	protected $belongs_to = array('created_by'=>'user', 'updated_by'=>'user');

	protected $search_field='surname';

	public function validate(Validation $array, $save = FALSE) {
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$array->pre_filter('trim');
		$array->add_rules('surname', 'required', 'length[1,30]');
		$array->add_rules('email_address', 'email', 'length[1,50]');
		// Any fields that don't have a validation rule need to be copied into the model manually
		$this->first_name = $array['first_name'];
		$this->initials = $array['initials'];
		$this->website_url = $array['website_url'];
		return parent::validate($array, $save);
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
