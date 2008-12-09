<?php

class data_entry_helper {

	public static function forward_post_to($url, $entity, $array = null) {
		if ($array == null) $array = self::wrap($_POST, $entity);
		$request = "$url/$entity";
		$postargs = 'submission='.json_encode($array);
		// passthrough the authentication tokens as POST data
		if (array_key_exists('auth_token', $_POST))
			$postargs .= '&auth_token='.$_POST['auth_token'];
		if (array_key_exists('nonce', $_POST))
			$postargs .= '&nonce='.$_POST['nonce'];
		// Get the curl session object
		$session = curl_init($request);
		// Set the POST options.
		curl_setopt ($session, CURLOPT_POST, true);
		curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		// Do the POST and then close the session
		$response = curl_exec($session);
		list($response_headers,$response_body) = explode("\r\n\r\n",$response,2);
		curl_close($session);
		return json_decode($response_body, TRUE);
	}

	public static function wrap( $array, $entity, $fkLink = false) {
        // Initialise the wrapped array
        $sa = array(
            'id' => $entity,
            'fields' => array(),
            'fkFields' => array(),
            'subModels' => array()
        );

        // Iterate through the array
        foreach ($array as $a => $b) {
            // Check whether this is a fk placeholder
            if (substr($a,0,3) == 'fk_'
                && $fkLink) {
                    // Generate a foreign key instance
                    $sa['fkFields'][$a] = array(
                        // Foreign key id field is table_id
                        'fkIdField' => substr($a,3)."_id",
                        'fkTable' => substr($a,3),
                        'fkSearchField' =>
                        ORM::factory(substr($a,3))->get_search_field(),
                        'fkSearchValue' => $b);
			    // Determine the foreign table name
			    $m = ORM::factory($id);
			    if (array_key_exists(substr($a,3), $m->belongs_to)) {
				    $sa['fkFields'][$a]['fkTable'] = $m->belongs_to[substr($a,3)];
			    } else if (array_key_exists(substr($a,3), $m->parent)) {
				    $sa['fkFields'][$a]['fkTable'] = $id;
			    }
			} else {
				// Don't wrap the authentication tokens
				if ($a!='auth_token' && $a!='nonce') {
					// This should be a field in the model.
					// Add a new field to the save array
					$sa['fields'][$a] = array(
						// Set the value
						'value' => $b);
				}

			}
		}
        return $sa;
    }

    /**
     * Takes a response, and outputs any errors from it onto the screen.
     *
     * @todo method of placing the errors alongside the controls.
     */
    public static function dump_errors($response)
    {
    	if (is_array($response)) {
	    	if (array_key_exists('error',$response)) {
				echo 'An error occurred when the data was submitted.';
				if (is_array($response['error'])) {
					echo '<ul>';
					foreach ($response['error'] as $field=>$message)
						echo "<li>$field: $message</li>";
					echo '</ul>';
				} else {
					echo '<p class="error">'.$response['error'].'</p>';
				}
			} elseif (array_key_exists('warning',$response)) {
				echo 'A warning occurred when the data was submitted.';
				echo '<p class="error">'.$response['error'].'</p>';
			} elseif (array_key_exists('success',$response)) {
				echo 'Data was successfully inserted. The record\'s ID is'.
							$response['success'].'</p>';
			}
			if (array_key_exists('trace', $response)) {
				print_r($response['trace']);
			}
    	} else {
    		print_r($response);
    	}


    }

    /**
     * Helper function to generate a drop-down list box from a Indicia core service query.
     */
	public static function select($id, $url, $entity, $nameField, $valueField = null, $extraParams = null) {
		// If valueField is null, set it to $nameField
	   	if ($valueField == null) $valueField = $nameField;
		// Execute a request to the service
	    	$request = "$url/$entity?mode=json";
		foreach ($extraParams as $a => $b){
			$request .= "&$a=$b";
		}
	    	// Get the curl session object
	    	$session = curl_init($request);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = json_decode(curl_exec($session), true);
		$r = "";
		if (!array_key_exists('error', $response)){
			$r .= "<select id='$id' >";
			foreach ($response as $item){
				if (array_key_exists($nameField, $item) &&
					array_key_exists($valueField, $item)) {
						$r .= "<option value='$item[$valueField]' >";
						$r .= $item[$nameField];
						$r .= "</option>";
				}
			}
		$r .= "</select>";
		}

		return $r;
    }
    /**
     * Helper function to generate an autocomplete box from an Indicia core service query.
     */
    public static function autocomplete($id, $url, $entity, $nameField, $valueField = null, $extraParams = null) {
	    // If valueField is null, set it to $nameField
	    if ($valueField == null) $valueField = $nameField;
	    // Do stuff with extraParams
	    $sParams = '';
	    foreach ($extraParams as $a => $b){
		    $sParams .= "$a : '$b',";
	    }

		// Reference the necessary libraries
		$r = "<script type='text/javascript' >
			$(document).ready(function() {
				$('input#ac$id').autocomplete('$url/$entity', {
					minChars : 1,
					mustMatch : true,
					extraParams : {
						orderby : '$nameField',
						mode : 'json',
						qfield : '$nameField',
						$sParams
					},
					parse: function(data) {
						var results = [];
						var obj = JSON.parse(data);
						$.each(obj, function(i, item) {
							results[results.length] = {
								'data' : item,
								'result' : item.$nameField,
								'value' : item.$valueField };
						});
    					return results;
					},
					formatItem: function(item) {
						return item.$nameField;
					},
					formatResult: function(item) {
						return item.$valueField;
					}
				});
				$('input#ac$id').result(function(event, data){
					$('input#$id').attr('value', data.id);
				});
			});
			</script>";
			$r .= "<input type='hidden' id='$id' name='$id' />".
				"<input id='ac$id' name='ac$id' value='' />";
			return $r;
    }

    /**
     * Retrieves a token and inserts it into a data entry form which authenticates that the
     * form was submitted by this website.
     */
    public static function get_auth($website_id, $password) {
		$postargs = "website_id=$website_id";
		// Get the curl session object
		$session = curl_init('http://localhost/indicia/index.php/services/security/get_nonce');
		// Set the POST options.
		curl_setopt ($session, CURLOPT_POST, true);
		curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		// Do the POST and then close the session
		$response = curl_exec($session);
		list($response_headers,$nonce) = explode("\r\n\r\n",$response,2);
		curl_close($session);
    	$result = '<input id="auth_token" name="auth_token" type="hidden"' .
    			'value="'.sha1("$nonce:$password").'">';
    	$result .= '<input id="nonce" name="nonce" type="hidden"' .
    			'value="'.$nonce.'">';
    	return $result;
    }
	/**
	 * Helper function to generate a radio group from a Indicia core service query.
	 */
	public static function radio_group($id, $url, $entity, $nameField, $valueField = null, $extraParams = null) {
		// If valueField is null, set it to $nameField
	   	if ($valueField == null) $valueField = $nameField;
		// Execute a request to the service
	    	$request = "$url/$entity?mode=json";
		foreach ($extraParams as $a => $b){
			$request .= "&$a=$b";
		}
	    	// Get the curl session object
	    	$session = curl_init($request);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = json_decode(curl_exec($session), true);
		$r = "";
		if (!array_key_exists('error', $response)){
			foreach ($response as $item){
				if (array_key_exists($nameField, $item) &&
					array_key_exists($valueField, $item)) {
						$r .= "<input type='radio' name='$id' value='$item[$valueField]' >";
						$r .= $item[$nameField];
						$r .= "</input>";
				}
			}
		}

		return $r;
    }
}
?>
