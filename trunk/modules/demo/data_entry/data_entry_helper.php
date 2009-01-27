<?php

include('helper_config.php');

class data_entry_helper extends helper_config {

	/**
	 * Helper function to collect javascript code in a single location.
	 */
	public static function dump_javascript() {
		global $javascript;
		$script = "<script type='text/javascript'>
			jQuery(document).ready(function() {
				$javascript
	});
			</script>";
		return $script;
	}


	/**
	 * Helper function to support image upload
	 */
	public static function image_upload($id){
		$r = "<label for='$id'>Image upload</label>";
		$r .= "<input type='file' id='$id' name='$id' accept='png|jpg|gif'/>";

		return $r;
	}

	/**
	 * Helper function to generate a species checklist from a given taxon list.
	 *
	 * <p>This function will generate a flexible grid control with one row for each species
	 * in the specified list. For each row, the control will display the list preferred term
	 * for that species, a checkbox to indicate its presence, and a series of cells for a set
	 * of occurrence attributes passed to the control.</p>
	 *
	 * <p>Further, the control will incorporate the functionality to add extra terms to the
	 * control from the parent list of the one given. This will take the form of an autocomplete
	 * box against the parent list which will add an extra row to the control upon selection.</p>
	 *
	 * @param int list_id Database id of the taxon list to lookup against.
	 * @param int[] occ_attrs Integer array, where each entry corresponds to the id of the
	 * desired attribute in the occurrence_attributes table.
	 * @param string[] readAuth The read authorisation key/value pair, needed for making
	 * queries to the data services.
	 * @param string[] extraParams Array of key=>value pairs which will be passed to the service
	 * as GET parameters.
	 */
	public static function species_checklist($list_id, $occ_attrs, $readAuth, $extraParams = array(), $lookupList = null){
		$occAttrControls = array();
		$occAttrs = array();
		// Reference to the config file.
		global $javascript;
		// Declare the data service
		$url = parent::$base_url."/index.php/services/data";
		$termRequest = "$url/taxa_taxon_list?mode=json&taxon_list_id=$list_id&preferred=t";
		$termRequest .= "&$readAuth";
		foreach ($extraParams as $a => $b){
			$request .= "&$a=$b";
		}
		// Get the curl session object
		$session = curl_init($termRequest);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$taxalist = curl_exec($session);
		$taxalist = json_decode(array_pop(explode("\r\n\r\n",$taxalist)), true);

		// Get the list of occurrence attributes
		foreach ($occ_attrs as $occAttr) {
			$occAttrRequest = "$url/occurrence_attribute/$occAttr?mode=json";
			$occAttrRequest .= "&$readAuth";
			$session = curl_init($occAttrRequest);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			$a = json_decode(array_pop(explode("\n\n", curl_exec($session))), true);
			if (! array_key_exists('error', $a)){
				$b = $a[0];
				$occAttrs[$occAttr] = $b['caption'];
				// Build the correct control
				switch ($b['data_type']) {
				case 'L':
					$tlId = $b['termlist_id'];
					$occAttrControls[$occAttr] =
						data_entry_helper::select(
							'oa:'.$occAttr, 'termlists_term', 'term', 'id',
							$readAuth + array('termlist_id' => $tlId));
					break;
				case 'D' || 'V':
					// Date-picker control
					$occAttrControls[$occAttr] =
						"<input type='text' class='date' id='oa:$occAttr' name='oa:$occAttr' value='click here'/>";
					break;

				default:
					$occAttrControls[$occAttr] =
						"<input type='text' id='oa:$occAttr' name='oa:$occAttr'/>";
					break;
				}
			}

		}

		// Build the grid
		if (! array_key_exists('error', $taxalist)){
			$grid = "<table class='invisible'><tbody><tr id='scClonableRow'><td class='scTaxonCell'></td>".
				"<td class='scPresenceCell'>
				<input type='checkbox' name=''
				value='' /></td>";
			foreach ($occAttrControls as $oc){
				$grid .= "<td class='scOccAttrCell'>$oc</td>";
			}
			$grid .= "</tr></tbody></table>";

			$grid .= "<table class='speciesCheckList'>";
			$grid .= "<thead><th>Species</th><th>Present (Y/N)</th>";
			foreach ($occAttrs as $a) {
				$grid .= "<th>$a</th>";
			}
			$grid .= "</thead><tbody>";
			foreach ($taxalist as $taxon) {
				$id = $taxon['id'];
				$grid .= "<tr>";
				$grid .= "<td class='scTaxonCell'>".$taxon['taxon'].", ".$taxon['authority']."</td>";
				$grid .= "<td class='scPresenceCell'><input type='checkbox' name='sc:$id:present'
					value='sc:$id:present' /></td>";
				foreach ($occAttrControls as $oc){
					$oc = preg_replace('/oa:(\d+)/', "sc:$id:occAttr:$1", $oc);
					$grid .= "<td class='scOccAttrCell'>".$oc."</td>";
				}
				$grid .= "</tr>";
			}
			$grid .= "</tbody></table>";

			// Insert an autocomplete box if the termlist has a parent or an alternate
			// termlist has been given in the parameter.
			if ($lookupList == null) {
				$tlRequest = "$url/taxon_list/$list_id?mode=json&view=detail";
				$session = curl_init($tlRequest);
				curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
				$tl = json_decode(array_pop(explode("\r\n\r\n",curl_exec($session))), true);
				if (! array_key_exists('error', $tl)){
					$lookupList = $tl[0]['parent_id'];
				}
			}

			if ($lookupList != null) {
				// Javascript to add further rows to the grid
				$grid .= "<script type='text/javascript'
					src='./addRowToGrid.js' ></script>";
				$javascript .= "var addRowFn = addRowToGrid('$url', $readAuth);
				jQuery('#addRowButton').click(addRowFn);";

				// Drop an autocomplete box against the parent termlist
				$grid .= data_entry_helper::autocomplete('addSpeciesBox',
					'taxa_taxon_list', 'taxon', 'id', $readAuth +
					array('preferred' => 't',
						'taxon_list_id' => $lookupList));
				$grid .= "<button type='button' id='addRowButton'>
					Add Row</button>";

			}

			return $grid;
		}
	}

