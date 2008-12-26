<?php

abstract class Attr_Gridview_Base_Controller extends Indicia_Controller {

	/* Constructor. $modelname = name of the model for the grid.
	 * $viewname = name of the view which contains the grid.
	 * $controllerpath = path the controller from the controllers folder
	 * $viewname and $controllerpath can be ommitted if the names are all the same.
	 */
	public function __construct($modelname, $gridmodelname=NULL, $viewname=NULL, $controllerpath=NULL) {
		$this->model=ORM::factory($modelname);
		$this->gridmodelname=is_null($gridmodelname) ? $modelname : $gridmodelname;
		$this->viewname=is_null($viewname) ? $modelname : $viewname;
		$this->controllerpath=is_null($controllerpath) ? $modelname : $controllerpath;
		$this->createpath=$this->controllerpath."/create";
		$this->createbutton='New '.$modelname;
		$this->gridmodel = ORM::factory($this->gridmodelname);
		$this->pageNoUriSegment = 3;
		$this->base_filter = array();
		$this->auth_filter = null;
		$this->gen_auth_filter = null;
		$this->columns = $this->gridmodel->table_columns;
		$this->actionColumns = array(
			'edit' => $this->controllerpath."/edit/£id£"
		);
		$this->pagetitle = "Abstract Attribute gridview class - override this title!";
		$this->view = new View($this->viewname);
		parent::__construct();
		
		// If not logged in as a Core admin, restrict access to available websites. 
		if(!$this->auth->logged_in('CoreAdmin')){
			$site_role = (new Site_role_Model('Admin'));
			$websites=ORM::factory('users_website')->where(
					array('user_id' => $_SESSION['auth_user']->id,
							'site_role_id' => $site_role->id))->find_all();
			$website_id_values = array();
			foreach($websites as $website)
				$website_id_values[] = $website->website_id;
			$website_id_values[] = null;
			$this->gen_auth_filter = array('field' => 'website_id', 'values' => $website_id_values);
		}
		
	}

	protected function page_authorised()
	{
		return $this->auth->logged_in();
	}
		
	public function page($page_no, $limit) {
		if ($this->page_authorised() == false) {
			$this->access_denied();
			return;
		}
		$grid =	Attr_Gridview_Controller::factory($this->gridmodel,
			$page_no,
			$limit,
			$this->pageNoUriSegment,
			$this->createpath,
			$this->createbutton);
		$grid->base_filter = $this->base_filter;
		$grid->auth_filter = $this->auth_filter;
		$grid->columns = array_intersect_key($grid->columns, $this->columns);
		$grid->actionColumns = $this->actionColumns;

		// Add table to view
		$this->view->table = $grid->display();
		
		// Templating
		$this->template->title = $this->GetEditPageTitle($this->gridmodel, $this->pagetitle);
		$this->template->content = $this->view;
	}

	public function page_gv($page_no, $limit) {
		$this->auto_render = false;
		$grid =	Attr_Gridview_Controller::factory($this->gridmodel,
			$page_no,
			$limit,
			$this->pageNoUriSegment,
			$this->createpath,
			$this->createbutton);
		$grid->base_filter = $this->base_filter;
		$grid->auth_filter = $this->auth_filter;
		$grid->columns = array_intersect_key($grid->columns, $this->columns);
		$grid->actionColumns = $this->actionColumns;
		return $grid->display();
	}


}
