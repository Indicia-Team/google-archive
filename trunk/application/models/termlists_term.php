<?php defined('SYSPATH') or die('No direct script access.');

class Termlists_term_Model extends ORM {

	protected $belongs_to = array('term', 'termlist', 'created_by' => 'user', 'updated_by' => 'user');

}
