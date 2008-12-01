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
     */
    public function run( $current_version, $new_system )
    {
        // Upgrade not possible if the new version is lower than the database version
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
/*
            if(0 == version_compare('0.1', $current_version) )
            {
                // upgrade from version 0.1 to 0.2
                $this->upgrade_0_1_to_0_2();
                $current_version = '0.2';
            }

            if(0 == version_compare('0.2', $current_version) )
            {
                // upgrade from version 0.2 to 0.3
                $this->upgrade_0_2_to_0_3();
                $current_version = '0.3';
            }

            if(0 == version_compare('0.3', $current_version) )
            {
                // upgrade from version 0.3 to 0.4
                $this->upgrade_0_3_to_0_4();
                $current_version = '0.4';
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

    }

    /**
     * upgrade from version 0.2 to 0.3
     *
     */
    private function upgrade_0_2_to_0_3()
    {

    }

    /**
     * upgrade from version 0.3 to 0.4
     *
     */
    private function upgrade_0_3_to_0_4()
    {

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
        $message  = "________________________________________________\n";
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
        $str = "<?php \n\n";

        $str .= '$config["system"]["version"]'      . " = '{$new_system['version']}';\n";
        $str .= '$config["system"]["name"]'         . " = '{$new_system['name']}';\n";
        $str .= '$config["system"]["repository"]'   . " = '{$new_system['repository']}';\n";
        $str .= '$config["system"]["release_date"]' . " = '{$new_system['release_date']}';\n\n";

        $str .= "?>";

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
}

?>