	/**
	 * Helper function to insert a date picker control.
	 */
	public static function date_picker($id) {
		global $javascript;
		$javascript .=
			'jQuery(\'.'.$id.'\').datepicker({dateFormat : \'yy-mm-dd\', constrainInput: false}); ';
		$r =
			'<input type="text" size="30" value="click here" class="date" id="'.$id.'" name="'.$id.'"/>' .
			'<style type="text/css">.embed + img { position: relative; left: -21px; top: -1px; }</style> ';
		return $r;
	}


	/**
	 * Helper function to generate a select control from a Indicia core service query.
	 *
	 * @param int $id id attribute for the returned control.
	 * @param string $entity Name (Kohana-style) of the database entity to be queried.
	 * @param string $nameField Field to draw values to show in the control from.
	 * @param string $valueField Field to draw values to return from the control from. Defaults
	 * to the value of $nameField.
	 * @param map<string, string> $extraParams Associative array of items to pass via the query
	 * string to the service.
	 *
	 * @return string HTML code for a select control.
	 */
	public static function select($id, $entity, $nameField, $valueField = null, $extraParams = null) {
		$url = parent::$base_url."/index.php/services/data";
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
		$response = curl_exec($session);
		$response = json_decode(array_pop(explode("\r\n\r\n",$response)), true);
		$r = "";
		if (!array_key_exists('error', $response)){
			$r .= "<select name='$id' id='$id' >";
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
		else
			echo "Error loading control";

		return $r;
	}
	/**
	 * Helper function to generate a list box from a Indicia core service query.
	 */
	public static function listbox($id, $entity, $nameField, $size = 3, $multival = false, $valueField = null, $extraParams = null) {
		$url = parent::$base_url."/index.php/services/data";
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
		$response = curl_exec($session);
		$response = json_decode(array_pop(explode("\r\n\r\n",$response)), true);
		$r = "";
		if (!array_key_exists('error', $response)){
			$r .= "<select id='$id' name='$id' multiple='$multival' size='$size'>";
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
		else
			echo "Error loading control";

		return $r;
	}


	/**
	 * Helper function to generate an autocomplete box from an Indicia core service query.
	 */
	public static function autocomplete($id, $entity, $nameField, $valueField = null, $extraParams = null) {
		global $javascript;
		$url = parent::$base_url."/index.php/services/data";
		// If valueField is null, set it to $nameField
		if ($valueField == null) $valueField = $nameField;
		// Do stuff with extraParams
		$sParams = '';
		foreach ($extraParams as $a => $b){
			$sParams .= "$a : '$b',";
		}
		// lop the comma off the end
		$sParams = substr($sParams, 0, -1);

		// Reference the necessary libraries
		$javascript .= "jQuery('input#ac$id').autocomplete('$url/$entity', {
			minChars : 1,
				mustMatch : true,
				extraParams : {
					orderby : '$nameField',
						mode : 'json',
						qfield : '$nameField',
						$sParams
	},
	dataType: 'jsonp',
	parse: function(data) {
		var results = [];
		$.each(data, function(i, item) {
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
	jQuery('input#ac$id').result(function(event, data){
		jQuery('input#$id').attr('value', data.id);
	});";
	$r = "<input type='hidden' id='$id' name='$id' />".
		"<input id='ac$id' name='ac$id' value='' />";
	return $r;
	}

	/**
	 * Helper function to generate a radio group from a Indicia core service query.
	 */
	public static function radio_group($id, $entity, $nameField, $valueField = null, $extraParams = null) {
		$url = parent::$base_url."/index.php/services/data";
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
						$r .= "<input type='radio' id='$id' name='$id' value='$item[$valueField]' >";
						$r .= $item[$nameField];
						$r .= "</input>";
					}
			}
		}

		return $r;
	}

	public static function forward_post_to($entity, $array = null) {
		if ($array == null) $array = self::wrap($_POST, $entity);
		$request = parent::$base_url."/index.php/services/data/$entity";
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
		curl_close($session);
		// The last block of text in the response is the body
		return json_decode(array_pop(explode("\r\n\r\n",$response)), true);
	}

	public static function handle_media($media_id = 'imgUpload') {
		syslog(LOG_DEBUG, print_r($_FILES, true));
		if (array_key_exists($media_id, $_FILES)) {
			syslog(LOG_DEBUG, "SITE: Media id $media_id to upload.");
			$uploadpath = parent::$upload_path;
			$target_url = parent::$base_url."/index.php/services/data/handle_media";

			$name = $_FILES[$media_id]['name'];
			$fname = $_FILES[$media_id]['tmp_name'];
			$fext = array_pop(explode(".", $name));
			$bname = basename($fname, ".$fext");

			// Generate a file id to store the image as
			$destination = time().rand(0,1000).".".$fext;

			if (move_uploaded_file($fname, $uploadpath.$destination)) {
				$postargs = array();
				if (array_key_exists('auth_token', $_POST))
					$postargs['auth_token'] = $_POST['auth_token'];
				if (array_key_exists('nonce', $_POST))
					$postargs['nonce'] = $_POST['nonce'];
				$file_to_upload = array('media_upload'=>'@'.$uploadpath.
					$destination);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,$target_url);
				curl_setopt($ch, CURLOPT_POST,1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $file_to_upload + $postargs);
				$result=curl_exec ($ch);
				curl_close ($ch);
				return $destination;

			} else {
				//TODO error messaging
				return false;
			}
		}



	}

	/**
	 * Wraps data from a species checklist grid (generated by
	 * data_entry_helper::species_checklist) into a suitable format for submission. This will
	 * return an array of submodel entries which can be dropped directly into the subModel
	 * section of the submission array.
	 */
	public static function wrap_species_checklist($arr){
		if (array_key_exists('website_id', $arr)){
			$website_id = $arr['website_id'];
		} else {
			throw new Exception('Cannot find website id in POST array!');
		}
		if (array_key_exists('determiner_id', $arr)){
			$determiner_id = $arr['determiner_id'];
		} else {
			throw new Exception('Cannot find determiner id in POST array!');
		}
		$records = array();
		$subModels = array();
		foreach ($arr as $key=>$value){
			if (strpos($key, 'sc') !== false){
				// Don't explode the last element for occurrence attributes
				$a = explode(':', $key, 3);
				$records[$a[1]][$a[2]] = $value;
			}
		}
		foreach ($records as $id => $record){
			if (! array_key_exists('present', $record) || !$record['present']){
				unset ($records[$id]);
				break;
			}
			$record['taxa_taxon_list_id'] = $id;
			$record['website_id'] = $website_id;
			$record['determiner_id'] = $determiner_id;
			$occAttrs = data_entry_helper::wrap_attributes($record, 'occurrence');
			$occ = data_entry_helper::wrap($record, 'occurrence');
			$occ['metaFields']['occAttributes']['value'] = $occAttrs;
			$subModels[] = array(
				'fkId' => 'sample_id',
				'model' => $occ);
		}

		return $subModels;
	}

	/**
	 * Wraps attribute fields (entered as normal) into a suitable container for submission.
	 * Throws an error if $entity is not something for which attributes are known to exist.
	 * @return array
	 */
	public static function wrap_attributes($arr, $entity) {
		switch ($entity) {
		case 'occurrence':
			$prefix = 'occAttr';
			break;
		case 'location':
			$prefix = 'locAttr';
			break;
		case 'sample':
			$prefix = 'smpAttr';
			break;
		default:
			throw new Exception('Unknown attribute type. Unable to wrap.');
		}
		$oap = array();
		$occAttrs = array();
		foreach ($arr as $key => $value) {
			if (strpos($key, $prefix) !== false) {
				$a = explode(':', $key);
				// Attribute in the form occAttr:36 for attribute with attribute id
				// of 36.
				$oap[] = array(
					$entity."_attribute_id" => $a[1],
					'value' => $value
				);

			}
		}
		foreach ($oap as $oa) {
			$occAttrs[] = data_entry_helper::wrap($oa, "$entity"."_attribute");
		}
		return $occAttrs;

	}
	public static function wrap( $array, $entity, $fkLink = false) {
		// Initialise the wrapped array
		$sa = array(
			'id' => $entity,
			'fields' => array()
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
				echo '<div class="error">';
				echo '<p>An error occurred when the data was submitted.</p>';
				if (is_array($response['error'])) {
					echo '<ul>';
					foreach ($response['error'] as $field=>$message)
						echo "<li>$field: $message</li>";
					echo '</ul>';
				} else {
					echo '<p class="error_message">'.$response['error'].'</p>';
				}
				if (array_key_exists('file', $response) && array_key_exists('line', $response)) {
					echo '<p>Error occurred in '.$response['file'].' at line '.$response['line'].'</p>';
				}
				if (array_key_exists('errors', $response)) {
					echo '<pre>'.print_r($response['errors'], true).'</pre>';
				}
				if (array_key_exists('trace', $response)) {
					echo '<pre>'.print_r($response['trace'], true).'</pre>';
				}
				echo '</div>';
			} elseif (array_key_exists('warning',$response)) {
				echo 'A warning occurred when the data was submitted.';
				echo '<p class="error">'.$response['error'].'</p>';
			} elseif (array_key_exists('success',$response)) {
				echo '<div class="success">Data was successfully inserted ('.
					$response['success'].')</div>';
			}
		}
		else
			echo $response;
	}

