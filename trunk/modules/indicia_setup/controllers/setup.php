<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * Main indicia setup controller
 *
 * @package Indicia
 * @subpackage Controller
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Armand Turpel <armand.turpel@gmail.com>
 * @version $Rev$ / $LastChangedDate$ / $Author$
 */
class Setup_Controller extends Template_Controller
{
    /**
     * setup template name
     *
     * @var string $template
     */
	public $template = 'setup';

	public function __construct()
	{
		parent::__construct();

		// init and default values of view vars
		//
		$this->template->title       = Kohana::lang('setup.title');
		$this->template->description = Kohana::lang('setup.description');

		$this->template->url              = url::site() . 'setup';
		$this->template->dbhost           = '';
		$this->template->error_dbhost     = false;
		$this->template->dbport           = '5432';
		$this->template->error_dbport     = false;
		$this->template->dbuser           = '';
		$this->template->error_dbuser     = false;
		$this->template->dbpassword       = '';
		$this->template->error_dbpassword = false;
		$this->template->dbschema         = '';
		$this->template->error_dbschema   = false;
		$this->template->dbname           = '';
		$this->template->error_dbname     = false;
		$this->template->indicia_login          = '';
		$this->template->error_indicia_login    = false;
		$this->template->indicia_password       = '';
		$this->template->error_indicia_password = false;
	}

	public function index()
	{

	}

}

?>
