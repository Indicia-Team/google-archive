<?php

class Sample_attribute_Controller extends Attr_Gridview_Base_Controller {

	public function __construct() {
		parent::__construct('sample_attribute', 'gv_sample_attribute', 'custom_attribute/index');
		$this->columns = array(
			'website'=>'',
			'survey'=>'',
			'caption'=>'',
			'data_type'=>'');
		$this->pagetitle = "Custom Sample Attribute";
		$this->model = ORM::factory('sample_attribute');
		$this->auth_filter = $this->gen_auth_filter;
	}

	/**
	 * Action for sample_attribute/create page.
	 * Displays a page allowing entry of a new sample_attribute.
	 */
	public function create() {
		$this->model = ORM::factory('sample_attribute');	
		$this->setView('custom_attribute/sample_attribute_edit', 'Sample_attribute');
		$website = $this->input->post('website_id', null);
		if ($website == null)
        {
	   		$this->setError('Invocation error: missing argument', 'You cannot call create sample_attribute without posting a website ID');
        }
		$this->view->website_id = $website;
	}

	public function edit($id = null) {
		if ($id == null)
        {
	   		$this->setError('Invocation error: missing argument', 'You cannot call edit sample_attribute without an ID');
        }
        else if (!$this->record_authorised($id))
		{
			$this->access_denied('record with ID='.$id);
		}
        else
		{
			$sample_attribute = new Sample_attribute_Model($id);  	    
			$this->template->title = $this->GetEditPageTitle($sample_attribute, 'Sample_attribute');
			$view = new View('custom_attribute/sample_attribute_edit');
			$view->model = $sample_attribute;
			$view->metadata = $this->GetMetadataView($sample_attribute);
			$this->template->content = $view;
		}
	}

	public function save() {
		if (! empty($_POST['id']))
			$sample_attribute = new Sample_attribute_Model($_POST['id']);
		else
			$sample_attribute = new Sample_attribute_Model();
		$_POST = new Validation($_POST);
		if ($sample_attribute->validate($_POST, TRUE)) {
			url::redirect('sample_attribute');
		} else {
			// errors are now embedded in the model
		    $this->template->title = $this->GetEditPageTitle($sample_attribute, 'Sample_attribute');
			$view = new View('custom_attribute/sample_attribute_edit');
			$view->model = $sample_attribute;
			$view->metadata = $this->GetMetadataView($sample_attribute);
			$this->template->content = $view;
		}

	}
	

    protected function record_authorised ($id)
	{
		if (!is_null($id) AND !is_null($this->auth_filter))
		{
			$sample_attribute = new Sample_attribute_Model($id);
			return (in_array($sample_attribute->website_id, $this->auth_filter['values']));
		}		
		return true;
	} 
}

?>
