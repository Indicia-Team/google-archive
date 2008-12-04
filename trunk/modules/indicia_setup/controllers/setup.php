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

		$this->view_var = array();

		$this->template->title       = Kohana::lang('setup.title');
		$this->template->description = Kohana::lang('setup.description');

		$this->view_var['url']              = url::site() . 'setup/run';
		$this->view_var['dbhost']           = '';
		$this->view_var['error_dbhost']     = false;
		$this->view_var['dbport']           = '5432';
		$this->view_var['error_dbport']       = false;
		$this->view_var['dbuser']           = '';
		$this->view_var['error_dbuser']     = false;
		$this->view_var['dbpassword']       = '';
		$this->view_var['error_dbpassword'] = false;
		$this->view_var['dbschema']         = '';
		$this->view_var['error_dbschema']   = false;
		$this->view_var['dbname']           = '';
		$this->view_var['page_title_error'] = '';
		$this->view_var['error_dbname']     = false;
		$this->view_var['indicia_login']          = '';
		$this->view_var['error_indicia_login']    = false;
		$this->view_var['indicia_password']       = '';
		$this->view_var['error_indicia_password'] = false;
		$this->view_var['error_general']          = array();

		// run system pre check
		$this->base_check();
	}

	public function index()
	{
		$this->assign_view_vars();
	}

	/**
	 * run setup
	 *
	 */
	public function run()
	{


		$this->assign_view_vars();
	}

	/**
	 * base pre check
	 *
	 */
	private function base_check()
	{
		// /upload directory must be writeable by php scripts
		//
		$upload_dir = dirname(dirname(dirname(dirname(__file__ )))) . '/upload';

		if(!is_writeable($upload_dir))
		{
			$this->view_var['page_title_error'] = ' - Warning';
			$this->view_var['error_general'][] = Kohana::lang('setup.error_upload_folder') . "<br /> {$upload_dir}";
			Kohana::log("error", "The following folder isnt writeable by php scripts: {$upload_dir}");
		}

		// /application/config directory must be writeable by php scripts
		//
		$config_dir = dirname(dirname(dirname(dirname(__file__ )))) . '/application/config';

		if(!is_writeable($config_dir))
		{
			$this->view_var['page_title_error'] = ' - Warning';
			$this->view_var['error_general'][] = Kohana::lang('setup.error_config_folder') . "<br /> {$config_dir}";
			Kohana::log("error", "The following folder isnt writeable by php scripts: {$config_dir}");
		}

		// /application/db/indicia_setup.sql file must be readable by php scripts
		//
		$db_file = dirname(dirname(dirname(dirname(__file__ )))) . '/application/db/indicia_setup.sql';

		if(!is_readable($db_file))
		{
			$this->view_var['page_title_error'] = ' - Warning';
			$this->view_var['error_general'][] = Kohana::lang('setup.error_db_file') . "<br /> {$db_file}";
			Kohana::log("error", "The following indicia setup sql file isnt readable by php scripts: {$db_file}");
		}

		// check if postgresql php extension is installed
		//
		if(!function_exists('pg_version'))
		{
			$this->view_var['page_title_error'] = ' - Warning';
			$this->view_var['error_general'][] = Kohana::lang('setup.error_no_postgres_client_extension');
			Kohana::log("error", "The postgresql php extension isnt installed");
		}
	}

	/**
	 * assign view vars
	 *
	 */
	private function assign_view_vars()
	{
		foreach($this->view_var as $key => $val)
		{
			$this->template->$key = $val;
		}
	}

}

?>
