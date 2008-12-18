<?php

class Validation_Controller extends Service_Base_Controller {

	/**
	  * Service call method. This will parse the POST data for a submission type
	  * format encapsulating some data to be validated and the rules to validate
	  * it against.
	  */
	public function check()
	{
		if (!array_key_exists('submission', $_POST)) $retVal = 'No array!';
		$mode = $this->get_input_mode();
		switch ($mode) {
			case 'json':
			$s = json_decode($_POST['submission'], true);
			break;
		}
		if (array_key_exists('fields', $s)) {
			$fields = array();
			$rules = array();
			foreach ($s['fields'] as $name => $arr) {
				// We build an array, convert it to a validation object
				// and add all of the rules. Then we validate it.
				if (array_key_exists('value', $arr)){
					$fields[$name] = $arr['value'];
					if (array_key_exists('rules', $arr)){
						foreach ($arr['rules'] as $r){
							$rules['name'][] = $r['rule'];
						}
					}
				}
			}
			$val = Validation::factory($fields);
			foreach ($rules as $name => $arr){
				foreach ($arr as $rule) {
					$val->add_rules($name, $rule);
				}
			}
			if ($val->validate()){
				$retVal = 'success';
			} else {
				$errRules = $val->errors();
				$errMessages = $val->errors('form_error_messages');
				foreach ($errRules as $name => $rule){
					$msg = $errMessages[$name];
					$s['fields'][$name][$rule]['result'] = $msg;
				}
				$retVal = $s;
			}
		}
		$output_mode = $this->get_output_mode();
		switch ($output_mode) {
			case 'json':
				return json_encode($retval);
				break;
		}
	}
}
