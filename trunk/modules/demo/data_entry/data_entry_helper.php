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

}
?>
