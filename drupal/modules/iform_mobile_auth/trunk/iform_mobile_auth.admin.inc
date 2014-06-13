<?php

/**
 * @file
 * Iform Mobile Authentication Module's UI.
 */

/** 
 * Creates a blank form for a new app or populates it if editing.
 */
function iform_mobile_auth_app($form_state, $db_app = NULL) {
  if ($db_app) {
    //editing an app
    $app = db_fetch_object($db_app);
    if($app != FALSE && userHasPermission($app)){
      drupal_set_title(t('%title settings', array('%title' => check_plain($app->title))));
    } else{
      drupal_access_denied(); 
    }
  }
  else {
    //set variables to default values for creating new app
    $app = new stdClass;
    $app->enabled = 1;
    $app->debug = 0;
    $app->name = '';
    $app->title = '';
    $app->description = '';
    $app->secret = iform_mobile_auth_generate_random_string(8);
    
    //extra fields
    $app->registration_subject = 'Welcome to the !site';
    $app->registration_body = 'Activate your account by clicking on the following link:<br/>!activation_url';
    $app->redirect_unsuccessful = '<front>';
    $app->registration_redirect = '<front>';
  } 
    
  //build form
  $form['enabled'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enabled'),
    '#default_value' => $app->enabled,
    '#description' => t('Check to enable app.')
  );
  $form['debug'] = array(
    '#type' => 'checkbox',
    '#title' => t('Debug mode'),
    '#default_value' => $app->debug,
    '#description' => t('Check to enable debug mode. DO NOT ENABLE ON YOUR PRODUCTION SITE')
  );
  $form['title'] = array(
    '#type' => 'textfield',
    '#title' => t('Title'),
    '#default_value' => $app->title,
    '#description' => t('Set the human readable title for this app.'),
    '#required' => TRUE,
  );
  $form['description'] = array(
    '#type' => 'textarea',
    '#title' => t('App description'),
    '#description' => t('Short app description.'),
    '#default_value' => $app->description,
  );
  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => t('App name'),
    '#default_value' => $app->name,
    '#description' => t('Set the app name to be used in forms.'),
    '#required' => TRUE,
  );
  $form['secret'] = array(
    '#type' => 'textfield',
    '#title' => t('Shared secret'),
    '#default_value' => $app->secret, 
    '#description' => t('Set the shared secret to be used in forms.'),
    '#required' => TRUE,
  );
  //extra
  $form['extra'] = array(
    '#type' => 'fieldset',
    '#title' => t('Extra parameters'),
    '#collapsible' => TRUE, 
    '#collapsed' => TRUE,
  );
  $form['extra']['registration_subject'] = array(
    '#type' => 'textfield',
    '#title' => t('Subject'),
    '#default_value' => $app->registration_subject,
    '#description' => t('User registration email subject heading. Note: Only !site is supported.')
  );
  $form['extra']['registration_body'] = array(
    '#type' => 'textarea',
    '#title' => t('Body'),
    '#default_value' => $app->registration_body,
    '#description' => t('User registration email body. Note: only !activation_url is supported. Emails are sent in plain text <i>not</i> HMTL!')
  );
  $form['extra']['registration_redirect'] = array(
    '#type' => 'textfield',
    '#title' => t('Redirection link'),
    '#default_value' => $app->registration_redirect,
    '#description' => t('The url to redirect users to after clicking on the confirmation link. E.g. "&lt;front&gt;", "/node", "http://somewhere.com"')
  );
  $form['extra']['redirect_unsuccessful'] = array(
    '#type' => 'textfield',
    '#title' => t('Invalid redirection link'),
    '#default_value' => $app->redirect_unsuccessful,
    '#description' => t('The url to redirect anonymous users to if attempting to reactive an already expired or invalid link E.g. "&lt;front&gt;", "/node", "http://somewhere.com"')
  );
  
  if (!empty($app->id)) {
    //editing existing app
    $form['id'] = array(
      '#type' => 'value',
      '#value' => $app->id
    );
    $form['changed'] = array(
      '#type' => 'value',
      '#value' => time()
    );
  } else {
    //new app
    $time = time();
    global $user;
    $form['created_by'] = array(
      '#type' => 'value',
      '#value' => $user->uid 
    );
    $form['created'] = array(
      '#type' => 'value',
      '#value' => $time
    );
    $form['changed'] = array(
      '#type' => 'value',
      '#value' => $time
    );
  }

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save')
  );
  
  //check if user has access to create new app
  if(user_access('user') || user_access('admin')){
   return $form;
  } else{
   drupal_access_denied(); 
  }
}


