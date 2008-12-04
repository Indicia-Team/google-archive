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
		// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
		$array->pre_filter('trim');
		$array->add_rules('date_type', 'required', 'length[1,2]');
		$array->add_rules('entered_sref_system', 'sref_system');
		$orig_values = $array->as_array();
		$system 	 = $orig_values['entered_sref_system'];
		$array->add_rules('entered_sref', "sref[$system]");
		// Any fields that don't have a validation rule need to be copied into the model manually
		$this->date_start 	= $array['date_start'];
		$this->date_end 	= $array['date_end'];
		$this->geom 		= $array['geom'];
		return parent::validate($array, $save);
	}

	/**
	 * Before submission, map spatial references and vague dates to their underlying database
	 * fields.
	 *
	 * @todo - map vague dates.
	 */
	protected function preSubmit()
	{
		if (array_key_exists('entered_sref', $this->submission['fields'])) {
			$sref = $this->submission['fields']['entered_sref']['value'];
			$sref_system = $this->submission['fields']['entered_sref_system']['value'];
			$geom = spatial_ref::sref_to_wgs84($sref, $sref_system);
			$this->submission['fields']['geom']['value']="ST_GeomFromText('$geom')";
		}
		return parent::presubmit();
	}

}
?>
