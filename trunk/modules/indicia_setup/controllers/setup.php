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

        $this->disable_browser_caching();

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
        $this->view_var['dbschema']         = '';
        $this->view_var['error_dbschema']   = false;
        $this->view_var['dbname']           = '';
        $this->view_var['page_title_error'] = '';
        $this->view_var['error_dbname']     = false;
        $this->view_var['error_dbgrant']    = false;
        $this->view_var['dbgrant']          = '';
        $this->view_var['error_general']          = array();

        // run system pre check
        $this->base_check();
    }

    /**
     * setup on first load
     *
     */
    public function index()
    {
        // only assign default values
        //
        $this->assign_view_vars();
    }

    /**
     * run setup on submit
     *
     */
    public function run()
    {
        $this->get_form_vars();

        // reload the main page if setup was successful
        //
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
        // first try to connect to the database
        //
        if(false !== ($this->dbconn = $this->db_connect()))
        {
            $this->prefix = '';

            // empty or public schema isnt allowed
            //
            if(($this->db['schema'] == 'public') || empty($this->db['schema']))
            {
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_wrong_schema');
                Kohana::log("error", "Setup failed: wrong schema {$this->db['schema']}");
                $this->view_var['error_dbschema'] = true;
                return false;
            }
            else
            {
                $this->schema_and_postfix = $this->db['schema'] . '.';
            }

            // start transaction
            //
            pg_query($this->dbconn, "BEGIN");

            // set schema search path
            // if the schema dosent exists we get an error
            //
            if(false === pg_query($this->dbconn, "SET search_path TO {$this->db['schema']}, public, pg_catalog"))
            {
                $error = pg_last_error($this->dbconn);
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_schema');
                Kohana::log("error", "Setup failed: {$error}");
                $this->view_var['error_dbschema'] = true;
                return false;
            }

            // check postgis installation
            //
            if( false === $this->check_postgis())
            {
                return false;
            }

            //
            // create sequences
            //
            $_db_file_sequences = str_replace("i_schema.",$this->schema_and_postfix, file_get_contents( $this->db_file_indicia_sequences));
            Kohana::log("info", "Processing: ".$this->db_file_indicia_sequences);

            if(false === pg_query($this->dbconn, $_db_file_sequences))
            {
                $error = pg_last_error($this->dbconn);
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
                Kohana::log("error", "Setup failed: {$error}");
                return false;
            }

            //
            // create tables
            //
            $_db_file_tables = str_replace("i_schema.",$this->schema_and_postfix, file_get_contents( $this->db_file_indicia_tables));
            Kohana::log("info", "Processing: ".$this->db_file_indicia_tables);

            if(false === pg_query($this->dbconn, $_db_file_tables))
            {
                $error = pg_last_error($this->dbconn);
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
                Kohana::log("error", "Setup failed: {$error}");
                return false;
            }

            //
            // create views
            //
            $_db_file_views = str_replace("i_schema.",$this->schema_and_postfix, file_get_contents( $this->db_file_indicia_views));
            Kohana::log("info", "Processing: ".$this->db_file_indicia_views);

            if(false === pg_query($this->dbconn, $_db_file_views))
            {
                $error = pg_last_error($this->dbconn);
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
                Kohana::log("error", "Setup failed: {$error}");
                return false;
            }

            //
            // insert alterations
            //
            $_db_file_alterations = str_replace("i_schema.",$this->schema_and_postfix, file_get_contents( $this->db_file_postgis_alterations));
            Kohana::log("info", "Processing: ".$this->db_file_postgis_alterations);

            if(false === pg_query($this->dbconn, $_db_file_alterations))
            {
                $error = pg_last_error($this->dbconn);
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
                Kohana::log("error", "Setup failed: {$error}");
                return false;
            }

            // grant all privileges to other users on database items
            //
            if(!empty($this->db['grant_users']))
            {
                if(false === $this->grant_on_database_items())
                {
                    return false;
                }
            }

            // insert indicia version values into system table
            //
            if(false === $this->insert_into_system())
            {
                return false;
            }

            // get transaction status
            $stat = pg_transaction_status($this->dbconn);

            if($stat === PGSQL_TRANSACTION_INERROR)
            {
                $error = pg_last_error($this->dbconn);
                $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
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
        if( false === ($dbconn = pg_connect("host     = {$this->db['host']}
                                             port     = {$this->db['port']}
                                             dbname   = {$this->db['name']}
                                             user     = {$this->db['user']}
                                             password = {$this->db['password']}")))
        {
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_connect');
            Kohana::log("error", "Setup failed: database connection error");
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

        // /application/db/indicia_sequences.sql file must be readable by php scripts
        //
        $this->db_file_indicia_sequences = dirname(dirname(dirname(dirname(__file__ )))) . '/modules/indicia_setup/db/indicia_sequences.sql';

        if(!is_readable($this->db_file_indicia_sequences))
        {
            $this->view_var['page_title_error'] = ' - Warning';
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_file') . "<br /> {$this->db_file_indicia_sequences}";
            Kohana::log("error", "The following indicia setup sql file isnt readable by php scripts: {$this->db_file_indicia_sequences}");
        }

        // /application/db/indicia_tables.sql file must be readable by php scripts
        //
        $this->db_file_indicia_tables = dirname(dirname(dirname(dirname(__file__ )))) . '/modules/indicia_setup/db/indicia_tables.sql';

        if(!is_readable($this->db_file_indicia_tables))
        {
            $this->view_var['page_title_error'] = ' - Warning';
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_file') . "<br /> {$this->db_file_indicia_tables}";
            Kohana::log("error", "The following indicia setup sql file isnt readable by php scripts: {$this->db_file_indicia_tables}");
        }

        // /application/db/indicia_views.sql file must be readable by php scripts
        //
        $this->db_file_indicia_views = dirname(dirname(dirname(dirname(__file__ )))) . '/modules/indicia_setup/db/indicia_views.sql';

        if(!is_readable($this->db_file_indicia_views))
        {
            $this->view_var['page_title_error'] = ' - Warning';
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_file') . "<br /> {$this->db_file_indicia_views}";
            Kohana::log("error", "The following indicia setup sql file isnt readable by php scripts: {$this->db_file_indicia_views}");
        }

        // /application/db/postgis_alterations.sql file must be readable by php scripts
        //
        $this->db_file_postgis_alterations = dirname(dirname(dirname(dirname(__file__ )))) . '/modules/indicia_setup/db/postgis_alterations.sql';

        if(!is_readable($this->db_file_postgis_alterations))
        {
            $this->view_var['page_title_error'] = ' - Warning';
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_file') . "<br /> {$this->db_file_postgis_alterations}";
            Kohana::log("error", "The following indicia setup sql file isnt readable by php scripts: {$this->db_file_postgis_alterations}");
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
        $this->db['grant_users'] = $this->view_var['dbgrant'] = trim($_POST['dbgrant']);
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

        $_config = str_replace(array("*host*","*port*","*name*","*user*","*password*","*prefix*","*schema*"),
                               array($this->db['host'],$this->db['port'],$this->db['name'],$this->db['user'],$this->db['password'],$this->prefix,$this->db['schema']),
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
     * grant privileges to additional users
     *
     * @return bool
     */
    private function grant_on_database_items()
    {
        // assign users in array
        $_users = explode(",", $this->db['grant_users']);

        // grant on tables
        //
        if(false !== ($result = pg_query($this->dbconn, "SELECT table_name FROM information_schema.tables WHERE table_schema = '{$this->db['schema']}'")))
        {
            while ($row = pg_fetch_row($result))
            {
                foreach($_users as $user)
                {
                    $user = trim($user);
                    if(false === pg_query($this->dbconn, "GRANT ALL ON TABLE \"{$row[0]}\" TO \"{$user}\"" ))
                    {
                        $error = pg_last_error($this->dbconn);
                        $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
                        Kohana::log("error", "Setup failed: {$error}");
                        $this->view_var['error_dbgrant'] = true;
                        return false;
                    }
                }
            }
        }
        else
        {
            $error = pg_last_error($this->dbconn);
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
            Kohana::log("error", "Setup failed: {$error}");
            return false;
        }


        // grant on views
        //
        if(false !== ($result = pg_query($this->dbconn, "SELECT table_name FROM information_schema.views WHERE table_schema = '{$this->db['schema']}'")))
        {
            while ($row = pg_fetch_row($result))
            {
                foreach($_users as $user)
                {
                    $user = trim($user);
                    if(false === pg_query($this->dbconn, "GRANT ALL ON TABLE \"{$row[0]}\" TO \"{$user}\"" ))
                    {
                        $error = pg_last_error($this->dbconn);
                        $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
                        Kohana::log("error", "Setup failed: {$error}");
                        $this->view_var['error_dbgrant'] = true;
                        return false;
                    }
                }
            }
        }
        else
        {
            $error = pg_last_error($this->dbconn);
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
            Kohana::log("error", "Setup failed: {$error}");
            return false;
        }


        // grant on sequences
        //
        if(false !== ($result = pg_query($this->dbconn, "SELECT sequence_name FROM information_schema.sequences")))
        {
            while ($row = pg_fetch_row($result))
            {
                foreach($_users as $user)
                {
                    $user = trim($user);
                    if(false === pg_query($this->dbconn, "GRANT ALL ON SEQUENCE \"{$row[0]}\" TO \"{$user}\"" ))
                    {
                        $error = pg_last_error($this->dbconn);
                        $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
                        Kohana::log("error", "Setup failed: {$error}");
                        $this->view_var['error_dbgrant'] = true;
                        return false;
                    }
                }
            }
        }
        else
        {
            $error = pg_last_error($this->dbconn);
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
            Kohana::log("error", "Setup failed: {$error}");
            return false;
        }

        return true;
    }

    /**
     * check if postscript scripts are installed
     *
     * @return bool
     */
    private function check_postgis()
    {
        if(false === ($result = pg_query($this->dbconn, "SELECT postgis_scripts_installed()")))
        {
            $error = pg_last_error($this->dbconn);
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_postgis');
            Kohana::log("error", "Setup failed: {$error}");
            return false;
        }

        return true;
    }

    /**
     * insert values in the system table
     *
     * @return bool
     */
    private function insert_into_system()
    {
        $new_system = Kohana::config('indicia_dist.system');

        if(false === ($result = pg_query($this->dbconn, "INSERT INTO \"system\"
                                                           VALUES (1,
                                                                   '{$new_system['version']}',
                                                                   '{$new_system['name']}',
                                                                   '{$new_system['repository']}',
                                                                   '{$new_system['release_date']}')")))
        {
            $error = pg_last_error($this->dbconn);
            $this->view_var['error_general'][] = Kohana::lang('setup.error_db_setup') . '<br />' . $error;
            Kohana::log("error", "Setup failed: {$error}");
            return false;
        }

        return true;
    }


    /**
     * Add http headers to disable browser caching
     *
     */
    private function disable_browser_caching()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('P3P: CP="NOI NID ADMa OUR IND UNI COM NAV"');
    }
}

?>
