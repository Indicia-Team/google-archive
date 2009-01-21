<?php

class Person_Controller extends Gridview_Base_Controller {

	public function __construct() {
		parent::__construct('person', 'person', 'person/index');
		$this->columns = array(
			'first_name'=>''
			,'surname'=>''
			,'initials'=>''
			,'email_address'=>''
			,'username'=>''
			,'is_core_user'=>''
		);
		$this->pagetitle = "People";
		$this->model = new Person_Model();
		
		if(!is_null($this->gen_auth_filter)){
			$users_websites=ORM::factory('users_website')->where('site_role_id IS NOT ', null)->in('website_id', $this->gen_auth_filter['values'])->find_all();
			$person_id_values = array();
			foreach($users_websites as $users_website) {
				$user=ORM::factory('user', $users_website->user_id);
				$person_id_values[] = $user->person_id;
			}
			$this->auth_filter = array('field' => 'id', 'values' => $person_id_values);
		}
		
	}

	protected function return_url($return_url)
	{
		return '<input type="hidden" name="return_url" id="return_url" value="'.html::specialchars($return_url).'" />';
	}

	/**
	 * Action for person/create page.
	 * Displays a page allowing entry of a new person.
	 */
	public function create() {
		$this->setView('person/person_edit', 'Person',
					array('return_url' => '')); // will jump back to the gridview on submit
	}

	/**
	 * Action for person/create page.
	 * Displays a page allowing entry of a new person.
	 */
	public function create_from_user() {
		$this->setView('person/person_edit', 'Person',
					array('return_url' => $this->return_url('user'))); // will jump back to the user gridview on submit
	}
	
	/**
	 * Action for person/edit page.
	 * Displays a page allowing modification of an existing person.
	 * This functrion is envoked in 2 different ways:
	 * 1) From the gridview
	 * 2) Direct URL
	 */
	public function edit($id = NULL) {
		if ($id == null)
        {
	   		$this->setError('Invocation error: missing argument', 'You cannot use the edit person functionality without an ID');
        }
        else if (!$this->record_authorised($id))
		{
			$this->access_denied('record with ID='.$id);
		}
        else
		{
			$this->model = new Person_Model($id);
			$this->setView('person/person_edit', 'Person',
					array('return_url' => '')); // will jump back to the gridview on submit
		}
	}

	/**
	 * Subsiduary Action for person/edit page.
	 * Displays a page allowing modification of an existing person.
	 * This is called from a User Record.
	 * When called from User we want to return back to the User gridview on submission for that person
	 */
	public function edit_from_user($id = NULL) {
		if ($id == null)
        {
	   		$this->setError('Invocation error: missing argument', 'You cannot edit a person through edit_from_user() without a Person ID');
        }
        else if (!$this->record_authorised($id))
		{
			$this->access_denied('record with ID='.$id);
		}
        else
        {
        	$this->model = new Person_Model($id);
			$this->setView('person/person_edit', 'Person',
					array('return_url' => $this->return_url('user')));
		}
	}

	/**
     * Returns to the index view for this controller.
     */
    protected function submit_succ($id) {
        Kohana::log("debug", "Submitted record ".$id." successfully.");
		if(isset($_POST['return_url'])) 
			url::redirect($_POST['return_url']);

		url::redirect($this->model->object_name);
    }

	protected function submit_fail() {
		$this->setView('person/person_edit', 'Person',
			array('return_url' => isset($_POST['return_url']) ? $this->return_url($_POST['return_url']) : ''));
	}

	protected function record_authorised ($id)
	{
		if (!is_null($id) AND !is_null($this->auth_filter))
		{
			return (in_array($id, $this->auth_filter['values']));
		}		
		return true;
	}
	
}

?>
