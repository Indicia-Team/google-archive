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
  public function caption()
  {
    return $this->id;
  }
  protected $belongs_to=array(
  'determiner',
  'sample',
  'taxon',
  'created_by'=>'user',
  'updated_by'=>'user'
  );
  
  public function validate(Validation $array, $save = false) {
    $array->pre_filter('trim');
    $array->add_rules('sample_id', 'required');
    $array->add_rules('website_id', 'required');
    $array->add_rules('taxa_taxon_list_id', 'required');
    
    // Explicitly add those fields for which we don't do validation
    $extraFields = array(
    'comment',
    'determiner_id',
    'deleted'
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
    if (array_key_exists('metaFields', $this->submission) &&
      array_key_exists('occAttributes', $this->submission['metaFields']))
      {
	Kohana::log("info", "About to submit occurrence attributes.");
	foreach ($this->submission['metaFields']['occAttributes']['value'] as
	  $idx => $attr)
	{
	  $value = $attr['fields']['value'];
	  if ($value['value'] != '') {
	    $attrId = $attr['fields']['occurrence_attribute_id']['value'];
	    $oa = ORM::factory('occurrence_attribute', $attrId);
	    $vf = null;
	    switch ($oa->data_type) {
	      case 'T':
		$vf = 'text_value';
		break;
	      case 'F':
		$vf = 'float_value';
		break;
	      case 'D':
		// Date
		$vd=vague_date::string_to_vague_date($value['value']);
		$attr['fields']['date_start_value']['value'] = $vd['start'];
		$attr['fields']['date_end_value']['value'] = $vd['end'];
		$attr['fields']['date_type_value']['value'] = $vd['type'];
		break;
	      case 'V':
		// Vague Date
		$vd=vague_date::string_to_vague_date($value['value']);
		$attr['fields']['date_start_value']['value'] = $vd['start'];
		$attr['fields']['date_end_value']['value'] = $vd['end'];
		$attr['fields']['date_type_value']['value'] = $vd['type'];
		
		break;
	      default:
		// Lookup in list
		$vf = 'int_value';
		break;
	  }
	  
	  if ($vf != null) $attr['fields'][$vf] = $value;
	  $attr['fields']['occurrence_id']['value'] = $this->id;
	  
	  $oam = ORM::factory('occurrence_attribute_value');
	  $oam->submission = $attr;
	  if (!$oam->inner_submit()) {
	    $this->db->query('ROLLBACK');
	    return null;
	  }
	  }
	}
	return true;
      }
      return true;
  }
}
?>
