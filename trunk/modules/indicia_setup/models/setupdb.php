<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * database setup model
 *
 * @package Indicia
 * @subpackage Model
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Armand Turpel <armand.turpel@gmail.com>
 * @version $Rev$ / $LastChangedDate$ / $Author$
 */

class Setupdb_Model extends Model
{
    /**
     * database connection object
     *
     * @var object $dbconn
     */
    private $dbconn = false;

    public function __construct(){}

    /**
     * connect to the database
     *
     * @return resource false on error
     */
    public function dbConnect($host, $port, $name, $user, $password)
    {
        return $this->dbconn = pg_connect("host     = {$host}
                                           port     = {$port}
                                           dbname   = {$name}
                                           user     = {$user}
                                           password = {$password}");
    }

    /**
     * start transaction
     *
     */
    public function begin()
    {
        pg_query($this->dbconn, "BEGIN");
    }

    /**
     * end transaction
     *
     */
    public function commit()
    {
        pg_query($this->dbconn, "COMMIT");
    }

    /**
     * rollback transaction
     *
     */
    public function rollback()
    {
        pg_query($this->dbconn, "ROLLBACK");
    }

    /**
     * create schema
     *
     * @param string $schema
     * @return bool
     */
    public function createSchema( $schema )
    {
        // remove any existing schema with this name
        //
        if(false === pg_query($this->dbconn, "DROP SCHEMA IF EXISTS {$schema} CASCADE"))
        {
            return pg_last_error($this->dbconn);
        }

        // create schema
        //
        if(false === pg_query($this->dbconn, "CREATE SCHEMA {$schema}"))
        {
            return pg_last_error($this->dbconn);
        }

        // add schema to search path
        //
        if(true !== ($result = $this->set_search_path( $schema )))
        {
            return $result;
        }

        return true;
    }

    /**
     * set schema search path
     *
     * @param string $schema
     * @return bool
     */
    private function set_search_path( $schema )
    {
        //
        // if the schema dosent exists we get an error
        //
        if(false === pg_query($this->dbconn, "SET search_path TO {$schema}, public, pg_catalog"))
        {
            return pg_last_error($this->dbconn);
        }

        return true;
    }

    /**
     * check if postscript scripts are installed
     *
     * @return bool
     */
    public function checkPostgis()
    {
        if(false === ($result = pg_query($this->dbconn, "SELECT postgis_scripts_installed()")))
        {
            return pg_last_error($this->dbconn);
        }

        return true;
    }

    /**
     * insert values in the system table
     *
     * @return bool
     */
    public function insertSystemInfo()
    {
        $new_system = Kohana::config('indicia_dist.system');

        if(false === pg_query($this->dbconn, "INSERT INTO \"system\"
                                                       VALUES (1,
                                                               '{$new_system['version']}',
                                                               '{$new_system['name']}',
                                                               '{$new_system['repository']}',
                                                               '{$new_system['release_date']}')"))
        {
            return pg_last_error($this->dbconn);
        }

        return true;
    }

    /**
     * check postgres serversion. at least 8.2 required
     *
     * @return mixed bool true if successful, false if unknown version, else server_version
     */
    public function check_postgres_version()
    {
        $server_version = pg_parameter_status( $this->dbconn, "server_version" );

        if(false !== $server_version)
        {
            if(-1 == version_compare($server_version, "8.2"))
            {
                return $server_version;
            }

            // version ok
            return true;
        }

        // unknown server_version
        return false;
    }

    /**
     * query
     *
     * @param string $content
     * @return mixed bool if successful else error string
     */
    public function query( $content )
    {
        if(false === pg_query($this->dbconn, $content))
        {
            return pg_last_error($this->dbconn);
        }

        return true;
    }

    /**
     * grant privileges to additional users
     *
     * @param string $users comma separated if more than one
     * @return bool
     */
    public function grant( $users )
    {
        // assign users in array
        $_users = explode(",", $users);

        // grant on tables
        //
        if(false !== ($result = pg_query($this->dbconn, "SELECT table_name FROM information_schema.tables WHERE table_schema = '{$this->table_schema}'")))
        {
            while ($row = pg_fetch_row($result))
            {
                foreach($_users as $user)
                {
                    $user = trim($user);
                    if(false === pg_query($this->dbconn, "GRANT ALL ON TABLE \"{$row[0]}\" TO \"{$user}\"" ))
                    {
                        return pg_last_error($this->dbconn);
                    }
                }
            }
        }
        else
        {
            return pg_last_error($this->dbconn);
        }

        // grant on views
        //
        if(false !== ($result = pg_query($this->dbconn, "SELECT table_name FROM information_schema.views WHERE table_schema = '{$this->table_schema}'")))
        {
            while ($row = pg_fetch_row($result))
            {
                foreach($_users as $user)
                {
                    $user = trim($user);
                    if(false === pg_query($this->dbconn, "GRANT ALL ON TABLE \"{$row[0]}\" TO \"{$user}\"" ))
                    {
                        return pg_last_error($this->dbconn);
                    }
                }
            }
        }
        else
        {
            return pg_last_error($this->dbconn);
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
                        return pg_last_error($this->dbconn);
                    }
                }
            }
        }
        else
        {
            return pg_last_error($this->dbconn);
        }

        return true;
    }

}

?>
