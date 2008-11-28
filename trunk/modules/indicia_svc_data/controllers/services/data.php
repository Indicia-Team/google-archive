<?php

class Data_Controller extends Service_Base_Controller {
	protected $model;
	protected $entity;
	protected $viewname;
	protected $foreign_keys;
	protected $view_columns;
	protected $db;

	/**
	 * Provides the /services/data/language service.
	 * Retrieves details of a single language.
	 */
	public function language()
	{
		$this->handle_call('language');
	}

	/**
	 * Provides the /services/data/location service.
	 * Retrieves details of a single survey.
	 */
	public function location()
	{
		$this->handle_call('location');
	}

	/**
	 * Provides the /services/data/person service.
	 * Retrieves details of a single person.
	 */
	public function person()
	{
		$this->handle_call('person');
	}

	/**
	 * Provides the /services/data/survey service.
	 * Retrieves details of a single survey.
	 */
	public function survey()
	{
		$this->handle_call('survey');
	}

	/**
	 * Provides the /services/data/taxon_group service.
	 * Retrieves details of a single taxon_group.
	 */
	public function taxon_group()
	{
		$this->handle_call('taxon_group');
	}

	/**
	 * Provides the /services/data/taxon_list service.
	 * Retrieves details of a single taxon_list.
	 */
	public function taxon_list()
	{
		$this->handle_call('taxon_list');
	}

	/**
	 * Provides the /services/data/taxa_taxon_list service.
	 * Retrieves details of taxa on a taxon_list.
	 */
	public function taxa_taxon_list()
	{
		$this->handle_call('taxa_taxon_list');
	}

	/**
	 * Provides the /services/data/term service.
	 * Retrieves details of a single term.
	 */
	public function term()
	{
		$this->handle_call('term');
	}

	/**
	 * Provides the /services/data/termlist service.
	 * Retrieves details of a single termlist.
	 */
	public function termlist()
	{
		$this->handle_call('termlist');
	}

	/**
	 * Provides the /services/data/termlists_term service.
	 * Retrieves details of a single termlists_term.
	 */
	public function termlists_term()
	{
		$this->handle_call('termlists_term');
	}

	/**
	 * Provides the /services/data/user service.
	 * Retrieves details of a single user.
	 */
	public function user()
	{
		$this->handle_call('user');
	}

	/**
	 * Provides the /services/data/website service.
	 * Retrieves details of a single website.
	 */
	public function website()
	{
		$this->handle_call('website');
	}

	/**
	 * Internal method to handle calls - decides if it's a request for data or a submission.
	 */
	protected function handle_call($entity) {
		$this->entity = $entity;

		if (array_key_exists('submission', $_POST)) {
			$this->handle_submit();
		} else {
			$this->handle_request();
		}
	}

	/**
	 * Internal method for handling a generic submission to a particular model.
	 */
	protected function handle_submit() {
		$mode = $this->get_input_mode();
		switch ($mode) {
		case 'json':
			$s = json_decode($_POST['submission'], true);
		}

		if (array_key_exists('submission', $s)) {
			$this->submit($s);
		} else {
			$model = ORM::factory($this->entity);
			$model->submission = $s;
			$model->submit();
		}
	}

	/**
	 * Internal method to handle a generic request for either a single item from a model
	 * (when an argument representing the primary key is present in the URL), or a list
	 * of items (if no argunment is present in the URL).
	 */
	protected function handle_request()
	{
		// Store the entity in class member, so less recursion overhead when building XML.
		$this->viewname = $this->get_view_name();
		$this->model=ORM::factory($this->entity);
		$this->db = new Database();
		$this->view_columns = $this->db->list_fields($this->viewname);
		$mode = $this->get_output_mode();
		$records=$this->build_query_results();

		switch ($mode) {
			case 'json':
				echo json_encode($records);
				break;
			case 'xml':
				if (array_key_exists('xsl', $_GET)) {
					$xsl = $_GET['xsl'];
					if (!strpos($xsl, '/'))
						// xsl is not a fully qualified path, so point it to the media folder.
						$xsl = url::base().'media/services/stylesheets/'.$xsl;
				} else {
					$xsl = '';
				}
				echo $this->xml_encode($records, $xsl, TRUE);
				header('Content-Type: text/xml');
				break;
			case 'csv':
				echo $this->csv_encode($records);
				header('Content-Type: text/comma-separated-values');
				break;
			default:
				// Code to load from a view
				if (kohana::file_exists('views',"services/data/$entity/$mode")) {
					echo $this->view_encode($records, View::factory("services/data/$entity/$mode"));
				} else {
					$this->error("$entity data cannot be output using mode $mode.");
				}
		}
	}

