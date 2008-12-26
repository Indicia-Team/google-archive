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

	/**
	 * Action for occurrence_attribute/create page.
	 * Displays a page allowing entry of a new occurrence_attribute.
	 */
	public function create() {
		$this->model = ORM::factory('occurrence_attribute');	
		$this->setView('custom_attribute/occurrence_attribute_edit', 'Occurrence_attribute');
		$website = $this->input->post('website_id', null);
		if ($website == null)
        {
	   		$this->setError('Invocation error: missing argument', 'You cannot call create occurrence_attribute without posting a website ID');
        }
		$this->view->website_id = $website;
	}

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
			$occurrence_attribute = new Occurrence_attribute_Model($id);  	    
			$this->template->title = $this->GetEditPageTitle($occurrence_attribute, 'Occurrence_attribute');
			$view = new View('custom_attribute/occurrence_attribute_edit');
			$view->model = $occurrence_attribute;
			$view->metadata = $this->GetMetadataView($occurrence_attribute);
			$this->template->content = $view;
		}
	}

	public function save() {
		if (! empty($_POST['id']))
			$occurrence_attribute = new Occurrence_attribute_Model($_POST['id']);
		else
			$occurrence_attribute = new Occurrence_attribute_Model();
		$_POST = new Validation($_POST);
		if ($occurrence_attribute->validate($_POST, TRUE)) {
			url::redirect('occurrence_attribute');
		} else {
			// errors are now embedded in the model
		    $this->template->title = $this->GetEditPageTitle($occurrence_attribute, 'Occurrence_attribute');
			$view = new View('custom_attribute/occurrence_attribute_edit');
			$view->model = $occurrence_attribute;
			$view->metadata = $this->GetMetadataView($occurrence_attribute);
			$this->template->content = $view;
		}

	}
	

    protected function record_authorised ($id)
	{
		if (!is_null($id) AND !is_null($this->auth_filter))
		{
			$occurrence_attribute = new Occurrence_attribute_Model($id);
			return (in_array($occurrence_attribute->website_id, $this->auth_filter['values']));
		}		
		return true;
	} 
}

?>
