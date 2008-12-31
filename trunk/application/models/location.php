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
		$this->centroid_sref = $array['centroid_sref'];
		try {
			$this->centroid_geom = spatial_ref::sref_to_internal_wkt($this->centroid_sref, $this->centroid_sref_system);
		} catch (Exception $e) {
			$this->errors['centroid_sref']=$e->getMessage();
			return FALSE;
		}
		// TODO: boundarys!
		$this->boundary_geom = null;
		return parent::validate($array, $save);
	}

	/**
	 * Override set handler to translate WKT to PostGIS internal spatial data.
	 */
	public function __set($key, $value)
	{
		if (substr($key,-5) == '_geom')
		{
			if ($value) {
				$row = $this->db->query("SELECT ST_GeomFromText('$value', ".kohana::config('sref_notations.internal_srid').") AS geom")->current();
				$value = $row->geom;
			}
		}
		parent::__set($key, $value);
	}

	/**
	 * Override get handler to translate PostGIS internal spatial data to WKT.
	 */
	public function __get($column)
	{
		$value = parent::__get($column);

		if  ($column === 'centroid_geom' || $column === 'boundary_geom') {
			$row = $this->db->query("SELECT ST_asText('$value') AS wkt, 'hello' AS test")->current();
			$value = $row->wkt;
		}
		return $value;
	}

}
