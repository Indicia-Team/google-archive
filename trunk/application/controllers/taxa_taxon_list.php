<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * Taxa_taxon_list page controller
 *
 *
 * @package Indicia
 * @subpackage Controller
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author xxxxxxx <xxx@xxx.net> / $Author$
 * @copyright xxxx
 * @version $Rev$ / $LastChangedDate$
 */

class Taxa_taxon_list_Controller extends Gridview_Base_Controller {
	public function __construct() {
		parent::__construct(
			'taxa_taxon_list',
			'gv_taxon_lists_taxon',
		       	'taxa_taxon_list/index');
		$this->base_filter = array(
			'parent_id' => null,
			'deleted' => 'f',
			'preferred' => 't');
		$this->columns = array(
			'taxon'=>'',
			'authority'=>'',
			'language'=>'',
			);
		$this->pagetitle = "Taxons";
		$this->pageNoUriSegment = 4;
		$this->model = ORM::factory('taxa_taxon_list');
	}

	private function getSynonomy($taxon_meaning_id) {
		return ORM::factory('taxa_taxon_list')
			->where(array(
				'preferred' => 'f',
				'deleted' => 'f',
				'taxon_meaning_id' => $taxon_meaning_id
			))->find_all();
	}

	private function formatScientificSynonomy(ORM_Iterator $res){
		$syn = "";
		foreach ($res as $synonym) {
			if ($synonym->taxon->language->iso == "lat") {
				$syn .= $synonym->taxon->taxon;
				$syn .=	",".$synonym->taxon->authority."\n";
			}
		}
		return $syn;
	}
	
	private function formatCommonSynonomy(ORM_Iterator $res){
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

	public function edit($id,$page_no,$limit) {
		// Generate model
		$this->model->find($id);
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
		
		// Add items to view
		$vArgs = array(
			'taxon_list_id' => $this->model->taxon_list_id,
			'table' => $grid->display(),
			'synonomy' => $this->formatScientificSynonomy($this->
			getSynonomy($this->model->
				taxon_meaning_id)),
			'commonNames' => $this->formatCommonSynonomy($this->
			getSynonomy($this->model->
				taxon_meaning_id))
			);
		$this->setView('taxa_taxon_list/taxa_taxon_list_edit', 'Taxon', $vArgs);

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
		$this->model = ORM::factory('taxa_taxon_list');
		$parent = $this->input->post('parent_id', null);
		$this->model->parent_id = $parent;

		$vArgs = array(
			'table' => null,
			'taxon_list_id' => $taxon_list_id,
			'synonomy' => null,
			'commonNames' => null);

		$this->setView('taxa_taxon_list/taxa_taxon_list_edit', 'Taxon', $vArgs);
			
	}

	public function save(){
		$_POST['preferred'] = 't';
		parent::save();
	}

	protected function wrap($array) {

		$sa = array(
			'id' => 'taxa_taxon_list',
			'fields' => array(),
			'fkFields' => array(),
			'subModels' => array(),
			'metaFields' => array()
		);

		// Declare which fields we consider as native to this model
		$nativeFields = array_intersect_key($array, $this->model->table_columns);

		// Use the parent method to wrap these
		$sa = parent::wrap($nativeFields);

		// Declare child models
		if (array_key_exists('taxon_meaning_id', $array) == false || 
			$array['taxon_meaning_id'] == '') {
				$sa['subModels'][] = array(
					'fkId' => 'taxon_meaning_id',
					'model' => parent::wrap(
						array_intersect_key($array, ORM::factory('taxon_meaning')
						->table_columns), false, 'taxon_meaning'));
			}

		if (array_key_exists('taxon_id', $array) == false || 
			$array['taxon_id'] == '') {
				$sa['subModels'][] = array(
					'fkId' => 'taxon_id',
					'model' => parent::wrap(
						array_intersect_key($array, ORM::factory('taxon')
						->table_columns), false, 'taxon'));
			}

		$sa['metaFields']['synonomy'] = array(
			'value' => $array['synonomy']
		);

		$sa['metaFields']['commonNames'] = array(
			'value' => $array['commonNames']
		);

		return $sa;
	}

	/**
	 * Overrides the fail functionality to add args to the view.
	 */
	protected function submit_fail(){
		$mn = $this->model->object_name;
		$vArgs = array(
			'taxon_list_id' => $this->model->taxon_list_id,
			'synonomy' => null,
			'commonNames' => null,
		);
		$this->setView($mn."/".$mn."_edit", ucfirst($mn), $vArgs);
	}

	/**
	 * Overrides the success function to add in synonomies
	 */
	protected function submit_succ($id){
		// Okay, the thing saved correctly - we now need to add the common names
		$arrLine = split("\n",trim($this
			->model->submission['metaFields']['commonNames']['value']));
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
		$arrLine = split("\n", trim($this
			->model->submission['metaFields']['synonomy']['value']));
		$arrSyn = array();

		foreach ($arrLine as $line) {
			$b = preg_split("/(?<!\\\\ ),/",$line); 
			if (count($b) == 2) {
				$arrSyn[$b[0]] = array('auth' => trim($b[1]),
					'lang' => 'lat');
			} else {
				$arrSyn[$b[0]] = array('auth' => '',
					'lang' => 'lat');
			}
		}
		syslog(LOG_DEBUG, "Number of synonyms is: ".count($arrSyn));

		$arrSyn = array_merge($arrSyn, $arrCommonNames);

		syslog(LOG_DEBUG, "Looking for existing terms with meaning ".$this->model->taxon_meaning_id);
		$existingSyn = $this->getSynonomy($this->model->taxon_meaning_id);

		// Iterate through existing synonomies, discarding those that have
		// been deleted and removing existing ones from the list to add

		foreach ($existingSyn as $syn){
			// Is the taxon from the db in the list of synonyms?
			if (array_key_exists($syn->taxon->taxon, $arrSyn) && 
				$arrSyn[$syn->taxon->taxon]['lang'] == 
				$syn->taxon->language->iso &&
				$arrSyn[$syn->taxon->taxon]['auth'] ==
				$syn->taxon->authority)
			{
				array_splice($arrSyn, array_search(
					$syn->taxon->taxon, $arrSyn), 1);
				syslog(LOG_DEBUG, "Known synonym: ".$syn->taxon->taxon);
			} else {
				// Synonym has been deleted - remove it from the db
				$syn->deleted = 't';
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

			// Wrap a new submission
			syslog(LOG_DEBUG, "Wrapping submission for synonym ".$taxon);

			$syn = $_POST;
			$syn['taxon_id'] = null;
			$syn['taxon'] = $taxon;
			$syn['authority'] = $auth;
			$syn['language_id'] = ORM::factory('language')->where(array(
					'iso' => $lang))->find()->id;
			$syn['id'] = '';
			$syn['preferred'] = 'f';
			$syn['taxon_meaning_id'] = $this->model->taxon_meaning_id;

			$sub = $this->wrap($syn);
			$this->model->submission = $sub;
			$this->model->submit();
		}

		url::redirect('taxa_taxon_list/'.$this->model->taxon_list_id);
	}
}
?>
