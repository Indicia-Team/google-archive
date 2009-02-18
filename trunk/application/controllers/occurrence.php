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
* @author Nicholas Clarke <xxx@xxx.net> / $Author$
* @copyright xxxx
* @version $Rev$ / $LastChangedDate$
*/
class Occurrence_controller extends Gridview_Base_Controller {
  
  public function __construct()
  {
    parent::__construct('occurrence', 'gv_occurrence', 'occurrence/index');
    $this->pageTitle = 'Occurrences';
    $this->model = ORM::factory('occurrence');
    $this->columns = array
    (
    'taxon' => '',
    'entered_sref' => '',
    'entered_sref_system' => '',
    'vague_date' => ''
    );
  }
  
  /**
  * Action for occurrence/create page/
  * Displays a page allowing entry of a new occurrence.
  */
  public function create()
  {
    if (!$this->page_authorised())
    {
      $this->access_denied();
    }
    else
    {
      $this->setView('occurrence/occurrence_edit', 'Occurrence');
    }
  }
  
  /**
  * Action for occurrence/edit page
  * Edit website data
  */
  public function edit($id  = null, $page_no, $limit)
  {
    if (!$this->page_authorised())
    {
      $this->access_denied();
    }
    else if ($id == null)
    {
      $this->setError('Invocation error: missing argument', 'You cannot call edit occurrence without an ID');
    }
    else
    {
      $this->model = ORM::factory('occurrence', $id);
      $gridmodel = ORM::factory('occurrence_comment');
      $grid = Gridview_Controller::factory($gridmodel,	$page_no,  $limit, 4);
      $grid->base_filter = array('occurrence_id' => $id, 'deleted' => 'f');
      $grid->columns = array('comment' => '', 'updated_on' => ''); 
      $vArgs = array('comments' => $grid->display());
      $this->setView('occurrence/occurrence_edit', 'Occurrence', $vArgs);
    }
  }
  
  public function edit_gv($id = null, $page_no, $limit)
  {
    $this->auto_render = false;
    $gridmodel = ORM::factory('occurrence_comment');
    $grid = Gridview_Controller::factory($gridmodel,	$page_no,  $limit, 4);
    $grid->base_filter = array('occurrence_id' => $id, 'deleted' => 'f');
    $grid->columns = array('comment' => '', 'updated_on' => ''); 
    
    return $grid->display();
  }
}