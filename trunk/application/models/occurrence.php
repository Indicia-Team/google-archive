<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * Occurrence Model
 *
 *
 * @package Indicia
 * @subpackage Model
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @version $Rev$ / $LastChangedDate$
 */
class Occurrence_Model extends ORM
{
	protected $belongs_to=array(
		'determiner',
		'sample',
		'taxon',
		'created_by'=>'user',
		'updated_by'=>'user');

	public function validate(Validation $array, $save = false) {
		$array->pre_filter('trim');
		$array->add_rules('sample_id', 'required');
		$array->add_rules('determiner_id', 'required');
		$array->add_rules('website_id', 'required');
		$array->add_rules('taxa_taxon_list_id', 'required');
		
		// Explicitly add those fields for which we don't do validation
		$extraFields = array(
			'comment',
		);
		foreach ($extraFields as $a) {
			if (array_key_exists($a, $array->as_array())){
				$this->__set($a, $array[$a]);
			}
		}
		return parent::validate($array, $save);

	}

}
?>
