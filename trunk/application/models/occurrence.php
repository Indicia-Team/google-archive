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

	/**
	 * Overrides the postSubmit() function to provide support for adding occurrence attributes
	 * within the transaction.
	 */
	protected function postSubmit() {
		// Occurrences have occurrence attributes associated, stored in a
		// metafield.
		syslog(LOG_DEBUG, "About to submit occurrence attributes.");
		if (array_key_exists('metaFields', $this->submission) &&
			array_key_exists('occAttributes', $this->submission['metaFields']))
		{
			foreach ($this->submission['metaFields']['occAttributes']['value'] as
				$idx => $attr)
			{
				syslog(LOG_DEBUG, print_r($attr, true));
				$value = $attr['fields']['value'];
				$attrId = $attr['fields']['occurrence_attribute_id']['value'];
				$oa = ORM::factory('occurrence_attribute', $attrId);
				$vf = 'text_value';
				switch ($oa->data_type) {
				case 'T':
					$vf = 'text_value';
					break;
				case 'F':
					$vf = 'float_value';
					break;
				case 'D':
					// Date
					$vf = 'text_value';
					break;
				case 'V':
					// Vague Date
					// TODO
					$vf = 'text_value';
					break;
				default:
					// Lookup in list
					$vf = 'int_value';
					break;
				}

				$attr['fields'][$vf] = $value;
				$attr['fields']['occurrence_id'] = $this->id;

				$oam = ORM::factory('occurrence_attribute_value');
				$oam->submission = $attr;
				if (!$oam->inner_submit()) {
					$this->db->query('ROLLBACK');
					return null;
				}
			}
			return true;
		}
		break;
	}
}
?>
