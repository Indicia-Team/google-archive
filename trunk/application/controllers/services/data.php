<?php

class Data_Controller extends Controller {

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
	 * Provides the /services/data/termlist service.
	 * Retrieves details of a single termlist.
	 */
	public function termlist()
	{
		if (URI::total_arguments() != 1)
			$this->error('Only one argument allowed when requesting a termlist.');
		else {
			$listid=URI::argument(1);
			$mode = $this->get_output_mode();
			$model=ORM::factory('termlist')->where('id', $listid)->find();
			if ($model->id) {
				switch ($mode) {
					case 'xml':
						$view = new View('services/data/termlist_xml');
						$view->model = $model;
						$view->render(true);
						break;
					case 'json':
						echo json_encode(array('result' => $model->as_array()));
						break;
					 	//$json = json_encode(array('result' => $iterator->as_array()));
				}
			} else {
				$this->warning('Termlist '.$listid.' not found.');
			}

		}
	}

	/**
	 * Provides the /services/data/termlist_terms service.
	 * Retrieves details of a list of terms from a termlist termlist.
	 */
	public function termlist_terms()
	{
		if (URI::total_arguments() != 1)
			$this->error('Only one argument allowed when requesting terms from a termlist.');
		else {
			$mode = $this->get_output_mode();
			$listid=URI::argument(1);
			$db = new Database();
			$db->from(array('termlists_terms'));
			$db->join('terms', 'terms.id = termlists_terms.term_id');
			if ($mode='xml') {
				$db->select('terms.term, termlists_terms.id, languages.iso, termlists_terms.sort_order');
				$db->join('languages', 'languages.id = terms.language_id');
			}
			else
				$db->select('terms.term, termlists_terms.id');
			if (array_key_exists('filter', $_GET))
				$db->like('terms.term',$_GET['filter']);
			/*if (array_key_exists('filter_field', $_GET))
			if (array_key_exists('orderby', $_GET))
			if (array_key_exists('limit', $_GET))
			if (array_key_exists('offset', $_GET))*/

			$view = new View('services/data/termlist_terms_xml');
			$view->terms=$db->get()->as_array();
			$view->render(true);
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
}

?>
