<?php

class Termlist_Controller extends Opal_Controller {
	public function __construct() {
		parent::__construct();
	}
	public function index(){
		url::redirect('termlist/page/1/5');
	}
	public function page($page_no,$limit) {
		$model = ORM::factory('termlist');
		if (!request::is_ajax()) {
			// Generate a new termlist object
			$this->template->title = "Pagination";
			$this->template->message = 'Termlists grid'; 
			$termlist = new View('termlist');
			$termlist->termtable = 
				Gridview_Controller::factory($model,$page_no,$limit)->display();
			$this->template->content = $termlist;
		} else {
			$this->auto_render = false;
			return $termlist->termtable = 
				Gridview_Controller::factory($model,$page_no,$limit)->display();
		}

	}
}
