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
 * @package	Core.Controllers
 * @subpackage Controllers
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL
 * @link 	http://code.google.com/p/indicia/
 */

 defined('SYSPATH') or die('No direct script access.');

/**
 * Controller class for the home page.
 *
 * @package Core
 * @subpackage Controllers
 */
class Home_Controller extends Indicia_Controller {

  public function index()
  {
    //$this->check_for_upgrade();

    $view = new View('home');
    $this->template->title='Indicia';
    $system = new System_Model;
    $view->db_version=$system->getVersion();
    $view->app_version=kohana::config('version.version');
    $this->template->content=$view;
  }
  
  /**
   * Action called when an formal upgrade is required.      
   */
  public function upgrade() 
  {
    $upgrader = new Upgrade_Model();
    $result = $upgrader->run();
    $view = new View('upgrade');
    $this->template->title='Indicia Upgrade';
    $system = new System_Model;
    $view->db_version=$system->getVersion();
    $view->app_version=kohana::config('version.version');    
    if (!$result===true) {
      $view->error = $result;
    }
    $this->template->content=$view;
  }
 
}

?>
