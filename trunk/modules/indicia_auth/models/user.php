<?php defined('SYSPATH') or die('No direct script access.');

class User_Model extends Auth_User_Model {

	protected $belongs_to = array('core_role');
		
} // End User Model