	/**
	 * Puts a spatial reference entry control, optional system selector, and map onto a data entry form.
	 * The system selector is automatically output if there is more than one system present, otherwise it
	 * is replaced by a hidden input.
	 *
	 * @param string $field_name Name of the spatial reference db field.
	 * @param string $geom_field_name Name of the geom db field.
	 * @param array $systems Associative array of the available spatial reference systems, in form code -> description.
	 * @param array $opts Associative array of additional options. Possible options are init_value, width, height, instruct, inc_google, init_lat, init_long, init_zoom.
	 */
	public static function map_picker($field_name, $geom_field_name, $systems, $opts) {
		global $javascript;
		// Handle the options
		$init_value = self::option('init_value', $opts, '');
		$width      = self::option('width', $opts, '600');
		$height     = self::option('height', $opts, '350');
		$instruct   = self::option('instruct', $opts, "Zoom the map in by double-clicking then single click on the location's centre to set the ".
					"spatial reference. The more you zoom in, the more accurate the reference will be.");
		$inc_google = self::option('inc_google', $opts, 'false');
		$init_lat   = self::option('init_lat', $opts, '7300000');
		$init_long  = self::option('init_long', $opts, '-100000');
		$init_zoom  = self::option('init_zoom', $opts, '4');
		$init_layer = self::option('init_layer', $opts, '');
		$r = '<script type="text/javascript" src="'.parent::$base_url.'/media/js/OpenLayers.js"></script>';
		$r .= '<script type="text/javascript" src="'.parent::$base_url.'/media/js/spatial-ref.js"></script>';
		$r .= '<script type="text/javascript" src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1"></script>';
		$javascript .= "init_map(\"".parent::$base_url."\", null, '$field_name', '$geom_field_name', ".
			"$inc_google, '".parent::$geoplanet_api_key."', $init_lat, $init_long, $init_zoom, '$init_layer');";

		$r .= '<input id="'.$field_name.'" name="'.$field_name.'" value="'.$init_value.'" '.
			'onblur="exit_sref();" onclick="enter_sref();"/>';
		if (count($systems)==1) {
			$srids = array_keys($systems);
			// only 1 spatial reference system, so put it into a hidden input
			$r .= '<input id="'.$field_name.'_system" name="'.$field_name.'_system" type="hidden" value="'.$srids[0].'" />';
		} else {
			$r .= '<select id="'.$field_name.'_system" name="'.$field_name.'_system">';
			foreach($systems as $srid=>$desc)
				$r .= "<option value=\"$srid\">$desc</option>";
			$r .= '</select>';
		}
		$r .= "<input type=\"hidden\" id=\"$geom_field_name\" name=\"$geom_field_name\" />";
		$r .= '<p class="instruct">'.$instruct.'</p>';
		$r .= '<div id="map" class="smallmap" style="width: '.$width.'px; height: '.$height.'px;"></div>';
		return $r;
	}

