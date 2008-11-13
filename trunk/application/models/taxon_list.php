<?php defined('SYSPATH') or die('No direct script access.');

class Taxon_list_Model extends ORM_Tree {

	protected $children = "taxon_lists";
	protected $belongs_to = array('website', 'created_by'=>'user', 'updated_by'=>'user');
	protected $has_many = array('taxon_lists_terms');

	public function validate(Validation $array, $save = FALSE) {
		$array->pre_filter('trim');
		$array->add_rules('title', 'required');

		// Explicitly add those fields for which we don't do validation
		$this->description = $array['description'];
		$this->website_id = $array['website_id'];
		$this->parent_id = $array['parent_id'];
		return parent::validate($array, $save);
	}

}
