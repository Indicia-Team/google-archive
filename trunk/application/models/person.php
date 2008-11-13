<?php defined('SYSPATH') or die('No direct script access.');

class Person_Model extends ORM {

	protected $has_many = array('user');
}
