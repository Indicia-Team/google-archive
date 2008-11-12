<?php defined('SYSPATH') or die('No direct script access.');

class Termlist_Model extends ORM_Tree {

	protected $children = "termlist";
	protected $belongs_to = array('website');
	protected $has_many = array('termlists_term');

	public function validate(Validation $array, $save = FALSE) {
		$array->pre_filter('trim');
		$array->add_rules('title','required','length[1-100]','standard_text');
		$array->add_rules('description','standard_text');
		return parent::validate($array, $save);
	}

}