	/**
	 * Private method to find an option from an associative array of options. If not present, returns the default.
	 */
	private static function option($key, array $opts, $default)
	{
		if (array_key_exists($key, $opts)) {
			$r = $opts[$key];
		} else {
			$r = $default;
		}
		return $r;
	}

	/**
	 * Helper function to put a location search box onto the data entry page, linked to a map picker.
	 * The search box uses the GeoPlanet API to find locations.
	 *
	 * @param int $id id attribute for the returned control.
	 * @param string $link_text Text to display for the search link
	 * @param string $pref_area Text to suffix to location searches, to help keep them in the target region. E.g. Dorset.
	 *
	 * @return HTML for the location search box.
	 */
	public static function geoplanet_search($id='place_search', $link_text='find on map', $pref_area='gb') {
		$r = '<input name="'.$id.'" id="'.$id.'"/><a href="#" onclick="find_place(\''.$pref_area.'\');" />'.$link_text.'</a>' .
			'<div id="place_search_box" style="display: none"><div id="place_search_output"></div>' .
			'<a href="#" id="place_close_button" onclick="jQuery(\'#place_search_box\').hide(\'fast\');">Close</a></div>';
		return $r;
	}


	/**
	 * Retrieves a read token and passes it back as an array suitable to drop into the
	 * 'extraParams' options for an Ajax call.
	 */
	public static function get_read_auth($website_id, $password) {
		$postargs = "website_id=$website_id";
		// Get the curl session object
		$session = curl_init(parent::$base_url.'/index.php/services/security/get_read_nonce');
		// Set the POST options.
		curl_setopt ($session, CURLOPT_POST, true);
		curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		// Do the POST and then close the session
		$response = curl_exec($session);
		list($response_headers,$nonce) = explode("\r\n\r\n",$response,2);
		return array(
			'auth_token' => sha1("$nonce:$password"),
			'nonce' => $nonce
		);
	}

	/**
	 * Retrieves a token and inserts it into a data entry form which authenticates that the
	 * form was submitted by this website.
	 */
	public static function get_auth($website_id, $password) {
		$postargs = "website_id=$website_id";
		// Get the curl session object
		$session = curl_init(parent::$base_url.'/index.php/services/security/get_nonce');
		// Set the POST options.
		curl_setopt ($session, CURLOPT_POST, true);
		curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		// Do the POST and then close the session
		$response = curl_exec($session);
		list($response_headers,$nonce) = explode("\r\n\r\n",$response,2);
		curl_close($session);
		$result = '<input id="auth_token" name="auth_token" type="hidden" ' .
			'value="'.sha1("$nonce:$password").'">'."\r\n";
		$result .= '<input id="nonce" name="nonce" type="hidden" ' .
			'value="'.$nonce.'">'."\r\n";
		return $result;
	}
}
?>
