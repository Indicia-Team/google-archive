<?php defined('SYSPATH') or die('No direct script access.');

class Site_Role_Model extends ORM {

	protected $has_many = array('users_websites');


} // End site Role Model