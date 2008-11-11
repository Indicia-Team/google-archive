<?php defined('SYSPATH') or die('No direct script access.');

class Website_Model extends ORM {
	protected $has_many = array('termlists');
}

?>
