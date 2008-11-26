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
    /**
     * original code ???
     */
    protected $has_many = array('termlist');
    protected $belongs_to = array('created_by'=>'user', 'updated_by'=>'user');
    protected $has_and_belongs_to_many = array('locations');
    /**
     * original save function
     */
    public function validate(Validation $array, $save = FALSE) {
        // uses PHP trim() to remove whitespace from beginning and end of all fields before validation
        $array->pre_filter('trim');
        $array->add_rules('title', 'required', 'length[1,100]');
        $array->add_rules('url', 'required', 'length[1,500]', 'url');
        // Any fields that don't have a validation rule need to be copied into the model manually
        $this->description = $array['description'];
        return parent::validate($array, $save);
    }

    // modification starts from here

    /**
     * The validation object
     *
     * @var object $data_validation
     */
    private $data_validation = false;

    /**
     * Save website data
     *
     * @param array $data If present it have to contains form array data
     * @return bool true or false validation success status
     */
    public function saveData( $data = false )
    {
        // if no custom validation object exists set default validation
        if( $this->data_validation === false )
        {
            $this->default_validation( $data );
        }

        // finally validate and save
        return parent::validate($this->data_validation, true);
    }

    /**
     * Set custom validation object
     *
     * If some one will manage the whole validation process from within a controller
     *
     * @param object $data_validation Instance of Validation class with data to save
     */
    public function setValidation( Validation $data_validation )
    {
        $this->data_validation = $data_validation;
    }

    /**
     * Set default validation object
     *
     * @param array $data Website data to save
     */
    private function default_validation( & $data )
    {
        // create validation instance
        $this->data_validation = new Validation( $data );

        // uses PHP trim() to remove whitespace from beginning and end of all fields before validation
        $this->data_validation->pre_filter('trim');

        // remove html tags
        // doesnt work ??? utf8 ?
        $this->data_validation->pre_filter('strip_tags');

        // title content is required, length must be between 1 and 100 chars
        $this->data_validation->add_rules('title', 'required', 'length[1,100]');

        // Url is required, must be valid and between 1 and 500 chars.
        $this->data_validation->add_rules('url', 'required', 'length[1,500]', 'url');

        // Any fields that don't have a validation rule need to be copied into the model manually
        //
        // Thats clumsy ORM. if would see something in kohana like:
        // $this->data_validation->add_rules('description', 'none');
        $this->description = $this->data_validation['description'];
    }
}

?>
