<?php

class Taxa_taxon_list_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct(
			'taxa_taxon_list',
			'gv_taxon_lists_taxon',
		       	'taxa_taxon_list/index');
		$this->base_filter = array(
			'parent_id' => null,
			'preferred' => 't');
		$this->columns = array(
			'taxon'=>'',
			'language'=>'',
			);
		$this->pagetitle = "Taxons";
		$this->pageNoUriSegment = 4;
	}
	/**
	 * Override the default page functionality to filter by taxon_list.
	 */
	public function page($taxon_list_id, $page_no, $limit){
		$this->base_filter['taxon_list_id'] = $taxon_list_id;
		$this->pagetitle = "Taxons in ".ORM::factory('taxon_list',$taxon_list_id)->title;
		$this->view->taxon_list_id = $taxon_list_id;
		parent::page($page_no, $limit);
	}

	public function page_gv($taxon_list_id, $page_no, $limit){
		$this->base_filter['taxon_list_id'] = $taxon_list_id;
		$this->view->taxon_list_id = $taxon_list_id;
		parent::page_gv($page_no, $limit);
	}

	private function __getSynonomy($taxon_meaning_id) {
		return ORM::factory('taxa_taxon_list')
			->where(array(
				'preferred' => 'f',
				'taxon_meaning_id' => $taxon_meaning_id
			))->find_all();
	}

	private function __formatScientificSynonomy(ORM_Iterator $res){
		$syn = "";
		foreach ($res as $synonym) {
			if ($synonym->taxon->language->iso == "lat") {
				$syn .= $synonym->taxon->taxon;
				$syn .=	",".$synonym->taxon->authority."\n";
			}
		}
		return $syn;
	}
	
	private function __formatCommonSynonomy(ORM_Iterator $res){
		$syn = "";
		foreach ($res as $synonym) {
			if ($synonym->taxon->language->iso != "lat"){ 
				$syn .= $synonym->taxon->taxon;
				$syn .=	($synonym->taxon->language_id != null) ? 
					",".$synonym->taxon->language->iso."\n" :
					'';
			}
		}
		return $syn;
	}

	public function edit($id,$page_no,$limit) {
		// Generate model
		$model = ORM::factory('taxa_taxon_list',$id);
		$gridmodel = ORM::factory('gv_taxon_lists_taxon');

		// Add grid component
		$grid =	Gridview_Controller::factory($gridmodel,
				$page_no,
				$limit,
				4);
		$grid->base_filter = $this->base_filter;
		$grid->base_filter['parent_id'] = $id;
		$grid->columns = $this->columns;
		$grid->actionColumns = array(
			'edit' => 'taxa_taxon_list/edit/£id£'
		);
		
		// Add metadata panel
		$metadata = new View('templates/metadata');
		$metadata->model = $model->find($id);

		// Add items to view
		$view = new View('taxa_taxon_list/taxa_taxon_list_edit');
		$view->model = $model->find($id);
		$view->taxon_list_id = $model->taxon_list_id;
		$view->metadata = $metadata;
		$view->table = $grid->display();
		$view->synonomy = $this->__formatScientificSynonomy($this->
			__getSynonomy($model->
				taxon_meaning_id));
		$view->commonNames = $this->__formatCommonSynonomy($this->
			__getSynonomy($model->
				taxon_meaning_id));
		// Add everything to the template
		$this->template->title = "Edit ".$model->taxon->taxon;
		$this->template->content = $view;

	}
	// Auxilliary function for handling Ajax requests from the edit method gridview component
	public function edit_gv($id,$page_no,$limit) {
		$this->auto_render=false;

		$gridmodel = ORM::factory('gv_taxon_taxon_list');

		$grid =	Gridview_Controller::factory($gridmodel,
				$page_no,
				$limit,
				4);
		$grid->base_filter = $this->base_filter;
		$grid->base_filter['parent_id'] = $id;
		$grid->columns =  $this->columns;
		$grid->actionColumns = array(
			'edit' => 'taxa_taxon_list/edit/£id£'
		);
		return $grid->display();
	}
	/**
	 * Creates a new taxon given the id of the taxon_list to initially attach it to
	 */
	public function create($taxon_list_id){
		$parent = $this->input->post('parent_id', null);
		$metadata = new View('templates/metadata');
		$metadata->model = ORM::factory('taxa_taxon_list');

		// Create and assign variables to the view
		$view = new View('taxa_taxon_list/taxa_taxon_list_edit');
		$view->model = ORM::factory('taxa_taxon_list');
		$view->model->parent_id = $parent;
		$view->metadata = $metadata;
		$view->table = null;
		$view->taxon_list_id = $taxon_list_id;
		$view->synonomy = null;
		$view->commonNames = null;

		// Templating
		$this->template->title = "Create new taxon";
		$this->template->content = $view;
	}

	/**
	 * Function that takes an array and creates a new taxon entry, passing relevant
	 * values back to the return array.
	 */
	private function __saveTaxon($array) {
		if ($array['taxon_id'] == ''){
			$taxon = ORM::factory('taxon');
		} else {
			$taxon = ORM::factory('taxon', $array['taxon_id']);
		}
		// Look for an existing taxon matching attributes.
		$a = $taxon->where(array(
			'taxon' => $array['taxon'],
			'language_id' => $array['language_id'],
			'authority' => $array['authority']
		))->find()->id;
		if ($a == null){
			// Set scientific
			$scientific = 'f';
			if (ORM::factory('language')->where(array(
				'id' => $array['language_id']))->find()->iso == 'lat'){
					$scientific = 't';
				}				

			//No existing taxon
			$taxon->validate(new Validation(array(
				'taxon' => $array['taxon'],
				'language_id' => $array['language_id'],
				'authority' => $array['authority'],
				'scientific' => $scientific,
				'external_key' => $array['external_key'],
				'search_code' => $array['search_code'],
				'taxon_group_id' => $array['taxon_group_id']
			)), true);
		} else {
			//Already a taxon we can link to
			$taxon->find($a);
		}
		// Update with the new taxon
		$array['taxon_id'] = $taxon->id;
	
		return $array;
	}
	/**
	 * Saves the taxon_list_taxon to the model. Will create a corresponding taxon if one
	 * does not already exist. Will also create a new meaning.
	 */
	public function save() {
		if (! empty($_POST['id'])) {
			$model = ORM::factory('taxa_taxon_list',$_POST['id']);
		} else {
			$model = ORM::factory('taxa_taxon_list');
		}
		/**
		 * We need to submit null for integer fields, 
		 * because an empty string will fail.
		 */
		if ($_POST['parent_id'] == ''){
			$_POST['parent_id'] = null;
		}
		if ($_POST['taxonomic_sort_order'] == ''){
			$_POST['taxonomic_sort_order'] = null;
		}
		/**
		 * We need to generate a new meaning if there isn't one already.
		 */
		if ($_POST['taxon_meaning_id'] == ''){
			//Make a new meaning
			$meaning = ORM::factory('meaning');
			if ($meaning->insert())
			{
				$_POST['taxon_meaning_id'] = $meaning->id;
			} else {
				$_POST['taxon_meaning_id'] = null;
			}
		}
		/**
		 * Work out what the language is - atm, just say English. We should deduce
		 * this from a drop-down list or similar?
		 */
		if ($_POST['language_id'] == ''){
			$_POST['language_id']=1;
		}

		/**
		 * This is the preferred taxon in this list
		 */
		$_POST['preferred']='t';

		/**
		 * We may need to generate a new taxon - but first check if we can
		 * link an old one.
		 */
		
		$_POST = $this->__saveTaxon($_POST);

		/**
		 * Were we instructed to delete the taxon?
		 */
		if ($_POST['submit'] == 'Delete'){
			$_POST['deleted'] = 'true';
		} else {
			$_POST['deleted'] = 'false';
		}

		$validation = new Validation($_POST);
		if ($model->validate($validation, true)) {
			// Okay, the thing saved correctly - we now need to add the common names
			$arrLine = split("\n",$_POST['commonNames']);
			$arrCommonNames = array();

			foreach ($arrLine as $line) {
				$b = preg_split("/(?<!\\\\ ),/",$line); 
				if (count($b) == 2) {
					$arrCommonNames[$b[0]] = array('lang' => trim($b[1]),
						'auth' => '');
				} else {
					$arrCommonNames[$b[0]] = array('lang' => 'eng',
						'auth' => '');
				}
			}
			syslog(LOG_DEBUG, "Number of common names is: ".count($arrCommonNames));

			// Now do the same thing for synonomy
			$arrLine = split("\n", $_POST['synonomy']);
			$arrSyn = array();

			foreach ($arrLine as $line) {
				$b = preg_split("/(?<!\\\\ ),/",$line); 
				if (count($b) == 2) {
					$arrCommonNames[$b[0]] = array('auth' => trim($b[1]),
						'lang' => 'lat');
				} else {
					$arrCommonNames[$b[0]] = array('auth' => '',
						'lang' => 'lat');
				}
			}

			$arrSyn = array_merge($arrSyn, $arrCommonNames);

			$existingSyn = $this->__getSynonomy($_POST['taxon_meaning_id']);

			// Iterate through existing synonomies, discarding those that have
			// been deleted and removing existing ones from the list to add

			foreach ($existingSyn as $syn){
				// Is the taxon from the db in the list of synonyms?
				if (array_key_exists($syn->taxon->taxon, $arrCommonNames) && 
					$arrSyn[$syn->taxon->taxon]['lang'] == 
					$syn->taxon->language->iso &&
					$arrSyn[$syn->taxon->taxon]['auth'] ==
					$syn->taxon->authority)
				{
					array_splice($arrSyn, array_search(
						$syn->taxon, $arrSyn), 1);
					syslog(LOG_DEBUG, "Known synonym: ".$syn->taxon->taxon);
				} else {
					// Synonym has been deleted - remove it from the db
					$syn->taxon->deleted = 't';
					syslog(LOG_DEBUG, "New synonym: ".$syn->taxon->taxon);
					$syn->save();
				}
			}

			// $arraySyn should now be left only with those synonyms 
			// we wish to add to the database

			syslog(LOG_DEBUG, "Synonyms remaining to add: ".count($arrSyn));
			foreach ($arrSyn as $taxon => $syn) {
				
				$lang = $syn['lang'];
				$auth = $syn['auth'];

				// Save a new taxon
				syslog(LOG_DEBUG, "Saving taxon ".$taxon);
				$arr = array(
					'taxon_id' => null,
					'taxon' => $taxon,
					'authority' => $auth,
					'search_code' => $_POST['search_code'],
					'external_key' => $_POST['external_key'],
					'taxon_group_id' => $_POST['taxon_group_id'],
					'language_id' => ORM::factory('language')->where(array(
						'iso' => $lang))->find()->id);
				$arr = $this->__saveTaxon($arr);

				// Save a new taxa_taxon_list instance - we just copy most of
				// the properties but set preferred to false and update
				// the taxon id.

				$syn = $_POST;
				$syn['id'] = '';
				$syn['preferred'] = 'false';
				$syn['taxon_id'] = $arr['taxon_id'];
				ORM::factory('taxa_taxon_list')->
					validate(new Validation($syn), true);
			}

			url::redirect('taxa_taxon_list/'.$model->taxon_list_id);
		} else {
			$this->template->title = 
				$this->GetEditPageTitle($model, 'Taxon instance');
			$metadata = new View('templates/metadata');
			$metadata->model = $model;
			$view = new View('taxa_taxon_list/taxa_taxon_list_edit');
			$view->metadata = $metadata;
			$view->model = $model;
			$view->table = null;
			$view->synonomy = $this->__formatScientificSynonomy($this->
				__getSynonomy($_POST['taxon_meaning_id']));
			$view->commonNames = $this->__formatCommonSynonomy($this->
				__getSynonomy($_POST['taxon_meaning_id']));
			$view->taxon_list_id = $model->taxon_list_id;
			$this->template->content = $view;
		}
	}
}
?>
