<?php

/** 
 * A form definition for providing global condifuration options for Indicia. Used both in the installation 
 * profile as an install task, and provided on the admin menu.
 */
function iform_configuration_form(&$form_state, $url='', $submit_handler='') {
  require_once('client_helpers/helper_config.php');
  global $indicia_warehouses;
  iform_load_warehouse_array();
  foreach($indicia_warehouses as $warehouse=>$def) 
    $warehouses[$warehouse] = $def['title'];
  $warehouses[] = t('Other');
  $form['indicia_warehouse'] = array(
    '#type' => 'radios',
    '#title' => t('Indicia Warehouse'),
    '#options' => $warehouses,
    '#description' => t('Select the Indicia Warehouse to connect to, or select Other and enter the details in the Warehouse URL and GeoServer URL fields.'),
    '#required' => TRUE,
    '#default_value' => variable_get('indicia_warehouse', ''),
  );
  $form['other_warehouse'] = array(
    '#type' => 'fieldset',
    '#title' => t('Other Warehouse Details')
  );
  $form['other_warehouse']['indicia_base_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Warehouse URL'),
    '#description' => 'If selecting Other for the Indicia Warehouse option, please enter the URL of the Indicia Warehouse you are connecting to, otherwise ignore this setting.',
    '#maxlength' => 255,
    '#required' => FALSE,
    '#default_value' => variable_get('indicia_base_url', ''),
  );
  $form['other_warehouse']['indicia_geoserver_url'] = array(
    '#type' => 'textfield',
    '#title' => t('GeoServer URL'),
    '#description' => 'If selecting Other for the Indicia Warehouse option, please enter the URL of the GeoServer instance you are connecting to, otherwise ignore this setting. '.
        'This is optional, if not specified then you will not be able to use some of the advanced mapping facilities provided by GeoServer.',
    '#maxlength' => 255,
    '#required' => FALSE,
    '#default_value' => variable_get('indicia_geoserver_url', ''),
  );
  $form['indicia_website_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Indicia Website ID'),
    '#description' => 'Please enter the ID given to your website record when your website was registered on the Indicia Warehouse.',
    '#size' => 10,
    '#maxlength' => 10,
    '#required' => TRUE,
    '#default_value' => variable_get('indicia_website_id', ''),
  );
  // require the password only if not previously set.
  $pwdRequired = (variable_get('indicia_password', '')=='');
  if ($pwdRequired)
    $pwdDescription = t('Please enter the password specified when your website was registered on the Indicia Warehouse.');
  else
    $pwdDescription = t('If you need to change it, enter the password specified when your website was registered on the Indicia Warehouse. '.
        'Otherwise leave the password blank to keep your previous settings.');
  $form['indicia_password'] = array(
    '#type' => 'password_confirm',
    '#description' => $pwdDescription,
    '#required' => $pwdRequired,
  );
  $form['api_keys'] = array(
    '#type' => 'fieldset',
    '#title' => t('API Keys')
  );
  $form['api_keys']['indicia_geoplanet_api_key'] = array(
    '#type' => 'textfield',
    '#title' => t('GeoPlanet API Key'),
    '#description' => t('The Yahoo! GeoPlanet API is one option to lookup place names when you use the place search control. '.
        'It references a global database of places and returns the list of possibilities with their spatial references '.
        'to Indicia. To obtain your own API key for GeoPlanet, please visit <a target="_blank" href="http://developer.yahoo.com/geo/geoplanet/">'.
        'Yahoo! GeoPlanet</a> and follow the link to get an Application ID. '),
    '#required' => FALSE,
    '#default_value' => variable_get('indicia_geoplanet_api_key', helper_config::$geoplanet_api_key),
  );
   $form['api_keys']['indicia_google_api_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Google API Key'),
    '#description' => t('The Google API key is required to allow use of Google map layers but can be left blank if you do not intend '.
        'to use Google maps. To obtain your own key, please visit <a target="_blank" href="http://code.google.com/apis/maps/signup.html">Google Maps Signup</a>.'),
    '#required' => FALSE,
    '#default_value' => variable_get('indicia_google_search_api_key', helper_config::$google_search_api_key),
  );
  $form['api_keys']['indicia_google_search_api_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Google Search API Key'),
    '#description' => t('The Google Search API is a second option to lookup place names when you use the place search control. '.
        'It is also used by the Postcode textbox control to georeference postcodes you input. To obtain your own API key '.
        'for the Google Search API, please visit <a target="_blank" href="http://code.google.com/apis/loader/signup.html">'.
        'Google Search API</a> and follow the instructions to get an Application ID. '),
    '#required' => FALSE,
    '#default_value' => variable_get('indicia_google_search_api_key', helper_config::$google_search_api_key),
  );
  $form['continue'] = array(
    '#type' => 'submit', 
    '#value' => t('Save'),
  );
  
  // @todo show a map to set the default zoom level and centre.
  
  // Note that #action is set to the url passed through from
  // installer, ensuring that it points to the same page, and
  // #redirect is FALSE to avoid broken installer workflow.
  $form['errors'] = array();
  if (!empty($url)) {
    $form['#action'] = $url;
    $form['#redirect'] = FALSE;
  }
  if (empty($submit_handler))
    $form['#submit'][] = 'indicia_configuration_form_submit';
  else
    $form['#submit'][] = $submit_handler;
  return $form;
}

function indicia_configuration_form_validate($form, &$form_state) {
  if ($form_state['values']['indicia_warehouse']==t('Other') && empty($form_state['values']['indicia_base_url']))
    form_set_error('indicia_website_id', t('Please supply a warehouse URL for connection to Indicia, or select a pre-configured connection.')); 
  if (empty($form_state['values']['indicia_website_id']))
    form_set_error('indicia_website_id', t('Please supply a website ID for connection to Indicia.'));
  if (empty($form_state['values']['indicia_password']))
    form_set_error('indicia_password', t('Please supply a password for connection to Indicia.'));
}


function indicia_configuration_form_submit($form, &$form_state) {
  variable_set('indicia_warehouse', $form_state['values']['indicia_warehouse']);
  if ($form_state['values']['indicia_warehouse']==t('Other')) {
    variable_set('indicia_base_url', $form_state['values']['indicia_base_url']);
    variable_set('indicia_geoserver_url', $form_state['values']['indicia_geoserver_url']);
  } else {
    global $indicia_warehouses;
    iform_load_warehouse_array();
    foreach($indicia_warehouses as $warehouse=>$def) {
      if ($warehouse==$form_state['values']['indicia_warehouse']) {
        variable_set('indicia_base_url', $def['base_url']);
        variable_set('indicia_geoserver_url', $def['geoserver_url']);
        break;
      }
    }
  }
  variable_set('indicia_website_id', $form_state['values']['indicia_website_id']);
  if (!empty($form_state['values']['indicia_password']))
    variable_set('indicia_password', $form_state['values']['indicia_password']);
  variable_set('indicia_geoplanet_api_key', $form_state['values']['indicia_geoplanet_api_key']);
  variable_set('indicia_google_search_api_key', $form_state['values']['indicia_google_search_api_key']);
  drupal_set_message(t('Indicia settings saved.'));
}

/**
 * Utility function to populate the list of warehouses in the global $indicia_warehouses. Each warehouse is loaded from an .inc
 * file in the warehouses sub-folder.
 */
function iform_load_warehouse_array() {
  global $indicia_warehouses;
  $indicia_warehouses = array();
  foreach(glob(drupal_get_path('module', 'iform') . '/warehouses/*.inc') as $warehouse_file) {
    require($warehouse_file);
  }
}