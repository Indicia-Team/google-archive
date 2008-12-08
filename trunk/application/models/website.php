<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * Website Model
 *
 *
 * @package Indicia
 * @subpackage Model
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author xxxxxxx <xxx@xxx.net> / $Author$
 * @copyright xxxx
 * @version $Rev$ / $LastChangedDate$
 */
class Website_Model extends ORM
{

    protected $has_many = array(
			'termlists',
			'taxon_lists'
	);
    protected $belongs_to = array(
			'created_by'=>'user',
			'updated_by'=>'user'
	);
    protected $has_and_belongs_to_many = array(
			'locations',
			'users'
	);

    /**
     * Validate and save the data.
     */
    public function validate(Validation $array, $save = FALSE) {
    	echo kohana::debug($array->as_array());
        // uses PHP trim() to remove whitespace from beginning and end of all fields before validation
        $array->pre_filter('trim');
        $array->add_rules('title', 'required', 'length[1,100]');
        $array->add_rules('url', 'required', 'length[1,500]', 'url');
       	if (isset($array['password'])) $array->add_rules('password', 'required', 'length[7,30]');
        // Any fields that don't have a validation rule need to be copied into the model manually
        $this->description = $array['description'];
        return parent::validate($array, $save);
    }

}

?>
