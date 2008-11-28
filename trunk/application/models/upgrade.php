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
    /*
        if(1 == version_compare('0.1', $current_version) )
        {
            // upgrade from version 0.1 to 0.2
            $this->upgrade_0_1_to_0_2();
            $current_version = '0.2';
        }

        if(1 == version_compare('0.2', $current_version) )
        {
            // upgrade from version 0.2 to 0.3
            $this->upgrade_0_2_to_0_3();
            $current_version = '0.3';
        }

        if(1 == version_compare('0.3', $current_version) )
        {
            // upgrade from version 0.3 to 0.4
            $this->upgrade_0_3_to_0_4();
            $current_version = '0.4';
        }

        // update system table entry to new version
        $this->setNewVersion( $new_system );
    */
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
     * @param string $version  New version number
     */
    private function setNewVersion( $new_system )
    {
		$this->db->query("UPDATE
							system
						  SET
						  	indicia_version      ='{$new_system['version']}'
						  	indicia_name         ='{$new_system['name']}'
						  	indicia_repository   ='{$new_system['repository']}'
						  	indicia_release_date ='{$new_system['release_date']}'");
    }
}

?>
