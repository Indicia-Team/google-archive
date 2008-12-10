<?php defined('SYSPATH') or die('No direct script access.');

class Title_Model extends ORM {

	protected $has_many = array('people');

} // End Auth Role Model