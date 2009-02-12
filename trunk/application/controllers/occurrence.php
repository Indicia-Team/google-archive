<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * Occurrence page controller
 *
 *
 * @package Indicia
 * @subpackage Controller
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author xxxxxxx <xxx@xxx.net> / $Author$
 * @copyright xxxx
 * @version $Rev$ / $LastChangedDate$
 */
class Occurrence_controller extends Gridview_Base_Controller {
  
  public function __construct(){
      parent::__construct('occurrence', 'gv_occurrence', 'occurrence/index');
      $this->pageTitle = 'Occurrences';
      $this->model = ORM::factory('occurrence');
      
  }
 
}