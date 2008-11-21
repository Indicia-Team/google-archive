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
		switch (URI::total_arguments()) {}
			case 0:
				handle_list_request();
				break;
			case 1:
				// primary key of an item passed
				handle_item_request();
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
		// TODO: handle requests for lists
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
		if (!$recursion) {
			$data = '<?xml version="1.0"?>'.($indent?"\r\n":'').
				"<$this->entity xmlns:xlink=\"http://www.w3.org/1999/xlink\">".
				($indent?"\r\n":'');
		} else {
			$data = '';
		}

		foreach ($array as $element => $value) {
			if ($value) {
				if (is_numeric($element)) {
					$element = 'item';
				}
				if (substr($element, -3)=='_id') {
					// This is a foreign key to another entity, so include the xlink URL and remove _id from the name
					$element = substr($element, 0, -3);
					if (array_key_exists($element, $this->model->belongs_to)) {
						// Belongs_to specifies a fk table that does not match the attribute name
						$fk_entity=$this->model->belongs_to[$element];
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
			$data .= "</$this->entity>";
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
