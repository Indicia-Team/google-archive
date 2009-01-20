<?php

abstract class Attr_Gridview_Base_Controller extends Indicia_Controller {

	/* Constructor. $modelname = name of the model for the grid.
	 * $viewname = name of the view which contains the grid.
	 * $controllerpath = path the controller from the controllers folder
	 * $viewname and $controllerpath can be ommitted if the names are all the same.
	 */
	public function __construct($modelname, $name=NULL, $websitemodelname=NULL, $gridmodelname=NULL, $viewname=NULL, $controllerpath=NULL) {
		$this->name = is_null($name) ? $modelname : $name;
		$this->model=ORM::factory($modelname);
		$this->websitemodelname = is_null($websitemodelname) ? $modelname.'s_website' : $websitemodelname;
		$this->gridmodelname=is_null($gridmodelname) ? $modelname : $gridmodelname;
		$this->viewname=is_null($viewname) ? $modelname : $viewname;
		$this->controllerpath=is_null($controllerpath) ? $modelname : $controllerpath;
		$this->createpath=$this->controllerpath."/create";
		$this->processpath=$this->controllerpath."/process";
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

	/**
	 * Action for custom attribute/create page.
	 * Displays a page allowing entry of a new custom attribute.
	 */
	// Create function is called with the website_id and optional survey id. These are used to generate the
	// *_attribute_website record after the *_attribute
	public function create() {
		$website = $this->input->post('website_id', null);
		$survey = $this->input->post('survey_id', null);
		if ($website == null)
	   		$this->setError('Invocation error: missing argument', 'You cannot call create '.$this->model->object_name.' without posting a website ID');
        else {
        	$attribute_load = new View('templates/attribute_load', array('website_id' => $website));
        	$this->setView('custom_attribute/custom_attribute_edit',
        					$this->model->object_name,
        					array('website_id' => $website,
        							'survey_id' => $survey,
        							'enabled'=>'',
        							'disabled_input'=>'NO',
        							'attribute_load' => $attribute_load,
        							'name' => $this->name,
        							'processpath' => $this->processpath));
         }
	}

	// edit function is called with id of *_attribute_website record, not the *_attribute	 
	public function edit($id = null) {
		if ($id == null)
        {
	   		$this->setError('Invocation error: missing argument', 'You cannot call edit '.$this->model->object_name.' without an ID');
        }
        else if (!$this->record_authorised($id))
		{
			$this->access_denied('record with ID='.$id);
		}
        else
		{
			// ID points to id of *_attributes_website record.
            $attr_web = ORM::factory($this->websitemodelname, $id);
			$this->model = ORM::factory($this->model->object_name, $attr_web->__get($this->model->object_name.'_id'));
			$count = ORM::factory($this->websitemodelname)->where($this->model->object_name.'_id',$attr_web->occurrence_attribute_id)->find_all()->count();
			if ($count == 1)
				$this->setView('custom_attribute/custom_attribute_edit',
								$this->model->object_name,
								array('website_id' => $attr_web->website_id,
										'survey_id' => $attr_web->restrict_to_survey_id,
										'enabled'=>'',
										'disabled_input'=>'NO',
										'attribute_load' => '',
        								'name' => $this->name,
        								'processpath' => $this->processpath));			
			else
				$this->setView('custom_attribute/custom_attribute_edit',
								$this->model->object_name,
								array('website_id' => $attr_web->website_id,
										'survey_id' => $attr_web->restrict_to_survey_id,
										'enabled'=>'disabled="disabled"',
										'disabled_input'=>'YES',
										'attribute_load' => '',
        								'name' => $this->name,
        								'processpath' => $this->processpath));			
			$this->model->populate_validation_rules();
		}
	}

	public function process() {
		if ($_POST['submit']=='Save' )
			parent::save();
		else if ($_POST['submit']=='Reuse' ) {
			// _POST[load_attr_id] points to id of occurrence_attributes record.
			$this->model = ORM::factory($this->model->object_name, $_POST['load_attr_id']);
	        $this->setView('custom_attribute/custom_attribute_edit',
	        				$this->model->object_name,
	        				array('website_id' => $_POST['website_id'],
	        						'survey_id' => $_POST['survey_id'],
	        						'enabled'=>'disabled="disabled"',
	        						'disabled_input'=>'YES',
	        						'attribute_load' => '',
        							'name' => $this->name,
        							'processpath' => $this->processpath));			
			$this->model->populate_validation_rules();
		} else
	   		$this->setError('Invocation error: Invalid Submit', 'Value of Posted submit variable is invalid');
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

	protected function submit($submission){

        $this->model->submission = $submission;
        if (($id = $this->model->submit()) != null) {
            // Record has saved correctly
            // now save the users_websites records.
			$survey_id = is_numeric($submission['fields']['survey_id']['value']) ? $submission['fields']['survey_id']['value'] : NULL;
        	$attributes_websites = ORM::factory($this->websitemodelname,
						array($this->model->object_name.'_id' => $id
								, 'website_id' => $submission['fields']['website_id']['value']
								, 'restrict_to_survey_id' => $survey_id));
        	$save_array = array(
	        			'id' => $attributes_websites->object_name
        				,'fields' => array($this->model->object_name.'_id' => array('value' => $id)
        									,'website_id' => array('value' => $submission['fields']['website_id']['value'])
        									,'restrict_to_survey_id' => array('value' => $survey_id)
         									)
        				,'fkFields' => array()
        				,'superModels' => array());
       	if ($attributes_websites->loaded)
				$save_array['fields']['id'] = array('value' => $attributes_websites->id);
			$attributes_websites->submission = $save_array;
			$attributes_websites->submit();
       		$this->submit_succ($id);
        } else {
            // Record has errors - now embedded in model
            $this->submit_fail();
        }
    }

	/**
     * Returns to the index view for this controller.
     */
    protected function submit_succ($id) {
        syslog(LOG_DEBUG, "Submitted record ".$id." successfully.");
        url::redirect($this->model->object_name.'?website_id='.$_POST['website_id'].'&survey_id='.$_POST['survey_id']);
    }
	
    /**
     * Returns to the edit page to correct errors - now embedded in the model
     */
    protected function submit_fail() {
        $mn = $this->model->object_name;
        $this->setView("custom_attribute/custom_attribute_edit",
        				ucfirst($mn),
        				array('website_id' => $_POST['website_id'],
        						'survey_id' => $_POST['survey_id'],
        						'enabled'=>'',
        						'disabled_input'=>'NO',
        						'attribute_load' => '',
        						'name' => $this->name,
        						'processpath' => $this->processpath));
		$this->model->populate_validation_rules();
    }

    protected function record_authorised ($id)
	{
		if (!is_null($id) AND !is_null($this->auth_filter))
		{
			$attribute_website = ORM::factory($this->websitemodelname, $id);
			return (in_array($attribute_website->website_id, $this->auth_filter['values']));
		}		
		return true;
	} 
}