	/**
	 * Encodes an array as xml. Uses $this->entity to decide the name of the root element.
	 * Recurses into the array where array values are themselves arrays. Also inserts
	 * xlink paths to any foreign keys, and gets the caption of the foreign entity.
	 */
	protected function xml_encode($array, $xsl, $indent=false, $recursion=0) {
		// Keep an array to track any elements that must be skipped. For example if an array contains
		// {person_id=>1, person=>James Brown} then the xml output for the id is <person id="1">James Brown</person>.
		// There is no need to output the person separately so it gets flagged in this array for skipping.
		$to_skip=array();

		if (!$recursion) {
			// if we are outputting a specific record, root is singular
			if (uri::total_arguments()) {
				$root = $this->entity;
				// We don't need to repeat the element for each record, as there is only 1.
				$array = $array[0];
			} else {
				$root = inflector::plural($this->entity);
			}
			$data = '<?xml version="1.0"?>';
			if ($xsl)
				$data .= '<?xml-stylesheet type="text/xsl" href="'.$xsl.'"?>';
			$data .= ($indent?"\r\n":'').
				"<$root xmlns:xlink=\"http://www.w3.org/1999/xlink\">".
				($indent?"\r\n":'');
		} else {
			$data = '';
		}

		foreach ($array as $element => $value) {
			if (!in_array($element, $to_skip)) {
				if ($value) {
					if (is_numeric($element)) {
						$element = $this->entity;
					}
					if ((substr($element, -3)=='_id') && (array_key_exists(substr($element, 0, -3), $array))) {
						$element = substr($element, 0, -3);
						// This is a foreign key described by another field, so create an xlink path
						if (array_key_exists($element, $this->model->belongs_to)) {
							// Belongs_to specifies a fk table that does not match the attribute name
							$fk_entity=$this->model->belongs_to[$element];
						} elseif ($element=='parent') {
							$fk_entity=$this->entity;
						} else {
							// Belongs_to specifies a fk table that matches the attribute name
							$fk_entity=$element;
						}
						$data .= ($indent?str_repeat("\t", $recursion):'');
						$data .= "<$element id=\"$value\" xlink:href=\"".url::base(TRUE)."services/data/$fk_entity/$value\">";
						$data .= $array[$element];
						// We output the associated caption element already, so add it to the list to skip
						$to_skip[count($to_skip)-1]=$element;
					} else {
						$data .= ($indent?str_repeat("\t", $recursion):'').'<'.$element.'>';
						if (is_array($value)) {
							$data .= ($indent?"\r\n":'').$this->xml_encode($value, NULL, $indent, ($recursion + 1)).($indent?str_repeat("\t", $recursion):'');
						} else {
							$data .= $value;
						}
					}
					$data .= '</'.$element.'>'.($indent?"\r\n":'');
				}
			}
		}
		if (!$recursion) {
			$data .= "</$root>";
		}
		return $data;
	}

	/**
	 * Builds a query to extract data from the requested entity, and also
	 * include relationships to foreign key tables and the caption fields from those tables.
	 *
	 * @todo Review this code for SQL Injection attack!
	 */
	protected function build_query_results()
	{
		$this->foreign_keys = array();
		$this->db->from($this->viewname);
		// Select all the table columns from the view
		$select = '*';
		$this->db->select($select);
		// if requesting a single item in the segment, filter for it, otherwise use GET parameters to control the list returned
		if (URI::total_arguments()==0)
			$this->apply_get_parameters_to_db();
		else
			$this->db->where($this->viewname.'.id', URI::argument(1));
		return $this->db->get()->result_array(FALSE);
	}

