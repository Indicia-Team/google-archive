<?php
/**
 * Indicia, the OPAL Online Recording Toolkit.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 *
 * @package Client
 * @subpackage PrebuiltForms
 * @author  Indicia Team
 * @license http://www.gnu.org/licenses/gpl.html GPL 3.0
 * @link  http://code.google.com/p/indicia/
 */

/**
 * List of methods that can be used for a prebuilt form report configuration.
 * @package Client
 * @subpackage PrebuiltForms.
 */

/**
 * Return a list of parameter definitions for a form that includes definition of a report.
 * @return array List of parameter definitions.
 */
function iform_report_get_report_parameters() {
  return array(
    array(
      'name'=>'report_name',
      'caption'=>'Report Name',
      'description'=>'The name of the report file to load into the verification grid, excluding the .xml suffix.',
      'type'=>'string'
    ), array(
      'name' => 'param_presets',
      'caption' => 'Preset Parameter Values',
      'description' => 'To provide preset values for any report parameter and avoid the user having to enter them, enter each parameter into this '.
          'box one per line. Each parameter is followed by an equals then the value, e.g. survey_id=6. You can use {user_id} as a value which will be replaced by the '.
          'user ID from the CMS logged in user or {username} as a value replaces with the logged in username. Preset Parameter Values can not be overridden by the user.',
      'type' => 'textarea',
      'required' => false
    ), array(
      'name' => 'param_defaults',
      'caption' => 'Default Parameter Values',
      'description' => 'To provide default values for any report parameter which allow the report to run initially but can be overridden, enter each parameter into this '.
          'box one per line. Each parameter is followed by an equals then the value, e.g. survey_id=6. You can use {user_id} as a value which will be replaced by the '.
          'user ID from the CMS logged in user or {username} as a value replaces with the logged in username. Unlike preset parameter values, parameters referred '.
          'to by default parameter values are displayed in the parameters form and can therefore be changed by the user.',
      'type' => 'textarea',
      'required' => false
    ), array(
      'name' => 'output',
      'caption' => 'Output Mode',
      'description' => 'Select what combination of the params form and report output will be output. This can be used to develop a single page '.
          'with several reports linked to the same parameters form, e.g. using the Drupal panels module.',
      'type' => 'select',
      'required' => true,
      'options' => array(
        'default'=>'Include a parameters form and output',
        'form'=>'Parameters form only - the output will be displayed elsewhere.',
        'output'=>'Output only - the params form will be output elsewhere.',
      ),
      'default' => 'default'
    ), array(
      'name' => 'columns_config',
      'caption' => 'Columns Configuration JSON',
      'description' => 'JSON that describes the columns configuration parameter sent to the report grid component.',
      'type' => 'textarea',
      'required' => false
    ), array(
      'name' => 'refresh_timer',
      'caption' => 'Automatic reload seconds',
      'description' => 'Set this value to the number of seconds you want to elapse before the report will be automatically reloaded, useful for '.
      'displaying live data updates at BioBlitzes. Combine this with Page to reload to define a sequence of pages that load in turn.',
      'type' => 'int',
      'required' => false
    ), array(
      'name' => 'load_on_refresh',
      'caption' => 'Page to reload',
      'description' => 'Provide the full URL of a page to reload after the number of seconds indicated above.',
      'type' => 'string',
      'required' => false
    )
  );
}

/**
 * Retreives the options array required to set up a report according to the default
 * report parameters.
 * @global <type> $indicia_templates
 * @param string $args
 * @param <type> $auth
 * @return string
 */
function iform_report_get_report_options($args, $auth) {
  global $indicia_templates;
  // handle auto_params_form for backwards compatibility
  if (empty($args['output']) && !empty($args['auto_params_form'])) {
    if (!$args['auto_params_form'])
      $args['output']='output';
  }
  // put each param control in a div, which makes it easier to layout with CSS
  $indicia_templates['prefix']='<div id="container-{fieldname}" class="param-container">';
  $indicia_templates['suffix']='</div>';
  $r = '';
  $presets = _get_initial_vals('param_presets', $args);
  $defaults = _get_initial_vals('param_defaults', $args);
  // default columns behaviour is to just include anything returned by the report
  $columns = array();
  // this can be overridden
  if (isset($args['columns_config']) && !empty($args['columns_config']))
    $columns = json_decode($args['columns_config'], true);
  $reportOptions = array(
    'id' => 'report-grid',
    'class' => '',
    'thClass' => '',
    'dataSource' => $args['report_name'],
    'mode' => 'report',
    'readAuth' => $auth['read'],
    'columns' => $columns,
    'itemsPerPage' => $args['items_per_page'],
    'extraParams' => $presets,
    'paramDefaults' => $defaults,
    'galleryColCount' => isset($args['gallery_col_count']) ? $args['gallery_col_count'] : 1,
    'headers' => isset($args['gallery_col_count']) && $args['gallery_col_count']>1 ? false : true
  );
  if (empty($args['output']) || $args['output']=='default') {
    $reportOptions['autoParamsForm'] = true;
    $reportOptions['id'] = 'report-grid';
  } elseif ($args['output']=='form') {
    $reportOptions['autoParamsForm'] = true;
    $reportOptions['paramsOnly'] = true;
    $reportOptions['id'] = 'params-form';
  } else {
    $reportOptions['autoParamsForm'] = false;
    $reportOptions['id'] = $args['report_name'];
    $reportOptions['paramsFormId'] = 'params-form';
  }
  // Set up a page refresh for dynamic update of the report at set intervals
  if ($args['refresh_timer']!==0 && is_numeric($args['refresh_timer'])) { // is_numeric prevents injection
    if (isset($args['load_on_refresh']) && !empty($args['load_on_refresh']))
      report_helper::$javascript .= "setTimeout('window.location=\"".$args['load_on_refresh']."\";', ".$args['refresh_timer']."*1000 );\n";
    else
      report_helper::$javascript .= "setTimeout('window.location.reload( false );', ".$args['refresh_timer']."*1000 );\n";
  }
  return $reportOptions;
}

/**
 * Internal method to read either the preset or default param values from the config form parameters. Returns an associative
 * array.
 */
function _get_initial_vals($type, $args) {
  global $user;
  $r = array();
  if ($args[$type] != ''){
    $params = explode("\n", $args[$type]);
    foreach ($params as $param) {
      if (!empty($param)) {
        $tokens = explode('=', $param);
        if (count($tokens)==2) {
          // perform any replacements on the intiial values
          if ($tokens[1]=='{user_id}') {
            $tokens[1]=$user->uid;
          } else if ($tokens[1]=='{username}') {
            $tokens[1]=$user->name;
          }
          $r[$tokens[0]]=trim($tokens[1]);
        } else {
          throw new Exception('Some of the preset or default parameters defined for this page are not of the form param=value.');
        }
      }
    }
  }
  return $r;
}