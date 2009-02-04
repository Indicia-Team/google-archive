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

class SetupDb_Model extends Model
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
     * set schema search path
     *
     * @param string $schema
     * @return bool
     */
    public function setSearchPath( $schema )
    {
        //
        // if the schema dosent exists we get an error
        //
        if(false === pg_query($this->dbconn, "SET search_path TO {$schema}, public, pg_catalog"))
        {
            return pg_last_error($this->dbconn);
        }

        $this->table_schema = $schema;

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
