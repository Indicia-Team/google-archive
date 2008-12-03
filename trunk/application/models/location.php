<?php defined('SYSPATH') or die('No direct script access.');

class Location_Model extends ORM_Tree {

	protected $children = "locations";
	protected $has_and_belongs_to_many = array('websites');
	protected $has_many = array('samples');
	protected $belongs_to = array('created_by'=>'user', 'updated_by'=>'user');

	protected $search_field='name';

	public function validate(Validation $array, $save = FALSE) {
		$array->pre_filter('trim');
		$array->add_rules('name', 'required');
		$array->add_rules('centroid_sref', 'required');

		// Explicitly add those fields for which we don't do validation
		$this->code = $array['code'];
		$this->parent_id = $array['parent_id'];
		$this->centroid_sref_system = $array['centroid_sref_system'];
		$this->centroid_geom = $array['centroid_geom'];
		// TODO: boundarys!
		$this->boundary_geom = null;
		return parent::validate($array, $save);
	}

	/**
	 * Before submission, map spatial references to their underlying database fields.
	 */
	protected function preSubmit()
	{
		$sref = $this->submission['fields']['centroid_sref']['value'];
		$sref_system = $this->submission['fields']['centroid_sref_system']['value'];
		$geom = spatial_ref::sref_to_wgs84($sref, $sref_system);
		$this->submission['fields']['centroid_geom']['value']="ST_GeomFromText('$geom')";
		return parent::presubmit();
	}

}
