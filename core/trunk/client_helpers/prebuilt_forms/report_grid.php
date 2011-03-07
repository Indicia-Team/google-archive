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
 * Prebuilt Indicia data form that lists the output of any report
 *
 * @package Client
 * @subpackage PrebuiltForms
 */
class iform_report_grid {
  /**
   * Get the list of parameters for this form.
   * @return array List of parameters that this form requires.
   */
  public static function get_parameters() {
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
        'name' => 'columns_config',
        'caption' => 'Columns Configuration JSON',
        'description' => 'JSON that describes the columns configuration parameter sent to the report grid component.',
        'type' => 'textarea',
        'required' => false
      ), array(
        'name' => 'gallery_col_count',
        'caption' => 'Gallery Column Count',
        'description' => ' If set to a value greater than one, then each grid row will contain more than one record of data from the database, allowing '.
            ' a gallery style view to be built.',
        'type' => 'int',
        'required' => false,
        'default' => 1
      ),
      array(
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
      ), array(
        'name' => 'items_per_page',
        'caption' => 'Items per page',
        'description' => 'Maximum number of rows shown on each page of the table',
		    'type' => 'int',
        'default' => 20,
        'required' => true
      ),
      array(
        'name' => 'output',
        'caption' => 'Output Mode',
        'description' => 'Select what combination of the params form and report grid will be output. This can be used to develop a single page '.
            'with several reports linked to the same parameters form, e.g. using the Drupal panels module.',
		    'type' => 'select',
        'required' => true,
        'options' => array(
          'default'=>'Include a parameters form and grid',
          'form'=>'Parameters form only - the grid will be output elsewhere.',
          'grid'=>'Grid only - the params form will be output elsewhere.',
        )       
      )
    );
  }

  /**
   * Return the form title.
   * @return string The title of the form.
   */
  public static function get_title() {
    return 'Report grid - a simple grid report';
  }

  /**
   * Return the Indicia form code
   * @param array $args Input parameters.
   * @param array $node Drupal node object
   * @param array $response Response from Indicia services after posting a verification.
   * @return HTML string
   */
  public static function get_form($args, $node, $response) {
    require_once drupal_get_path('module', 'iform').'/client_helpers/report_helper.php';
    require_once drupal_get_path('module', 'iform').'/client_helpers/map_helper.php';
    global $indicia_templates;
    // handle auto_params_form for backwards compatibility
    if (empty($args['output']) && !empty($args['auto_params_form'])) {
      if (!$args['auto_params_form']) 
        $args['output']='grid';
    }
    // put each param control in a div, which makes it easier to layout with CSS
    $indicia_templates['prefix']='<div id="container-{fieldname}" class="param-container">';
    $indicia_templates['suffix']='</div>';
    $auth = report_helper::get_read_write_auth($args['website_id'], $args['password']);
    $r = '';
    $presets = self::get_initial_vals('param_presets', $args);
    $defaults = self::get_initial_vals('param_defaults', $args);
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
    // Add a download link - get_report_data does not use paramDefaults but needs all settings in the extraParams 
    $r .= '<br/>'.report_helper::report_download_link($reportOptions);
    // now the grid
    $r .= '<br/>'.report_helper::report_grid($reportOptions);
    // Set up a page refresh for dynamic update of the report at set intervals
    if ($args['refresh_timer']!==0 && is_numeric($args['refresh_timer'])) { // is_numeric prevents injection
      if (isset($args['load_on_refresh']) && !empty($args['load_on_refresh']))
        report_helper::$javascript .= "setTimeout('window.location=\"".$args['load_on_refresh']."\";', ".$args['refresh_timer']."*1000 );\n";
      else
        report_helper::$javascript .= "setTimeout('window.location.reload( false );', ".$args['refresh_timer']."*1000 );\n";
    }
    return $r;
  }
  
  /**
   * Private method to read either the preset or default param values from the config form parameters. Returns an associative
   * array.
   */
  private static function get_initial_vals($type, $args) {
    global $user; 
    $r = array();
    if ($args['param_presets'] != ''){
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

}