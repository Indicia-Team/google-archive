<?php defined('SYSPATH') or die('No direct script access.');

class Termlists_term_Model extends ORM {

	protected $belongs_to = array('term', 'termlist', 
		'created_by' => 'user', 
		'updated_by' => 'user');

	public function validate(Validation $array, $save = FALSE) {
		$array->pre_filter('trim');
		$array->add_rules('term_id', 'required');
		$array->add_rules('termlist_id', 'required');
		$array->add_rules('meaning_id', 'required');
#		$array->add_callbacks('deleted', array($this, '_dependents'));

		// Explicitly add those fields for which we don't do validation
		$this->parent_id = $array['parent_id'];
#		$this->deleted = $array['deleted'];
		return parent::validate($array, $save);
	}

}