/**
 * Main administrative page displaying the information about stored applications.
 * Allows to edit/delete each application's information and add new ones.
 */
function iform_mobile_auth_dashboard() {
  $r = '<p>Mobile applications accounts saved in Indicia Mobile Authorisation Module. 
    This allows you to manage unique accounts 
    as well as the ones sharing the same <i>appname</i> or <i>appsecret</i>.</p>';
    
  $r .= '<p>If used by older systems, there should be created an anonymous account with
    <i>appname</i> set to <i>anonymous</i> and an appropriate <i>appsecret</i>, allowing those older apps to login without an <i>appname</i></p>';
      
  $r .= '<fieldset><legend>Authentication accounts</legend>';
  
  //create table
  $header = array('Enabled', 'Title', 'Description', 'Debugging', '', '', '');
  $rows = array();
  $apps = iform_mobile_auth_app_load();
  while ($app = db_fetch_object($apps)){
    if(userHasPermission($app)){
      $row = array();
      $row[0] = '<input type="radio"' . ($app->enabled ? 'checked' : 'disabled') . ' >';
      $row[1] = $app->title;
      $row[2] = $app->description;
      $row[3] = '<input type="radio"' . ($app->debug ? 'checked' : 'disabled') . '>';
      $row[4] = l(t('Edit'), 'admin/settings/iform/mobile/' . $app->id);
      $row[5] = l(t('Delete'), 'admin/settings/iform/mobile/delete/' . $app->id);
      
      $rows[] = $row;
    }
  }
  
  $table = theme('table', $header, $rows);
  $r .= $table;
  
  // Add links beneath the table.
  $links = array(
    'link-add' => array(
      'title' => t('Add Mobile Application'),
      'href' => 'admin/settings/iform/mobile',
    ),
  );
  
  $r .= theme_links($links); 
  $r .= '</fieldset>';
  
  return $r;
}

/**
 * Submit handler to save an app.
 * Implements hook_submit() to submit a form produced by iform_mobile_auth_app().
 */
function iform_mobile_auth_app_submit($form, &$form_state) {
  if (empty($form_state['values']['id'])) {
    // Save new app.
    $res = drupal_write_record('iform_mobile_auth', $form_state['values']);
    $message = 'Created %app.';  
  }
  else {
    // Save existing app.
    $res = drupal_write_record('iform_mobile_auth', $form_state['values'], 'id');
    $message = 'Saved %app.';  
  }

  //inform user and return to dashboard
    drupal_set_message(t($message, array('%app' => $form_state['values']['title'])));
    $form_state['redirect'] = 'admin/settings/iform/mobile/dashboard';
} 

/** 
 * A confirmation page to check if the user is sure to delete an app from a database.
 */
function iform_mobile_auth_delete($form_state, $db_app) {
  $app = db_fetch_object($db_app);
  //set up the form information to be passed to submit handler
  $form = array();
  $form['id'] = array(
    '#type' => 'value',
    '#value' => $app->id,
  );
   $form['title'] = array(
    '#type' => 'value',
    '#value' => $app->title,
  );

  $message = t('Are you sure you want to delete %app ?', array('%app' => $app->title));
  
  // check if the user has permission to delete an app
  if(userHasPermission($app)){
    return confirm_form($form, $message,
      'admin/settings/iform/mobile/dashboard', //redirect if cancelled
      t('This action cannot be undone.'),
      t('Delete'),
      t('Cancel')
    );
    //proceed to delete_submit handler
  } else {
    drupal_access_denied(); 
  }
}

/**
 * Submit handler to delete an app.
 * Implements hook_submit() to submit a confirmation form produced by iform_mobile_auth_delete().
 */
function iform_mobile_auth_delete_submit($form, &$form_state) {
  if ($form_state['values']['confirm']) {
    //delete app from database
    db_query('DELETE FROM {iform_mobile_auth} WHERE id = %d', $form_state['values']['id']);
    
    //inform user and return to dashboard
    $message = 'Deleted %app.';
    drupal_set_message(t($message, array('%app' => $form_state['values']['title'])));
  }
  $form_state['redirect'] = 'admin/settings/iform/mobile/dashboard';
} 


