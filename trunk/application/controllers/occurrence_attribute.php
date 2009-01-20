<?php

class Occurrence_attribute_Controller extends Attr_Gridview_Base_Controller {

	public function __construct() {
		parent::__construct('occurrence_attribute', 'gv_occurrence_attribute', 'custom_attribute/index');
		$this->columns = array(
			'website'=>'',
			'survey'=>'',
			'caption'=>'',
			'data_type'=>'');
		$this->pagetitle = "Custom Occurrence Attribute";
		$this->model = ORM::factory('occurrence_attribute');
		$this->auth_filter = $this->gen_auth_filter;
	}


	// Create function is called with the website_id and optional survey id. These are used to generate the
	// occurrence_attribute_website record after the occurrence_attribute

	/**
	 * Action for occurrence_attribute/create page.
	 * Displays a page allowing entry of a new occurrence_attribute.
	 */
	public function create() {
		$website = $this->input->post('website_id', null);
		$survey = $this->input->post('survey_id', null);
		if ($website == null)
	   		$this->setError('Invocation error: missing argument', 'You cannot call create occurrence_attribute without posting a website ID');
        else {
        	$attribute_load = new View('templates/attribute_load', array('website_id' => $website));
        	$this->setView('custom_attribute/occurrence_attribute_edit', 'occurrence_attribute', array('website_id' => $website, 'survey_id' => $survey, 'enabled'=>'', 'disabled_input'=>'NO', 'attribute_load' => $attribute_load));
         }
	}

	// edit function is called with id of occurrence_attribute_website record, not the occurrence_attribute	 
	public function edit($id = null) {
		if ($id == null)
        {
	   		$this->setError('Invocation error: missing argument', 'You cannot call edit occurrence_attribute without an ID');
        }
        else if (!$this->record_authorised($id))
		{
			$this->access_denied('record with ID='.$id);
		}
        else
		{
			// ID points to id of occurrence_attributes_website record.
            $occ_attr_web = new Occurrence_attributes_website_Model($id);
			$this->model = new Occurrence_attribute_Model($occ_attr_web->occurrence_attribute_id);
			$count = ORM::factory('occurrence_attributes_website')->where('occurrence_attribute_id',$occ_attr_web->occurrence_attribute_id)->find_all()->count();
			if ($count == 1)
				$this->setView('custom_attribute/occurrence_attribute_edit', 'Occurrence_attribute', array('website_id' => $occ_attr_web->website_id, 'survey_id' => $occ_attr_web->restrict_to_survey_id, 'enabled'=>'', 'disabled_input'=>'NO', 'attribute_load' => ''));			
			else
				$this->setView('custom_attribute/occurrence_attribute_edit', 'Occurrence_attribute', array('website_id' => $occ_attr_web->website_id, 'survey_id' => $occ_attr_web->restrict_to_survey_id, 'enabled'=>'disabled="disabled"', 'disabled_input'=>'YES', 'attribute_load' => ''));			
			$this->model->populate_validation_rules();
		}
	}

	public function process() {
		if ($_POST['submit']=='Save' )
			parent::save();
		else if ($_POST['submit']=='Reuse' ) {
			// _POST[load_attr_id] points to id of occurrence_attributes record.
			$this->model = new Occurrence_attribute_Model($_POST['load_attr_id']);
	        $this->setView('custom_attribute/occurrence_attribute_edit', 'Occurrence_attribute', array('website_id' => $_POST['website_id'], 'survey_id' => $_POST['survey_id'], 'enabled'=>'disabled="disabled"', 'disabled_input'=>'YES', 'attribute_load' => ''));			
			$this->model->populate_validation_rules();
		} else
	   		$this->setError('Invocation error: Invalid Submit', '');
	}
	
	protected function submit($submission){

        $this->model->submission = $submission;
        if (($id = $this->model->submit()) != null) {
            // Record has saved correctly
            // now save the users_websites records.
			$survey_id = is_numeric($submission['fields']['survey_id']['value']) ? $submission['fields']['survey_id']['value'] : NULL;
        	$occurrence_attributes_websites = ORM::factory('occurrence_attributes_website',
						array('occurrence_attribute_id' => $id
								, 'website_id' => $submission['fields']['website_id']['value']
								, 'restrict_to_survey_id' => $survey_id));
        	$save_array = array(
	        			'id' => $occurrence_attributes_websites->object_name
        				,'fields' => array('occurrence_attribute_id' => array('value' => $id)
        									,'website_id' => array('value' => $submission['fields']['website_id']['value'])
        									,'restrict_to_survey_id' => array('value' => $survey_id)
         									)
        				,'fkFields' => array()
        				,'superModels' => array());
       	if ($occurrence_attributes_websites->loaded)
				$save_array['fields']['id'] = array('value' => $occurrence_attributes_websites->id);
			$occurrence_attributes_websites->submission = $save_array;
			$occurrence_attributes_websites->submit();
       		$this->submit_succ($id);
        } else {
            // Record has errors - now embedded in model
            $this->submit_fail();
        }
    }
		    
    protected function record_authorised ($id)
	{
		if (!is_null($id) AND !is_null($this->auth_filter))
		{
			$occurrence_attribute_website = new Occurrence_attributes_website_Model($id);
			return (in_array($occurrence_attribute_website->website_id, $this->auth_filter['values']));
		}		
		return true;
	} 
}

?>
