<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * Upgrade Model
 *
 * @package Indicia
 * @subpackage Model
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Armand Turpel <armand.turpel@gmail.com>
 * @version $Rev$ / $LastChangedDate$ / $Author$
 */
class Upgrade_Model extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Do upgrade
     *
     * @param string $current_version
     * @param array $new_system
     * @return bool
     */
    public function run( $current_version, $new_system )
    {
        // Downgrade not possible. The new version is lower than the database version
        //
        if(1 == version_compare($current_version, $new_system['version']) )
        {
            Kohana::log('error', 'Current script version is lower than the database version. Upgrade not possible.');
            return false;
        }

        // start transaction
        $this->db->query("BEGIN");

        try
        {

            // remove this upgrade step for the first indicia release
            //
            if(0 == version_compare('0.1', $current_version) )
            {
                // upgrade from version 0.1 to 0.2
                if(false === $this->upgrade_0_1_to_0_2())
                {
                    return false;
                }
                $current_version = '0.2';
            }

/* Sample for the next upgrade

            if(0 == version_compare('0.2', $current_version) )
            {
                // upgrade from version 0.2 to 0.3
                if(false === $this->upgrade_0_2_to_0_3())
                {
                    return false;
                }
                $current_version = '0.3';
            }

*/

            // update system table entry to new version
            $this->set_new_version( $new_system );

            // update indicia.php config file
            $this->update_config_file( $new_system );

            // commit transaction
            $this->db->query("COMMIT");

            return true;
        }
        catch(Kohana_Database_Exception $e)
        {
            $this->log($e);
        }
        catch(Indicia_File_Exception $e)
        {
            $this->log($e);
        }
        catch(Exception $e)
        {
            $this->log($e);
        }

        // rollback transaction
        $this->db->query("ROLLBACK");

        return false;
    }

    /**
     * upgrade from version 0.1 to 0.2
     *
     */
    private function upgrade_0_1_to_0_2()
    {
        if(false === $this->execute_sql_scripts( 'upgrade_0_1_to_0_2' ))
        {
            return false;
        }

        return true;
    }

    /**
     * upgrade from version 0.2 to 0.3
     *
     */
    private function upgrade_0_2_to_0_3()
    {
        if(false === $this->execute_sql_scripts( 'upgrade_0_2_to_0_3' ))
        {
            return false;
        }

        return true;
    }

    /**
     * update system table entry to new version
     *
     * @param array $new_system  New version number
     */
    private function set_new_version( $new_system )
    {
        $this->db->insert('system',
                          array('version'      => $new_system['version'],
                                'name'         => $new_system['name'],
                                'repository'   => $new_system['repository'],
                                'release_date' => $new_system['release_date']));
    }

    /**
     * update indicia.php config file with new system info
     * @param array $new_system
     */
    private function update_config_file( $new_system )
    {
        $this->write_config( $this->buildConfigFileContent( $new_system ) );
    }

    /**
     * log error message
     *
     * @param object $e
     */
    private function log($e)
    {
        $message  = "\n\n\n________________________________________________\n";
        $message .= "Upgrade Error - Time: " . date() . "\n";
        $message .= "MESSAGE: "  .$e->getMessage()."\n";
        $message .= "CODE: "     .$e->getCode()."\n";
        $message .= "FILE: "     .$e->getFile()."\n";
        $message .= "LINE: "     .$e->getLine()."\n";

        Kohana::log('error', $message);
    }

    /**
     * Build the system config content of indicia.php
     *
     * @param array $new_system
     */
    private function buildConfigFileContent( & $new_system )
    {
        // get config vars from the existing old config file
        $__config = Kohana::config('indicia');

        $str = "<?php \n\n";

        $str .= '$config["system"]["version"]'      . " = '{$new_system['version']}';\n";
        $str .= '$config["system"]["name"]'         . " = '{$new_system['name']}';\n";
        $str .= '$config["system"]["repository"]'   . " = '{$new_system['repository']}';\n";
        $str .= '$config["system"]["release_date"]' . " = '{$new_system['release_date']}';\n\n";

        $str .= '$config[\'private_key\']   = \'' . $__config['private_key'] . "'; // Change this to a unique value for each Indicia install\n";
        $str .= '$config[\'nonce_life\']      = '   . $__config['nonce_life'] . ";       // life span of an authentication token for services, in seconds\n";
        $str .= '$config[\'maxUploadSize\']   = '   . $__config['maxUploadSize'] . "; // Maximum size of an upload\n";
        $str .= '$config[\'defaultPersonId\'] = '   . $__config['defaultPersonId'] . ";\n";

        $str .= "\n?>";

        return $str;
    }

    /**
     * Write indicia.php config file
     *
     * @param string $config_content
     */
    private function write_config( $config_content )
    {
        $config_file = dirname(dirname(__file__)) . "/config/indicia.php";

        if( !@is_writeable($config_file) )
        {
            throw new Indicia_File_Exception("Config file indicia.php isnt writeable. Check permission on: ". $config_file);
        }

        if(!$fp = @fopen($config_file, 'w'))
        {
           throw new Indicia_File_Exception("Cant open file to write: ". $config_file);
        }

        if( !@fwrite($fp, $config_content) )
        {
            throw new Indicia_File_Exception("Cant write file: ". $config_file);
        }

        @fclose($fp);
    }

    /**
     * execute all sql srips from the upgrade folder
     *
     * @param string $upgrade_folder folder name
     */
    private function execute_sql_scripts( $upgrade_folder )
    {
        $file_name = array();
        $full_upgrade_folder = dirname(dirname(dirname(__file__))) . "/modules/indicia_setup/db/" . $upgrade_folder;

        if ( (($handle = @opendir( $full_upgrade_folder ))) != FALSE )
        {
            while ( (( $file = readdir( $handle ) )) != false )
            {
                if ( !preg_match("/^20.*\.sql$/", $file) )
                {
                    continue;
                }

                $file_name[] = $file;
            }
            @closedir( $handle );
        }
        else
        {
            Kohana::log("error", "Upgrade failed: cant open dir " . $full_upgrade_folder);
            return false;
        }

        sort( $file_name );

        foreach($file_name as $name)
        {
            $_db_file = file_get_contents( $full_upgrade_folder . '/' . $name );

            $this->db->query( $_db_file );
        }

        return true;
    }
}

?>
