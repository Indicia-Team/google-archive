<?php

class Location_attribute_Controller extends Attr_Gridview_Base_Controller {

	public function __construct() {
		parent::__construct('location_attribute', 'gv_location_attribute', 'custom_attribute/index');
		$this->columns = array(
			'website'=>'',
			'survey'=>'',
			'caption'=>'',
			'data_type'=>'');
		$this->pagetitle = "Custom Location Attribute";
		$this->model = ORM::factory('location_attribute');
		$this->auth_filter = $this->gen_auth_filter;
	}

	/**
	 * Action for location_attribute/create page.
	 * Displays a page allowing entry of a new location_attribute.
	 */
	public function create() {
		$this->model = ORM::factory('location_attribute');	
		$this->setView('custom_attribute/location_attribute_edit', 'Location_attribute');
		$website = $this->input->post('website_id', null);
		if ($website == null)
        {
	   		$this->setError('Invocation error: missing argument', 'You cannot call create location_attribute without posting a website ID');
        }
		$this->view->website_id = $website;
	}

	public function edit($id = null) {
		if ($id == null)
        {
	   		$this->setError('Invocation error: missing argument', 'You cannot call edit location_attribute without an ID');
        }
        else if (!$this->record_authorised($id))
		{
			$this->access_denied('record with ID='.$id);
		}
        else
		{
			$location_attribute = new Location_attribute_Model($id);  	    
			$this->template->title = $this->GetEditPageTitle($location_attribute, 'Location_attribute');
			$view = new View('custom_attribute/location_attribute_edit');
			$view->model = $location_attribute;
			$view->metadata = $this->GetMetadataView($location_attribute);
			$this->template->content = $view;
		}
	}

	public function save() {
		if (! empty($_POST['id']))
			$location_attribute = new Location_attribute_Model($_POST['id']);
		else
			$location_attribute = new Location_attribute_Model();
		$_POST = new Validation($_POST);
		if ($location_attribute->validate($_POST, TRUE)) {
			url::redirect('location_attribute');
		} else {
			// errors are now embedded in the model
		    $this->template->title = $this->GetEditPageTitle($location_attribute, 'Location_attribute');
			$view = new View('custom_attribute/location_attribute_edit');
			$view->model = $location_attribute;
			$view->metadata = $this->GetMetadataView($location_attribute);
			$this->template->content = $view;
		}

	}
	

    protected function record_authorised ($id)
	{
		if (!is_null($id) AND !is_null($this->auth_filter))
		{
			$location_attribute = new Location_attribute_Model($id);
			return (in_array($location_attribute->website_id, $this->auth_filter['values']));
		}		
		return true;
	} 
}

?>
