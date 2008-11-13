<?php defined('SYSPATH') or die('No direct script access.');

class User_Model extends ORM {

	protected $belongs_to = array('person');
	protected $has_many = array('termlist'=>'created_by');

}
