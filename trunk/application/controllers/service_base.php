<?php

class Service_Base_Controller extends Controller {


	/**
	 * Return an error XML or json document to the client
	 */
	protected function error($message)
	{
		$this->problem($message, 'error');
	}

	/**
	 * Return an warning XML or json document to the client
	 */
	protected function warning($message)
	{
		$this->problem($message, 'warning');
	}

	/**
	 * Return an error or warning XML or json document to the client
	 */
	private function problem($message, $type)
	{
		$mode = $this->get_input_mode();
		if ($mode=='xml') {
			$view = new View("services/$type");
			$view->message = $message;
			$view->render(true);
		} else {
			echo json_encode(array($type=>$message));
		}
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
	 * Retrieve the input mode for a RESTful request from the POST data.
	 * Defaults to json. Other options not yet implemented.
	 */
	protected function get_input_mode() {
		if (array_key_exists('mode', $_POST)){
			$result = $_POST['mode'];
		} else {
			$result = 'json';
		}
		return $result;
	}

}

?>
