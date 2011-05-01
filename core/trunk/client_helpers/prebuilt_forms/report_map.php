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

require_once('includes/report.php');
require_once('includes/map.php');


/**
 * Prebuilt Indicia data form that lists the output of any report on a map.
 *
 * @package Client
 * @subpackage PrebuiltForms
 */
class iform_report_map {

  /** 
   * Return the form metadata.
   * @return string The definition of the form.
   */
  public static function get_report_map_definition() {
    return array(
      'title'=>'Report Map',
      'category' => 'Reporting',
      'description'=>'Outputs data from a report onto a map. To work, the report must include a column containing spatial data. '.
          'Can automatically include the report parameters form required for the generation of the report.'
    );
  }
  
  /**
   * Get the list of parameters for this form.
   * @return array List of parameters that this form requires.
   */
  public static function get_parameters() {
    return array_merge(
      iform_report_get_report_parameters(),
      iform_map_get_map_parameters(),
      array(
        array(
          'name' => 'layer_picker',
          'caption' => 'Include Layer Picker',
          'description' => 'Choose whether to include a layer picker and where to include it. Use the '.
              'CSS id map-layer-picker for styling.',
          'type' => 'select',
          'required' => true,
          'options' => array(
            'none'=>'Exclude the layer picker',
            'before'=>'Include the layer picker before the map.',
            'after'=>'Include the layer picker after the map.',
          ),
          'default'=>'none'
        ),
        array(
          'name' => 'legend',
          'caption' => 'Include Legend',
          'description' => 'Choose whether to include a legend and where to include it. Use the '.
              'CSS id map-legend for styling.',
          'type' => 'select',
          'required' => true,
          'options' => array(
            'none'=>'Exclude the legend',
            'before'=>'Include the legend before the map.',
            'after'=>'Include the legend after the map.',
          ),
          'default'=>'after'
        ),
        array(
          'name' => 'geoserver_layer',
          'caption' => 'GeoServer Layer',
          'description' => 'For improved mapping performance, specify a layer on GeoServer which '.
              'has the same attributes and output as the report file. Then the report map can output '.
              'the contents of this layer filtered by the report parameters, rather than build a layer '.
              'from the report data.',
          'type' => 'text_input',
          'required' => false,
          'group' => 'WMS Mapping'
        ),
        array(
          'name' => 'geoserver_layer_style',
          'caption' => 'GeoServer Layer Style',
          'description' => 'Optional name of the SLD file available on GeoServer which is to be applied to the GeoServer layer.',
          'type' => 'text_input',
          'required' => false,
          'group' => 'WMS Mapping'
        ),
        array(
          'name' => 'cql_template',
          'caption' => 'CQL Filter Template',
          'description' => 'Use with the geoserver_layer to provide a template for the CQL to filter the layer '.
              'according to the parameters of the report. For example, if you are using the report called '.
              '<em>map_occurrences_by_survey</em> then you can set the geoserver_layer to the indicia:detail_occurrences '.
              'layer and set this to <em>INTERSECTS(geom, #searchArea#) AND survey_id=#survey#</em>.',
          'type' => 'textarea',
          'required' => false,
          'group' => 'WMS Mapping'
        )
      )
    );
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
    $auth = report_helper::get_read_write_auth($args['website_id'], $args['password']);
    $reportOptions = iform_report_get_report_options($args, $auth);
    $r = '<div class="ui-helper-clearfix">';
    $reportOptions['geoserverLayer'] = $args['geoserver_layer'];
    $reportOptions['geoserverLayerStyle'] = $args['geoserver_layer_style'];
    $reportOptions['cqlTemplate'] = $args['cql_template'];
    $r .= '<br/>'.report_helper::report_map($reportOptions);
    $options = iform_map_get_map_options($args, $readAuth);
    $olOptions = iform_map_get_ol_options($args);
    // This is used for drawing, so need an editlayer, but not used for input
    $options['editLayer'] = true;
    $options['editLayerInSwitcher'] = true;
    $options['clickForSpatialRef'] = false;
    if ($args['layer_picker']!='none') {
      $picker = array(
        'id'=>'map-layer-picker',
        'includeIcons'=>false,
        'includeSwitchers'=>true,
        'includeHiddenLayers'=>true
      );
      if ($args['layer_picker']=='before')
        $r .= map_helper::layer_list($picker);
      // as we have a layer picker, we can drop the layerSwitcher from the OL map.
      $options['standardControls']=array('panZoom');
    }
    if ($args['legend']!='none') {
      $legend = array(
        'id'=>'map-legend',
        'includeIcons'=>true,
        'includeSwitchers'=>false,
        'includeHiddenLayers'=>false
      );
      if ($args['legend']=='before')
        $r .= map_helper::layer_list($legend);
    }
    $r .= map_helper::map_panel($options, $olOptions);
    if ($args['layer_picker']=='after')
      $r .= map_helper::layer_list($picker);
    if ($args['legend']=='after')
      $r .= map_helper::layer_list($legend);
    $r .= '</div>';
    return $r;
  }

}