	/**
	 * Returns the name of the view for the request. This is a view
	 * associated with the entity, but prefixed by either list, gv or max depending
	 * on the GET view parameter.
	 */
	protected function get_view_name()
	{
		$table = inflector::plural($this->entity);
		$prefix='';
		if (array_key_exists('view', $_GET)) {
			$prefix = $_GET['view'];
		}
		// Check for allowed view prefixes, and use 'list' as the default
		if ($prefix!='gv' && $prefix!='detail')
			$prefix='list';
		return $prefix.'_'.$table;
	}


	/**
	 * Works out what filter and other options to set on the db object according to the
	 * $_GET parameters currently available, when retrieving a list of items.
	 */
	protected function apply_get_parameters_to_db()
	{
		$sortdir='ASC';
		$orderby='';
		$like=array();
		$where=array();
		foreach ($_GET as $param => $value) {
			switch ($param) {
				case 'sortdir':
					$sortdir=strtoupper($value);
					if ($sortdir != 'ASC' && $sortdir != 'DESC') {
						$sortdir='ASC';
					}
					break;
				case 'orderby':
					if (array_key_exists(strtolower($value), $this->view_columns))
						$orderby=strtolower($value);
					break;
				case 'limit':
					if (is_numeric($value))
						$this->db->limit($value);
					break;
				case 'offset':
					if (is_numeric($value))
						$this->db->offset($value);
					break;
				default:
					if (array_key_exists(strtolower($param), $this->view_columns)) {
						// A parameter has been supplied which specifies the field name of a filter field
						if ($this->view_columns[$param]=='int')
							$where[$param]=$value;
						else
							$like[$param]=$value;
					}
			}
		}
		if ($orderby)
			$this->db->orderby($orderby, $sortdir);
		if (count($like))
			$this->db->like($like);
		if (count($where))
			$this->db->where($where);
	}

	/**
	 * Encode the results of a query array as a csv string
	 */
	protected function csv_encode($array)
	{
		// Get the column titles in the first row
		$result = $this->get_csv(array_keys($array[0]));
		foreach ($array as $row) {
			$result .= $this->get_csv(array_values($row));
		}
		return $result;
	}

	/**
	 * Return a line of CSV from an array. This is instead of PHP's fputcsv because that
	 * function only writes straight to a file, whereas we need a string.
	 */
	function get_csv($data,$delimiter=',',$enclose='"') {
		$newline="\n";
		$output = '';
		foreach ($data as $cell) {
			//Test if numeric
			if (!is_numeric($cell)) {
				//Escape the enclose
				$cell = str_replace($enclose,$enclose.$enclose,$cell);
				//Not numeric enclose
				$cell = $enclose . $cell . $enclose;
			}
			if ($output=='') {
				$output = $cell;
			} else {
				$output.=  $delimiter . $cell;
			}
		}
		$output.=$newline;
		return $output;
	}

	/**
	 * Get the results of the query using the supplied view to render each row.
	 */
	protected function view_encode($array, $view) {
		$output = '';
		foreach ($array as $row) {
			$view->row= $row;
			$output .= $view->render();
		}
	}

	/**
	 * Accepts a submission from POST data and attempts to save to the database.
	 */
	public function save(){

		if (array_key_exists('submission', $_POST)){

			$mode = $this->get_input_mode();
			switch ($mode) {
			case 'json':
				$s = json_decode($_POST['submission'], true);
			}

			$this->submit($s);
		}
	}

	/**
	 * Takes a submission array and attempts to save to the database.
	 */
	protected function submit($s){
		foreach ($s['submission']['entries'] as $m) {
			$m = $m['model'];
			$model = ORM::factory($m['id']);
			$model->submission = $m;
			$model->submit();
		}
	}
}

?>
