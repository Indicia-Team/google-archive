<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * Sample Model
 *
 *
 * @package Indicia
 * @subpackage Model
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @version $Rev$ / $LastChangedDate$
 */

class Sample_Model extends ORM
{
	protected $has_many=array('occurrences');
	protected $belongs_to=array(
		'survey',
		'location',
		'created_by'=>'user',
		'updated_by'=>'user');

	/**
	 * Validate and save the data.
	 *
	 * @todo add a validation rule for valid date types.
	 * @todo allow a date string to be passed, which gets mapped to a vague date start, end and type.
	 * @todo validate at least a location_name or sref required
	 */
	public function validate(Validation $array, $save = FALSE) {
		$orig_values = $array->as_array();

		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$array->pre_filter('trim');
		$array->add_rules('date_type', 'required', 'length[1,2]');
		$system 	 = $orig_values['entered_sref_system'];
		$array->add_rules('entered_sref', "sref[$system]");
		$array->add_rules('entered_sref_system', 'sref_system');

		// Any fields that don't have a validation rule need to be copied into the model manually
		if (array_key_exists('date_start', $array->as_array()))
			$this->date_start 	= $array['date_start'];
		if (array_key_exists('date_end', $array->as_array()))
			$this->date_end 	= $array['date_end'];
		if (array_key_exists('geom', $array->as_array()))
			$this->geom 		= $array['geom'];

		return parent::validate($array, $save);
	}

	/**
	 * Before submission, map vague dates to their underlying database fields.
	 */
	protected function preSubmit()
	{
		$vague_date=vague_date::string_to_vague_date($this->submission['fields']['date']['value']);
		$this->submission['fields']['date_start']['value'] = $vague_date['start'];
		$this->submission['fields']['date_end']['value'] = $vague_date['end'];
		$this->submission['fields']['date_type']['value'] = $vague_date['type'];
		return parent::presubmit();
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

		if  (substr($column,-5) == '_geom') {
			$row = $this->db->query("SELECT ST_asText('$value') AS wkt")->current();
			$value = $row->wkt;
		}
		return $value;
	}

}
?>
