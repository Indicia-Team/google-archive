<?php

/**
 * Indicia, the OPAL Online Recording Toolkit.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 *
 * @package	Core
 * @subpackage Models
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL
 * @link 	http://code.google.com/p/indicia/
 */

/**
 * Model class for the System table.
 *
 * @package	Core
 * @subpackage Models
 * @link	http://code.google.com/p/indicia/wiki/DataModel
 */
class System_Model extends Model
{
    /**
     * @var array $system_data
     */
    private $system_data;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * get indicia version
     * @param string $name Name of the module or application to check the version for
     * @return string
     */
    public function getVersion($name='Indicia')
    {
      $this->getSystemData($name);
      if (isset($this->system_data[$name]))
        $data = $this->system_data[$name];
      return isset($data) ? $data->version : '0.0.0';
    }
    
    /**
     * get indicia version
     *
     * @return string
     */
    public function getLastScheduledTaskCheck($name='Indicia')
    {
      $this->getSystemData($name);
      if (isset($this->system_data[$name]))
        $data = $this->system_data[$name];
      return isset($data) ? $data->last_scheduled_task_check : 0;
    }

    /**
     * Load on demand for records from the system table.
     * @param <type> $name
     */
    private function getSystemData($name) {
      if (!isset($system_data[$name])) {
        $result = $this->db->query("SELECT * FROM \"system\" WHERE name='$name' LIMIT 1");
        if (count($result)>0)
          $this->system_data[$name] = $result[0];
      }
    }
}

?>
