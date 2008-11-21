<?php

class Data_Controller extends Controller {
	protected $model;
	protected $entity;

		// validate segments and query string
		// First argument is list ID.
		// Is it a valid list id that the user has access to?
		/* Query string options:
		 * filter = filter text
		 * filter_field = field name (defaults to lookup field)
		 * offset = record offset
		 * limit = record count
		 * type = li|JSON
		 * orderby = order field
		 */

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
		switch (URI::total_arguments()) {
			case 0:
				$this->handle_list_request();
				break;
			case 1:
				// primary key of an item passed
				$this->handle_item_request();
				break;
			default:
				$this->error("Only zero or one arguments (the ID) are allowed when requesting $this->entity data.");
		}
	}

	/**
	 * Internal method to handle a generic request for a representation of a single object.
	 * The $this->entity should match the model name.
	 * An optional $_POST parameter called mode describes the output format mode. Defaults
	 * to xml. Json and XML are supported for all objects by default, otherwise there must
	 * be a view template in views/services/data/$this->entity/format.
	 */
	protected function handle_item_request()
	{
		$id=URI::argument(1);
		$mode = $this->get_output_mode();
		$this->model=ORM::factory($this->entity, $id);
		if ($this->model->id) {
			switch ($mode) {
				case 'json':
					$array = $this->insert_fk_values($this->model->as_array());
					echo json_encode(array('result' => $array));
					break;
				case 'xml':
					echo $this->xml_encode($this->model->as_array(), TRUE);
					break;
				default:
					if (Kohana::find_file('views',"services/data/$this->entity/$mode")) {
						$view = new View("services/data/$this->entity/$mode");
						$view->model = $model;
						$view->render(true);
					} else {
						$this->error("Type $mode not available for $this->entity data.");
					}
					break;
			}
		} else {
			$this->warning(ucwords($this->entity)." $id not found.");
		}
	}

	/**
	 * Provides the /services/data/termlist_terms service.
	 * Retrieves details of a list of terms from a termlist termlist.
	 */
	public function handle_list_request()
	{
		// TODO: Review this code for SQL Injection attack!
		$mode = $this->get_output_mode();
		$db = new Database();
		$db->from(inflector::plural($this->entity));
		$this->model=ORM::factory($this->entity);
		$this->apply_get_parameters_to_db($db);
		$records=$db->get()->result_array(FALSE);

		// TODO: need to disable the automatic fk handling in the xml encode and build joins into the query,
		// otherwise it will be too slow.
		switch ($mode) {
			case 'json':
				echo json_encode(array('result' => $records));
				break;
			case 'xml':
				echo $this->xml_encode($records, TRUE);
				break;
			//default:
		}
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
			if ($this->model->table_columns[$filterfield]=='int') {
				$db->where($filterfield, $_GET['filter']);
			} else {
        		$db->like($filterfield, $_GET['filter']);
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
	 * Defaults to xml. Other options are json and li.
	 */
	protected function get_output_mode() {
		if (array_key_exists('mode', $_GET))
			$result = $_GET['mode'];
		elseif (array_key_exists('mode', $_POST))
			$result = $_POST['mode'];
		else
			$result='xml';
		if (!($result == 'xml' || $result == 'json' || $result == 'li' ))
			$result = 'xml';
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
	 * Takes a model's array, and inserts new array elements to provide the captions for each entity
	 * pointed to by a foreign key.
	 */
	 protected function insert_fk_values($array) {
	 	$inserts = array();
	 	foreach ($array as $item => $value) {
			if ($value) {
		 		if (substr($item, -3)=='_id') {
					// This is a foreign key to another entity, so insert a new element for the fk item caption
					$element = substr($item, 0, -3);
					// dynamically call the related model to get it's caption
					$inserts[$element] = $this->model->{$element}->caption();
		 		}
	 		}
	 	}
	 	return array_merge($array, $inserts);
	 }
}

?>
