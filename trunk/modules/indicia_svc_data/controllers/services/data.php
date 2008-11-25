<?php

class Data_Controller extends Controller {
	protected $model;
	protected $entity;
	protected $foreign_keys;

	/**
	 * Provides the /services/data/language service.
	 * Retrieves details of a single language.
	 */
	public function language()
	{
		$this->handle_request('language');
	}

	/**
	 * Provides the /services/data/location service.
	 * Retrieves details of a single survey.
	 */
	public function location()
	{
		$this->handle_request('location');
	}

	/**
	 * Provides the /services/data/person service.
	 * Retrieves details of a single person.
	 */
	public function person()
	{
		$this->handle_request('person');
	}

	/**
	 * Provides the /services/data/survey service.
	 * Retrieves details of a single survey.
	 */
	public function survey()
	{
		$this->handle_request('survey');
	}

	/**
	 * Provides the /services/data/taxon_group service.
	 * Retrieves details of a single taxon_group.
	 */
	public function taxon_group()
	{
		$this->handle_request('taxon_group');
	}

	/**
	 * Provides the /services/data/taxon_list service.
	 * Retrieves details of a single taxon_list.
	 */
	public function taxon_list()
	{
		$this->handle_request('taxon_list');
	}

	/**
	 * Provides the /services/data/term service.
	 * Retrieves details of a single term.
	 */
	public function term()
	{
		$this->handle_request('term');
	}

	/**
	 * Provides the /services/data/termlist service.
	 * Retrieves details of a single termlist.
	 */
	public function termlist()
	{
		$this->handle_request('termlist');
	}

	/**
	 * Provides the /services/data/termlists_term service.
	 * Retrieves details of a single termlists_term.
	 */
	public function termlists_term()
	{
		$this->handle_request('termlists_term');
	}

	/**
	 * Provides the /services/data/user service.
	 * Retrieves details of a single user.
	 */
	public function user()
	{
		$this->handle_request('user');
	}

	/**
	 * Provides the /services/data/website service.
	 * Retrieves details of a single website.
	 */
	public function website()
	{
		$this->handle_request('website');
	}

