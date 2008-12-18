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
        $this->view_var['dbhost']           = 'localhost';
        $this->view_var['error_dbhost']     = false;
        $this->view_var['dbport']           = '5432';
        $this->view_var['error_dbport']       = false;
        $this->view_var['dbuser']           = '';
        $this->view_var['error_dbuser']     = false;
        $this->view_var['dbpassword']       = '';
        $this->view_var['error_dbpassword'] = false;
        $this->view_var['dbschema']         = 'indicia';
        $this->view_var['error_dbschema']   = false;
        $this->view_var['dbname']           = '';
        $this->view_var['page_title_error'] = '';
        $this->view_var['error_dbname']     = false;
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
        $this->get_form_vars();

        if(true === $this->db_insert_data())
        {
            url::redirect();
        }

        $this->assign_view_vars();
    }

    /**
     * create db items and write database config file
     *
     * @return bool
     */
    private function db_insert_data()
    {
        if(false !== ($this->dbconn = $this->db_connect()))
        {
            $_db_file = str_replace("indicia",$this->db['schema'], file_get_contents( $this->db_file));

            pg_query($this->dbconn, "BEGIN");

            pg_send_query($this->dbconn, "SET search_path TO {$this->db['schema']}, public, pg_catalog");
            $res1 = pg_get_result($this->dbconn);

            if(false != ($error = pg_result_error($res1)))
            {
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . "<br />$error";
                Kohana::log("error", "Setup failed: {$error}");
                return false;
            }

            if(false === pg_query($this->dbconn, $_db_file ))
            {
                $error = pg_last_error($this->dbconn);
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup');
                Kohana::log("error", "Setup failed: {$error}");
                return false;
            }

            // execute sql files from the application/db folder
            //
            if(false === $this->_tmp_load_sql_files())
            {
                return false;
            }

            if(false === pg_query($this->dbconn, "GRANT ALL ON SCHEMA {$this->db['schema']} TO {$this->db['user']}" ))
            {
                $error = pg_last_error($this->dbconn);
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup');
                Kohana::log("error", "Setup failed: {$error}");
                return false;
            }

            $stat = pg_transaction_status($this->dbconn);

            if($stat === PGSQL_TRANSACTION_INERROR)
            {
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup');
                Kohana::log("error", "Setup failed: {$error}");
                return false;
            }

            pg_query($this->dbconn, "COMMIT");

            if(false === $this->write_database_config())
            {
                pg_query($this->dbconn, "ROLLBACK");
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_database_config');
                Kohana::log("error", "Could not write database config file. Please check file write permission rights.");
                return false;
            }

            if(false === $this->write_indicia_config())
            {
                pg_query($this->dbconn, "ROLLBACK");
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_indicia_config');
                Kohana::log("error", "Could not write indicia config file. Please check file write permission rights.");
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * connect to the database
     *
     * @return resource false on error
     */
    private function db_connect()
    {
        try
        {
            $dbconn = pg_connect("host={$this->db['host']}
                                  port={$this->db['port']}
                                  dbname={$this->db['name']}
                                  user={$this->db['user']}
                                  password={$this->db['password']}");
        }
        catch(ErrorException $e)
        {
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_connect');
            Kohana::log("error", "Setup failed: " . $e->getMessage());
            return false;
        }

        return $dbconn;
    }

    /**
     * base pre check
     *
     */
    private  function base_check()
    {
        // we stop here if the indicia config file exists
        //
        $system = Kohana::config('indicia.system', false, false);
        if($system !== null)
        {
            $this->view_var['page_title_error'] = ' - Warning';
            $this->view_var['error_general'][] = Kohana::lang('setup.error_remove_folder');
            Kohana::log("error", "First you have to remove or rename the config file application/config/indicia.php");
            return;
        }

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
        $this->db_file = dirname(dirname(dirname(dirname(__file__ )))) . '/modules/indicia_setup/db/indicia_setup.sql';

        if(!is_readable($this->db_file))
        {
            $this->view_var['page_title_error'] = ' - Warning';
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_file') . "<br /> {$this->db_file}";
            Kohana::log("error", "The following indicia setup sql file isnt readable by php scripts: {$this->db_file}");
        }

        // check if postgresql php extension is installed
        //
        if(!function_exists('pg_version'))
        {
            $this->view_var['page_title_error'] = ' - Warning';
            $this->view_var['error_general'][] = Kohana::lang('setup.error_no_postgres_client_extension');
            Kohana::log("error", "The postgresql php extension isnt installed");
        }

        // check if php_curl extension is installed
        //
        if(!function_exists('curl_version'))
        {
            $this->view_var['page_title_error'] = ' - Warning';
            $this->view_var['error_general'][] = Kohana::lang('setup.error_no_php_curl_extension');
            Kohana::log("error", "The php_curl extension isnt installed");
        }
    }

    /**
     * pre assign view vars
     *
     */
    private function get_form_vars()
    {
        $this->db['host']     = $this->view_var['dbhost']   = trim($_POST['dbhost']);
        $this->db['port']     = $this->view_var['dbport']   = trim(preg_replace("/[^0-9]/","", $_POST['dbport']));
        $this->db['name']     = $this->view_var['dbname']   = trim($_POST['dbname']);
        $this->db['schema']   = $this->view_var['dbschema'] = trim($_POST['dbschema']);
        $this->db['user']     = $this->view_var['dbuser']   = trim($_POST['dbuser']);
        $this->db['password'] = $this->view_var['dbpassword'] = trim($_POST['dbpassword']);
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

    /**
     * Write database.php config file
     *
     * @return bool
     */
    private function write_database_config()
    {
        $tmp_config = file_get_contents(dirname(dirname(__file__ )) . '/config/_database.php');

        $_config = str_replace(array("*host*","*port*","*name*","*user*","*password*","*schema*"),
                               array($this->db['host'],$this->db['port'],$this->db['name'],$this->db['user'],$this->db['password'],$this->db['schema']),
                               $tmp_config);

        $database_config = dirname(dirname(dirname(dirname(__file__)))) . "/application/config/database.php";

        if(!$fp = @fopen($database_config, 'w'))
        {
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup');
            Kohana::log("error", "Cant open file to write: ". $database_config);
            return false;
        }

        if( !@fwrite($fp, $_config) )
        {
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup');
            Kohana::log("error", "Cant write file: ". $database_config);
            return false;
        }

        @fclose($fp);

        return true;
    }

    /**
     * Write indicia.php config file
     *
     * @return bool
     */
    private function write_indicia_config()
    {
        $indicia_source_config = dirname(dirname(dirname(dirname(__file__)))) . "/application/config/indicia_dist.php";
        $indicia_dest_config = dirname(dirname(dirname(dirname(__file__)))) . "/application/config/indicia.php";

        return @copy($indicia_source_config, $indicia_dest_config);
    }

    /**
     * execute sql scripts from the application/db folder
     * Except the dbcreate.sql
     *
     * @return bool
     */
    private function _tmp_load_sql_files()
    {
        $file_name = array();
        $db_dir  = dirname(dirname(dirname(dirname(__file__ )))) . '/application/db';

        if ( (($handle = @opendir( $db_dir ))) != FALSE )
        {
            while ( (( $file = readdir( $handle ) )) != false )
            {
                if ( !preg_match("/^200\.*/", $file) )
                {
                    continue;
                }

                $file_name[] = $file;
            }
            @closedir( $handle );
        }
        else
        {
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup');
            Kohana::log("error", "Setup failed: cant open dir " . $db_dir);
            return false;
        }

        sort( $file_name );

        foreach($file_name as $name)
        {
            $_db_file = file_get_contents( $db_dir . '/' . $name );

            $_db_file = str_replace("indicia",$this->db['schema'], $_db_file);

            try
            {
                pg_query($this->dbconn, $_db_file );
            }
            catch(ErrorException $e)
            {
                $this->view_var['error_general'][] = 'Problem in file: ' . $name;
                $this->view_var['error_general'][] = $e->getMessage();
                Kohana::log("error", "Setup failed: " . $e->getMessage());
                return false;
            }
        }

        return true;
    }

}

?>
