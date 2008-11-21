<?php defined('SYSPATH') or die('No direct script access.');

class Termlists_taxon_Model extends ORM_Tree {

	protected $belongs_to = array('taxon', 'taxon_list', 
		'created_by' => 'user', 
		'updated_by' => 'user');

	protected $children = 'taxon_lists_taxa';

	public function validate(Validation $array, $save = FALSE) {
		$array->pre_filter('trim');
		$array->add_rules('taxon_id', 'required');
		$array->add_rules('taxon_list_id', 'required');
		$array->add_rules('meaning_id', 'required');
#		$array->add_callbacks('deleted', array($this, '__dependents'));

		// Explicitly add those fields for which we don't do validation
		$this->parent_id = $array['parent_id'];
		$this->preferred = $array['preferred'];
		return parent::validate($array, $save);
	}
	/**
	 * If we want to delete the record, we need to check that no dependents exist.
	 */
	public function __dependents(Validation $array, $field){
		if ($array['deleted'] == 'true'){
			$record = ORM::factory('taxon_lists_taxa', $array['id']);
			if ($record->children->count()!=0){
				$array->add_error($field, 'has_children');
			}
		}
	}

}
