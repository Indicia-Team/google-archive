<?php defined('SYSPATH') or die('No direct script access.');

class Termlist_Model extends ORM_Tree {
	protected $children = "termlists";
	protected $has_one = array('website');
	protected $has_many = array('termlists_term');
}