	/**
	 * Internal method to handle a generic request for either a single item from a model
	 * (when an argument representing the primary key is present in the URL), or a list
	 * of items (if no argunment is present in the URL).
	 */
	protected function handle_request($entity)
	{
		// Store the entity in class member, so less recursion overhead when building XML.
		$this->entity = $entity;
		$this->model=ORM::factory($this->entity);
		$mode = $this->get_output_mode();
		$records=$this->build_query_results();

		switch ($mode) {
			case 'json':
				echo json_encode(array('result' => $records));
				break;
			case 'xml':
				echo $this->xml_encode($records, TRUE);
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
	 * Builds a query to extract data from the requested entity, and also
	 * include relationships to foreign key tables and the caption fields from those tables.
	 */
	protected function build_query_results()
	{
		// TODO: Review this code for SQL Injection attack!
		$this->foreign_keys = array();
		$db = new Database();
		$tablename = inflector::plural($this->entity);
		$db->from($tablename);
		$select = $tablename.'.'.implode(", $tablename.", array_keys($this->model->table_columns));
		// Iterate each foreign key in the model, and add a join and the associated caption fields to the query.
		foreach ($this->model->belongs_to as $fk=>$fk_entity) {
			$fk_table = inflector::plural($fk_entity);
			// if the foreign key itself is not specified in the belongs_to array, it must be a foreign key with the
			// same name as the related table
			if (is_numeric($fk)) {
				$fk=$fk_entity;
				$fk_alias=$fk_table;
			} else {
				$fk_alias=$fk;
			}
			// TODO: Is a LEFT JOIN going to cause a performance issue? Can we use INNER when key is mandatory?
			// Add a join: LEFT JOIN fk_table (AS fk name - if specified which means it is ) ON fk_table.id = this_table.fk_name
			$fk_table = $fk_table.($fk_table==$fk_alias? "": " AS $fk_alias");
			$db->join($fk_table, "$fk_alias.id=$tablename.".$fk."_id", NULL, "LEFT");
			// Add a field to the SELECT data
			$fk_field = $fk_alias.'.'.ORM::factory($fk_entity)->get_search_field();
			$select .= ", $fk_field AS $fk";
			$this->foreign_keys[$fk]=$fk_field;
		}
		$db->select($select);
		// if requesting a single item in the segment, filter for it, otherwise use GET parameters to control the list returned
		if (URI::total_arguments()==0)
			$this->apply_get_parameters_to_db($db, $select);
		else
			$db->where($tablename.'.id', URI::argument(1));
		return $db->get()->result_array(FALSE);
	}


	/**
	 * Works out what filter and other options to set on the db object according to the
	 * $_GET parameters currently available, when retrieving a list of items.
	 */
	protected function apply_get_parameters_to_db($db)
	{
		if (array_key_exists('filter_field', $_GET))
			$filterfield = $_GET['filter_field'];
		else
			$filterfield = $this->model->get_search_field();

		if (array_key_exists('filter', $_GET)) {
			if (array_key_exists($filterfield, $this->model->table_columns)) {
				if ($this->model->table_columns[$filterfield]=='int') {
					$db->where(inflector::plural($this->entity).'.'.$filterfield, $_GET['filter']);
				} else {
        			$db->like(inflector::plural($this->entity).'.'.$filterfield, $_GET['filter']);
				}
			} elseif (array_key_exists($filterfield, $this->foreign_keys)) {
				// filter is against a foreign key field and must be a string
				$db->like($this->foreign_keys[$filterfield], $_GET['filter']);
			} else {
				// Can't find filter field either in the entitiy's attributes or a foreign key field
				$this->error("Invalid filter field $filterfield specified for $this->entity data.");
			}
		}

		$sortdir='';
		if (array_key_exists('dir', $_GET)) {
			$sortdir=strtoupper($_GET['dir']);
		}
		if ($sortdir != 'ASC' && $sortdir != 'DESC') {
			$sortdir='ASC';
		}
		if (array_key_exists('orderby', $_GET)) {
			$db->orderby($_GET['orderby'], $sortdir);
		}

		if (array_key_exists('limit', $_GET)) {
			$db->limit($_GET['limit']);
		}

		if (array_key_exists('offset', $_GET)) {
			$db->offset($_GET['offset']);
		}
	}

	/**
	 * Return an error XML document to the client
	 */
	protected function error($message)
	{
		$view = new View('services/data/error');
		$view->message = $message;
		$view->render(true);
	}

	/**
	 * Return an warning XML document to the client
	 */
	protected function warning($message)
	{
		$view = new View('services/data/warning');
		$view->message = $message;
		$view->render(true);
	}

	/**
	 * Retrieve the output mode for a RESTful request from the GET or POST data.
	 * Defaults to xml. Other options are json and csv, or a view loaded from the views folder.
	 */
	protected function get_output_mode() {
		if (array_key_exists('mode', $_GET))
			$result = $_GET['mode'];
		elseif (array_key_exists('mode', $_POST))
			$result = $_POST['mode'];
		else
			$result='xml';
		return $result;
	}

	/**
	 * Encodes an array as xml. Uses $this->entity to decide the name of the root element.
	 * Recurses into the array where array values are themselves arrays. Also inserts
	 * xlink paths to any foreign keys, and gets the caption of the foreign entity.
	 */
	protected function xml_encode($array, $indent=false, $recursion=0) {
		// if we are outputting a specific record, root is singular
		if ($this->model->id)
			$root = $this->entity;
		else
			$root = inflector::plural($this->entity);
		if (!$recursion) {
			$data = '<?xml version="1.0"?>'.($indent?"\r\n":'').
				"<$root xmlns:xlink=\"http://www.w3.org/1999/xlink\">".
				($indent?"\r\n":'');
		} else {
			$data = '';
		}

		foreach ($array as $element => $value) {
			if ($value) {
				if (is_numeric($element)) {
					$element = $this->entity;
				}
				if (substr($element, -3)=='_id') {
					// This is a foreign key to another entity, so include the xlink URL and remove _id from the name
					$element = substr($element, 0, -3);
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
					// If the input query includes the join to the related table, then use the input query to add the caption of
					// the related item. Otherwise fetch via ORM which will be slower.
					if (array_key_exists($element, $array))
						$data .= $array[$element];
					else
						$data .= ORM::factory($fk_entity, $value)->caption();
				} else {
					$data .= ($indent?str_repeat("\t", $recursion):'').'<'.$element.'>';
					if (is_array($value)) {
						$data .= ($indent?"\r\n":'').$this->xml_encode($value, $indent, ($recursion + 1)).($indent?str_repeat("\t", $recursion):'');
					} else {
						$data .= $value;
					}
				}
				$data .= '</'.$element.'>'.($indent?"\r\n":'');
			}
		}
		if (!$recursion) {
			$data .= "</$root>";
		}
		return $data;
	}

	/**
	 * Encode the results of a query as a csv string
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

}

?>
