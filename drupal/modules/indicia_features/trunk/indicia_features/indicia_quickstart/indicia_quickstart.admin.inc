<?php

function indicia_quickstart_report_setttings_form() {
  $form=array();
 $form['attributes']=array(
    '#type'=>'fieldset',
    '#title'=>t('Report parameter settings'),
    '#collapsible'=>true,
    '#collapsed'=>false
  );
  $form['attributes']['param_certainattr'] = array(
    '#type'=>'textfield',
    '#title'=>'Certainty attr ID',
    '#description'=>t('Enter the ID of the attribute used to store certainty of each record. This is used to allow reports to filter or display '.
        'records differently depending on their level of certainty. This must be a custom occurrence attribute '.
        'on the warehouse which is a lookup. The termlist associated with the lookup should contain terms which are sorted using the Sort Order '.
        'of each term, with the most certain term having the lowest sort order (first in the list). E.g. the termlist could contain terms '.
        'Certain, Likely and Possible with sort orders set to 1,2 & 3 respectively.'),
    '#default_value'=>variable_get('iform_param_certainattr', '')
  );
  $form['attributes']['param_maxcertain'] = array(
    '#type'=>'textfield',
    '#title'=>'Max sort order for Certain terms',
    '#description'=>t('Enter the maximum sort order of the set of terms which should be considered certain. In the example given above you would '.
        'enter a value 1 here.'),
    '#default_value'=>variable_get('iform_param_maxcertain', 1)
    
  );
  $form['attributes']['param_maxlikely'] = array(
    '#type'=>'textfield',
    '#title'=>'Max sort order for Likely terms',
    '#description'=>t('Enter the maximum sort order of the set of terms which should be considered likely or certain. In the example given above you would '.
        'enter a value 2 here. If you are not using a Likely certainty term then enter the same value as you used for the max sort order for certain terms.'),
    '#default_value'=>variable_get('iform_param_maxlikely', 2)
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save settings')    
  );
  return $form;
  
}

/**
 * Validate method for the report settings config form.
 */
function indicia_quickstart_report_setttings_form_validate($form, &$form_state) {
  if (!preg_match('/^[0-9]*$/', $form_state['values']['param_certainattr']))
    form_set_error('param_certainattr', t('The certainty attr ID should be a positive whole number.'));
  if (!preg_match('/^[0-9]*$/', $form_state['values']['param_maxcertain']))
    form_set_error('param_m,axcertain', t('The max sort order for Certain terms should be a positive whole number.'));
  if (!preg_match('/^[0-9]*$/', $form_state['values']['param_maxlikely']))
    form_set_error('param_maxlikely', t('The max sort order for Likely terms should be a positive whole number.'));
  if (!empty($form_state['values']['param_maxcertain']) && 
      !empty($form_state['values']['param_maxlikely']) &&
      $form_state['values']['param_maxcertain']>$form_state['values']['param_maxlikely'])
    form_set_error('param_maxlikely', t('The max sort order for Likely terms should be a greater than the max sort order for Certain terms, if set.'));
}

/**
 * Submit method for the reports config form.
 */
function indicia_quickstart_report_setttings_form_submit($form, &$form_state) {
  variable_set('iform_param_certainattr', $form_state['values']['param_certainattr']);
  variable_set('iform_param_maxcertain', $form_state['values']['param_maxcertain']);
  variable_set('iform_param_mincertain', $form_state['values']['param_mincertain']);
  drupal_set_message(t('Settings saved.'));
}
