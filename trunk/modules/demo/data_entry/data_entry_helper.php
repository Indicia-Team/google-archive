<?php

class data_entry_helper {

	public static function forward_post_to($url, $entity) {
		$array = self::wrap($_POST, $entity);
		$request = "$url/$entity";
		$postargs = 'submission='.json_encode($array);
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
			 // This should be a field in the model.
	                 // Add a new field to the save array
			 $sa['fields'][$a] = array(
				 // Set the value
				 'value' => $b);
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
							$response['error'].'</p>';
			}
    	} else {
    		print_r($response);
    	}


    }



}
?>
