<?php

class Service_Base_Controller extends Controller {


	/**
	 * Return an error XML document to the client
	 */
	protected function error($message)
	{
		$view = new View('services/error');
		$view->message = $message;
		$view->render(true);
	}

	/**
	 * Return an warning XML document to the client
	 */
	protected function warning($message)
	{
		$view = new View('services/warning');
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

}

?>
