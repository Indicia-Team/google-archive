<?php defined('SYSPATH') or die('No direct script access.');

class Taxon_Model extends ORM {
	protected $search_field='taxon';
	protected $belongs_to = array('meaning', 'language', 'created_by' => 'user', 'updated_by' => 'user');
	protected $has_many = array('taxa_taxon_lists');
	protected $has_and_belongs_to_many = array('taxon_lists');

	public function validate(Validation $array, $save = FALSE) {
		$array->pre_filter('trim');
		$array->add_rules('taxon', 'required');
		$array->add_rules('scientific', 'required');
		$array->add_rules('taxon_group_id', 'required');

		// Explicitly add those fields for which we don't do validation
		$this->language_id = $array['language_id'];
		$this->external_key = $array['external_key'];
		$this->authority = $array['authority'];
		$this->search_code = $array['search_code'];

		return parent::validate($array, $save);
	}
}

