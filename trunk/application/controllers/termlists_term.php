<?php

class Termlists_term_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct(
			'termlists_term',
			'gv_termlists_term',
		       	'termlists_term/index');
		$this->base_filter = array(
			'parent_id' => null,
			'preferred' => 't');
		$this->columns = array(
			'term'=>'',
			'language'=>'',
			);
		$this->pagetitle = "Terms";
	}
	/**
	 * Override the default page functionality to filter by termlist.
	 */
	public function page($termlist_id, $page_no, $limit){
		$this->base_filter['termlist_id'] = $termlist_id;
		$this->pagetitle = "Terms in ".ORM::factory('termlist',$termlist_id)->title;
		$this->view->termlist_id = $termlist_id;
		parent::page($page_no, $limit);
	}

	public function page_gv($termlist_id, $page_no, $limit){
		$this->base_filter['termlist_id'] = $termlist_id;
	}

	private function __getSynonomy($meaning_id) {
		$synonyms = ORM::factory('termlists_term')
			->where(array(
				'preferred' => 'f',
				'meaning_id' => $meaning_id
			))->find_all();
		return $synonyms;
	}

	private function __formatSynonomy(ORM_Iterator $res){
		$syn = "";
		foreach ($res as $synonym) {
			$syn << $synonym->term-term.",".$synonym->term->language."\n";
		}
		return $syn;
	}

	public function edit($id,$page_no,$limit) {
		// Generate model
		$model = ORM::factory('termlists_term',$id);
		$gridmodel = ORM::factory('gv_termlists_term');

		// Add grid component
		$grid =	Gridview_Controller::factory($gridmodel,
				$page_no,
				$limit,
				4);
		$grid->base_filter = $this->base_filter;
		$grid->base_filter['parent_id'] = $id;
		$grid->columns = $this->columns;
		
		// Add metadata panel
		$metadata = new View('templates/metadata');
		$metadata->model = $model->find($id);

		// Calculate and format the synonomy
		$synonyms = $this->__getSynonomy($model->meaning_id);
		// Add items to view
		$view = new View('termlists_term/termlists_term_edit');
		$view->model = $model->find($id);
		$view->metadata = $metadata;
		$view->table = $grid->display();
		$view->synonomy = $this->__formatSynonomy($this->
			__getSynonomy($model->
				meaning_id));
		// Add everything to the template
		$this->template->title = "Edit ".$model->term->term;
		$this->template->content = $view;

	}
	// Auxilliary function for handling Ajax requests from the edit method gridview component
	public function edit_gv($id,$page_no,$limit) {
		$this->auto_render=false;

		$gridmodel = ORM::factory('gv_term_termlist');

		$grid =	Gridview_Controller::factory($gridmodel,
				$page_no,
				$limit,
				4);
		$grid->base_filter = $this->base_filter;
		$grid->base_filter['parent_id'] = $id;
		$grid->columns =  $this->columns;
		return $grid->display();
	}
	/**
	 * Creates a new term given the id of the termlist to initially attach it to
	 */
	public function create($termlist_id){
		$parent = $this->input->post('parent_id', null);
		$metadata = new View('templates/metadata');
		$metadata->model = ORM::factory('termlists_term');

		// Create and assign variables to the view
		$view = new View('termlists_term/termlists_term_edit');
		$view->model = ORM::factory('termlists_term');
		$view->model->parent_id = $parent;
		$view->metadata = $metadata;
		$view->table = null;
		$view->termlist_id = $termlist_id;
		$view->synonomy = null;

		// Templating
		$this->template->title = "Create new term";
		$this->template->content = $view;
	}
	/**
	 * Saves the termlist_term to the model. Will create a corresponding term if one
	 * does not already exist. Will also create a new meaning.
	 */
	public function save() {
		if (! empty($_POST['id'])) {
			$tt = ORM::factory('termlists_term',$_POST['id']);
		} else {
			$tt = ORM::factory('termlists_term');
		}
		/**
		 * We need to submit null for integer fields, 
		 * because an empty string will fail.
		 */
		if ($_POST['parent_id'] == ''){
			$_POST['parent_id'] = null;
		}
		/**
		 * We need to generate a new meaning if there isn't one already.
		 */
		if ($_POST['meaning_id'] == ''){
			//Make a new meaning
			$meaning = ORM::factory('meaning');
			if ($meaning->save())
			{
				$_POST['meaning_id'] = $meaning->id;
			} else {
				$_POST['meaning_id'] = null;
			}
		}
		/**
		 * Work out what the language is - atm, just say English. We should deduce
		 * this from a drop-down list or similar?
		 */
		$_POST['language_id']=4;
		/**
		 * We may need to generate a new term - but first check if we can
		 * link an old one.
		 */
		if ($_POST['term_id'] == ''){
			// Look for an existing term matching attributes.
			$a = ORM::factory('term')->where(array(
				'term' => $_POST['term'],
				'language_id' => $_POST['language_id']
			))->find()->id;
			if ($a != null){
				//No existing term
				$term = ORM::factory('term');
				$term->term = $_POST['term'];
				$term->language_id = $_POST['language_id'];
				$term->validate(new Validation(array(
					'term' => $_POST['term'],
					'language_id' => $_POST['language_id']
				)), true);
			} else {
				//Already a term we can link to
				$term = ORM::factory('term',$a);
			}
			// Update with the new term
			$_POST['term_id'] = $term->id;
		}
		/**
		 * Were we instructed to delete the term?
		 */
		if ($_POST['submit'] == 'Delete'){
			$_POST['deleted'] = 'true';
		} else {
			$_POST['deleted'] = 'false';
		}

		$_POST = new Validation($_POST);
		if ($tt->validate($_POST, true)) {
			// Okay, the thing saved correctly - we now need to add the synonomies
			$enteredSyn = split("\n",$_POST['synomony']);
			url::redirect('termlists_term');
		} else {
			$this->template->title = $this->GetEditPageTitle($tt, 'Term instance');
			$metadata = new View('templates/metadata');
			$metadata->model = $tt;
			$view = new View('termlists_term/termlists_term_edit');
			$view->metadata = $metadata;
			$view->model = $tt;
			$view->table = null;
			$view->synonomy = $this->__formatSynonomy($this->
				__getSynonomy($_POST['meaning_id']));
			$this->template->content = $view;
		}
	}
}
?>
