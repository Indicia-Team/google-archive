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
 * @subpackage Controllers
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL
 * @link 	http://code.google.com/p/indicia/
 */

/**
 * Controller providing CRUD access to the list of termlists.
 *
 * @package	Core
 * @subpackage Controllers
 */
class Termlist_Controller extends Gridview_Base_Controller {
  public function __construct() {
    parent::__construct('termlist','gv_termlist','termlist/index');
    $this->columns = array(
      'title'=>'',
      'description'=>'',
      'website'=>''
      );
    $this->pagetitle = "Term lists";
    $this->model = ORM::factory('termlist');
    $this->auth_filter = $this->gen_auth_filter;
  }

  public function edit($id,$page_no,$limit)
  {
    if (!$this->record_authorised($id))
    {
      $this->access_denied('record with ID='.$id);
    } else {
      // Generate models
      $this->model->find($id);
      $gridmodel = ORM::factory('gv_termlist',$id);

      // Add grid component
      $grid =	Gridview_Controller::factory($gridmodel,
          $page_no,
          $limit,
          4);
      $grid->base_filter = $this->base_filter;
      $grid->base_filter['parent_id'] = $id;
      $grid->columns = array_intersect_key($grid->columns, array(
        'title'=>'',
        'description'=>''));
      $grid->actionColumns = array(
        'edit' => "termlist/edit/�id�"
      );

      $vArgs = array('table' => $grid->display());

      $this->setView('termlist/termlist_edit', 'Termlist', $vArgs);
    }
  }

  // Auxilliary function for handling Ajax requests from the edit method gridview component
  public function edit_gv($id,$page_no,$limit) {
    $this->auto_render=false;

    $gridmodel = ORM::factory('gv_termlist',$id);

    $grid =	Gridview_Controller::factory($gridmodel,
        $page_no,
        $limit,
        4);
    $grid->base_filter = $this->base_filter;
    $grid->base_filter['parent_id'] = $id;
    $grid->columns = array_intersect_key($grid->columns, array(
      'title'=>'',
      'description'=>''));
    $grid->actionColumns = array(
      'edit' => 'termlist/edit/$id�'
    );
    return $grid->display();
  }

  public function create(){
    $parent = $this->input->post('parent_id', null);
    $this->model->parent_id = $parent;
    if ($parent != null)
    {
      if (!$this->record_authorised($parent))
      {
        $this->access_denied('table to create a record with parent ID='.$parent);
        return;
      }
      $this->model->website_id = $this->model->parent->website_id;
    }

    $vArgs = array('table' => null);
    $this->setView('termlist/termlist_edit', 'Termlist');
  }

  protected function record_authorised ($id)
  {
    if (!is_null($id) AND !is_null($this->auth_filter))
    {
      $termlist=ORM::factory('termlist',$id);
      return (in_array($termlist->website_id, $this->auth_filter['values']));
    }
    return true;
  }

  /**
   * After a submission, override the default return page behaviour so that if the
   * termlist has a parent id, the edit page for that record is returned to.
   */
  protected function get_return_page() {
    if ($this->model->parent_id != null) {
      return "termlist/edit/".$this->model->parent_id."?tab=sublists";
    } else {
      return $this->model->object_name;
    }
  }
}
?>
