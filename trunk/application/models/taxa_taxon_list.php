<?php defined('SYSPATH') or die('No direct script access.');

class Taxa_taxon_list_Model extends ORM_Tree {

	protected $belongs_to = array('taxon', 'taxon_list', 
		'created_by' => 'user', 
		'updated_by' => 'user');

	protected $children = 'taxa_taxon_lists';

	public function validate(Validation $array, $save = FALSE) {
		$array->pre_filter('trim');
		$array->add_rules('taxon_id', 'required');
		$array->add_rules('taxon_list_id', 'required');
		$array->add_rules('taxon_meaning_id', 'required');
#		$array->add_callbacks('deleted', array($this, '__dependents'));

		// Explicitly add those fields for which we don't do validation
		$this->parent_id = $array['parent_id'];
		$this->preferred = $array['preferred'];
		$this->taxonomic_sort_order = $array['taxonomic_sort_order'];
		return parent::validate($array, $save);
	}
	/**
	 * If we want to delete the record, we need to check that no dependents exist.
	 */
	public function __dependents(Validation $array, $field){
		if ($array['deleted'] == 'true'){
			$record = ORM::factory('taxa_taxon_list', $array['id']);
			if ($record->children->count()!=0){
				$array->add_error($field, 'has_children');
			}
		}
	}

}
