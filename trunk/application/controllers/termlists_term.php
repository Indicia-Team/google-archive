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
			$syn << $synonym->term->term.",".$synonym->term->language->iso."\n";
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

		// Add items to view
		$view = new View('termlists_term/termlists_term_edit');
		$view->model = $model->find($id);
		$view->termlist_id = $model->termlist_id;
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
	 * Function that takes an array and creates a new term entry, passing relevant
	 * values back to the return array.
	 */
	private function __saveTerm($array) {
		if ($array['term_id'] == ''){
			$term = ORM::factory('term');

			// Look for an existing term matching attributes.
			$a = $term->where(array(
				'term' => $array['term'],
				'language_id' => $array['language_id']
			))->find()->id;
			if ($a == null){
				//No existing term
				$term->term = $array['term'];
				$term->language_id = $array['language_id'];
				$term->validate(new Validation(array(
					'term' => $array['term'],
					'language_id' => $array['language_id']
				)), true);
			} else {
				//Already a term we can link to
				$term->find($a);
			}
			// Update with the new term
			$array['term_id'] = $term->id;
		}
		return $array;
	}
	/**
	 * Saves the termlist_term to the model. Will create a corresponding term if one
	 * does not already exist. Will also create a new meaning.
	 */
	public function save() {
		if (! empty($_POST['id'])) {
			$model = ORM::factory('termlists_term',$_POST['id']);
		} else {
			$model = ORM::factory('termlists_term');
		}
		/**
		 * We need to submit null for integer fields, 
		 * because an empty string will fail.
		 */
		if ($_POST['parent_id'] == ''){
			$_POST['parent_xzd'] = null;
		}
		/**
		 * We need to generate a new meaning if there isn't one already.
		 */
		if ($_POST['meaning_id'] == ''){
			//Make a new meaning
			$meaning = ORM::factory('meaning');
			if ($meaning->save())
			{
#				$_POST['meaning_id'] = $meaning->id;
				$_POST['meaning_id'] = 1;
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
		 * This is the preferred term in this list
		 */
		$_POST['preferred']='t';

		/**
		 * We may need to generate a new term - but first check if we can
		 * link an old one.
		 */
		$_POST = $this->__saveTerm($_POST);

		/**
		 * Were we instructed to delete the term?
		 */
		if ($_POST['submit'] == 'Delete'){
			$_POST['deleted'] = 'true';
		} else {
			$_POST['deleted'] = 'false';
		}

		$_POST = new Validation($_POST);
		if ($model->validate($_POST, true)) {
			// Okay, the thing saved correctly - we now need to add the synonomies
			$arrLine = split("\n",$_POST['synonomy']);
			$arrSyn = array();

			foreach ($arrLine as $line) {
				 $b = preg_split("/(?<!\\\\ ),/",$line); 
				 if (count($b) == 2) {
					 $arrSyn[$b[0]] = trim($b[1]);
				 } else {
					 $arrSyn[$b[0]] = "eng";
				 }
			}


			$existingSyn = $this->__getSynonomy($_POST['meaning_id']);

			// Iterate through existing synonomies, discarding those that have
			// been deleted and removing existing ones from the list to add

			foreach ($existingSyn as $syn){
				// Is the term from the db in the list of synonyms?
				if (array_key_exists($syn->term->term, $arrSyn) && 
					$arrSyn[$syn->term->term] == $syn->language->iso) {
					array_splice($arrSyn, array_search(
						$syn->term, $arrSyn));
				} else {
					// Synonym has been deleted - remove it from the db
					$syn->deleted = 'f';
					$syn->save();
				}
			}

			// $arraySyn should now be left only with those synonyms 
			// we wish to add to the database

			foreach ($arrSyn as $term => $lang){
				// Save a new term
				$arr = array(
					'term_id' => null,
					'term' => $term,
					'language_id' => ORM::factory('language')->where(array(
						'iso' => $lang))->find()->id);
				$arr = $this->__saveTerm($arr);

				// Save a new termlists_term instance - we just copy most of
				// the properties but set preferred to false and update
				// the term id.

				$syn = $_POST;
				$syn['id'] = null;
				$syn['preferred'] = 'false';
				$syn['term_id'] = $arr['term_id'];
				ORM::factory('termlists_term')->validate($syn, true);
			}

			url::redirect('termlists_term');
		} else {
			$this->template->title = $this->GetEditPageTitle($model, 'Term instance');
			$metadata = new View('templates/metadata');
			$metadata->model = $model;
			$view = new View('termlists_term/termlists_term_edit');
			$view->metadata = $metadata;
			$view->model = $model;
			$view->table = null;
			$view->synonomy = $this->__formatSynonomy($this->
				__getSynonomy($_POST['meaning_id']));
			$view->termlist_id = $model->termlist_id;
			$this->template->content = $view;
		}
	}
}
?>
