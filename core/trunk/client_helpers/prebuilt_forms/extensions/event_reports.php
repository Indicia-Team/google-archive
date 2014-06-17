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
 * @package	Client
 * @subpackage PrebuiltForms
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL 3.0
 * @link 	http://code.google.com/p/indicia/
 */
 
/**
 * Extension class that supplies new controls to support reporting on public events such as bioblitzes.
 */
class extension_event_reports {

  /**
   * Outputs a map with an overlay of regions, showing a count for each. Default is to count records, but can
   * be configured to count taxa.
   *
   * @param array $auth Authorisation tokens.
   * @param array $args Form arguments (the settings on the form edit tab).
   * @param string $tabalias The alias of the tab this is being loaded onto.
   * @param array $options The options passed to this control using @option=value settings in the form structure.
   * Options supported are those which can be passed to the report_helper::report_map method. In addition
   * set @output=species to configure the report to show a species counts map and set @title=... to 
   * include a heading in the output.   
   * @param string $path The page reload path, in case it is required for the building of links.
   * @return string HTML to insert into the page for the location map. JavaScript is added to the variables in helper_base.
   *
   * @link http://www.biodiverseit.co.uk/indicia/dev/docs/classes/report_helper.html#method_report_map API docs for report_helper::report_map
   */
  public static function count_by_location_map($auth, $args, $tabalias, $options, $path) {
    iform_load_helpers(array('map_helper', 'report_helper'));
    require_once iform_client_helpers_path() . 'prebuilt_forms/includes/map.php';
    $mapOptions = iform_map_get_map_options($args, $auth['read']);
    $olOptions = iform_map_get_ol_options($args);
    $mapOptions['clickForSpatialRef'] = false;
     if ($args['interface']!=='one_page')
      $mapOptions['tabDiv'] = $tabalias;
    $r = self::output_title($options);
    $r .= map_helper::map_panel($mapOptions, $olOptions);
    if (!empty($options['output']) && $options['output']==='species')
      $type='species';
    else
      $type='occurrence';
    $reportOptions = array_merge(
      iform_report_get_report_options($args, $auth['read']),
      array(
        'dataSource' => "library/locations/filterable_{$type}_counts_mappable",
        'featureDoubleOutlineColour' => '#f7f7f7',
        'rowId' => 'id'
      ),
      $options
    );
    $r .= report_helper::report_map($reportOptions);
    return $r; 
  }
  
  /**
   * Outputs a block with total records, species and photos for the event.  
   
   * @param array $auth Authorisation tokens.
   * @param array $args Form arguments (the settings on the form edit tab).
   * @param string $tabalias The alias of the tab this is being loaded onto.
   * @param array $options The options passed to this control using @option=value settings in the form structure.
   * Options supported are those which can be passed to the report_helper::freeform_report method and set @title=... to 
   * include a heading in the output.
   * @param string $path The page reload path, in case it is required for the building of links.
   * @return string HTML to insert into the page for the location map. JavaScript is added to the variables in helper_base.
   *
   * @link http://www.biodiverseit.co.uk/indicia/dev/docs/classes/report_helper.html#method_freeform_report API docs for report_helper::freeform_report
   */
  public static function totals_block($auth, $args, $tabalias, $options, $path) {
    iform_load_helpers(array('report_helper'));
    $userId=hostsite_get_user_field('indicia_user_id');
    iform_load_helpers(array('report_helper'));
    $reportOptions = array_merge(
      iform_report_get_report_options($args, $auth['read']),
      array(
        'dataSource' => 'library/totals/filterable_species_occurrence_image_counts',
        'autoParamsForm' => false
      ),
      $options
    );
    $reportOptions['extraParams']['ownData'] = 0;
    $reportOptions['extraParams']['currentUser'] = $userId;
    $reportOptions['bands']=array(array('content'=>
        '<div class="totals species">{species_count} species</div>'.
        '<div class="totals species">{occurrences_count} records</div>'.
        '<div class="totals species">{photos_count} photos</div>'));
    $r = self::output_title($options);
    $r .= report_helper::freeform_report($reportOptions);
    return $r;
  }
  
  /**
   * Outputs a block of recent photos for the event.  
   
   * @param array $auth Authorisation tokens.
   * @param array $args Form arguments (the settings on the form edit tab).
   * @param string $tabalias The alias of the tab this is being loaded onto.
   * @param array $options The options passed to this control using @option=value settings in the form structure.
   * Options supported are those which can be passed to the report_helper::report_grid method, for example set @limit
   * to control how many photos to display and set @title=... to include a heading in the output.
   * @param string $path The page reload path, in case it is required for the building of links.
   * @return string HTML to insert into the page for the location map. JavaScript is added to the variables in helper_base.
   *
   * @link http://www.biodiverseit.co.uk/indicia/dev/docs/classes/report_helper.html#method_report_grid API docs for report_helper::report_grid
   */
  public static function photos_block($auth, $args, $tabalias, $options, $path) {
    iform_load_helpers(array('report_helper'));
    $reportOptions = array_merge(      
      iform_report_get_report_options($args, $auth['read']),
      array(
        'dataSource' => 'library/occurrence_images/filterable_explore_list',
        'bands' => array(array('content'=>
          '<div class="gallery-item status-{record_status} certainty-{certainty} ">'.
          '<a class="fancybox" href="{imageFolder}{media}"><img src="{imageFolder}thumb-{media}" title="{taxon}" alt="{taxon}"/><br/>{formatted}</a></div>')),
        'limit' => 10,
        'autoParamsForm' => false
      ),
      $options
    );
    $reportOptions['extraParams']['limit']=$reportOptions['limit'];
    $r = self::output_title($options);
    $r .= report_helper::freeform_report($reportOptions);
    return $r;
  }
  
  /**
   * Outputs a div containing a "cloud" of recorder names, based on the proportion of the recent records
   * recorded by each recorder.  
   
   * @param array $auth Authorisation tokens.
   * @param array $args Form arguments (the settings on the form edit tab).
   * @param string $tabalias The alias of the tab this is being loaded onto.
   * @param array $options The options passed to this control using @option=value settings in the form structure.
   * Options supported are those which can be passed to the report_helper::report_grid method, for example set @limit
   * to control how many recorders to display and set @title=... to include a heading in the output.
   * @param string $path The page reload path, in case it is required for the building of links.
   * @return string HTML to insert into the page for the location map. JavaScript is added to the variables in helper_base.
   *
   * @link http://www.biodiverseit.co.uk/indicia/dev/docs/classes/report_helper.html#method_freeform_report API docs for report_helper::freeform_report
   */
  public static function trending_recorders_cloud($auth, $args, $tabalias, $options, $path) {
    iform_load_helpers(array('report_helper'));
    $reportOptions = array_merge(      
      iform_report_get_report_options($args, $auth['read']),
      array(
        'dataSource' => 'library/users/filterable_trending_people',
        'header' => '<ul class="people cloud">',
        'bands' => array(array('content'=>
          '<li style="font-size: {font_size}px">{recorders}</li>')),
        'footer' => '</ul>',
        'limit' => 15,
        'autoParamsForm' => false
      ),
      $options
    );
    $reportOptions['extraParams']['limit']=$reportOptions['limit'];
    $r = self::output_title($options);
    $r .= report_helper::freeform_report($reportOptions);
    return $r;
  }
  
  /**
   * Outputs a div containing a "cloud" of taxon names, based on the proportion of the recent records
   * recorded for each taxon.  
   
   * @param array $auth Authorisation tokens.
   * @param array $args Form arguments (the settings on the form edit tab).
   * @param string $tabalias The alias of the tab this is being loaded onto.
   * @param array $options The options passed to this control using @option=value settings in the form structure.
   * Options supported are those which can be passed to the report_helper::report_grid method, for example set @limit
   * to control how many taxa to display and set @title=... to include a heading in the output
   * @param string $path The page reload path, in case it is required for the building of links.
   * @return string HTML to insert into the page for the location map. JavaScript is added to the variables in helper_base.
   *
   * @link http://www.biodiverseit.co.uk/indicia/dev/docs/classes/report_helper.html#method_freeform_report API docs for report_helper::freeform_report
   */
  public static function trending_taxa_cloud($auth, $args, $tabalias, $options, $path) {
    iform_load_helpers(array('report_helper'));
    $reportOptions = array_merge(      
      iform_report_get_report_options($args, $auth['read']),
      array(
        'dataSource' => 'library/taxa/filterable_trending_taxa',
        'header' => '<ul class="taxon cloud">',
        'bands' => array(array('content'=>
          '<li style="font-size: {font_size}px">{species}</li>')),
        'footer' => '</ul>',
        'limit' => 15,
        'autoParamsForm' => false
      ),
      $options
    );
    $reportOptions['extraParams']['limit']=$reportOptions['limit'];
    $r = self::output_title($options);
    $r .= report_helper::freeform_report($reportOptions);
    return $r;
  }
  
  /**
   * Outputs a pie chart for the proportion of each taxon group being recorded.  
   *
   * @param array $auth Authorisation tokens.
   * @param array $args Form arguments (the settings on the form edit tab).
   * @param string $tabalias The alias of the tab this is being loaded onto.
   * @param array $options The options passed to this control using @option=value settings in the form structure.
   * Options supported are those which can be passed to the report_helper::report_chart method, for example set @limit
   * to control how many taxa to display, set @width and @height to control the dimensions. Set @title=... to 
   * include a heading in the output
   * @param string $path The page reload path, in case it is required for the building of links.
   * @return string HTML to insert into the page for the location map. JavaScript is added to the variables in helper_base.
   *
   * @link http://www.biodiverseit.co.uk/indicia/dev/docs/classes/report_helper.html#method_report_chart API docs for report_helper::report_chart
   */
  public static function groups_pie($auth, $args, $tabalias, $options, $path) {
    iform_load_helpers(array('report_helper'));
    $reportOptions = array_merge(      
      iform_report_get_report_options($args, $auth['read']),
      array(
        'dataSource' => 'library/taxon_groups/filterable_group_counts',
        'id' => 'groups-pie',
        'width'=> 340,
        'height'=> 340,
        'chartType' => 'pie',
        'yValues'=>array('count'),
        'xValues'=>'taxon_group',
        'rendererOptions' => array(
          'sliceMargin' => 4,
          'showDataLabels' => true,
          'dataLabelThreshold' => 2,
          'dataLabels' => 'label',
          'dataLabelPositionFactor' => 1
        ),
        'autoParamsForm' => false
      ),
      $options
    );
    $r = self::output_title($options);
    $r .= report_helper::report_chart($reportOptions);
    return $r;
  }
  
  public static function species_by_location_league($auth, $args, $tabalias, $options, $path) {
    $label = empty($options['label']) ? 'Location' : $options['label'];
    return self::league_table($auth, $args, $options, 'library/locations/filterable_species_counts_league', $label);
  }
  
  public static function records_by_location_league($auth, $args, $tabalias, $options, $path) {
    $label = empty($options['label']) ? 'Location' : $options['label'];
    return self::league_table($auth, $args, $options, 'library/locations/filterable_record_counts_league', $label);
  }
  
  /**
   * Outputs a league table of the recorders ordered by species (taxon) count  
   *
   * @param array $auth Authorisation tokens.
   * @param array $args Form arguments (the settings on the form edit tab).
   * @param string $tabalias The alias of the tab this is being loaded onto.
   * @param array $options The options passed to this control using @option=value settings in the form structure.
   * Options supported are those which can be passed to the report_helper::get_report_data method. In addition
   * provide a parameter @groupByRecorderName=true to use the recorder's name as a string in the report grouping,
   * rather than basing the report on the logged in user. Set @title=... to include a heading in the output.
   * @param string $path The page reload path, in case it is required for the building of links.
   * @return string HTML to insert into the page for the league table. JavaScript is added to the variables in helper_base.
   */
  public static function species_by_recorders_league($auth, $args, $tabalias, $options, $path) { 
    $label = empty($options['label']) ? 'Recorders' : $options['label'];
    $groupby = isset($options['groupByRecorderName']) && $options['groupByRecorderName'] ? 'recorder_name' : 'users';
    return self::league_table($auth, $args, $options, "library/$groupby/filterable_species_counts_league", $label);  
  }
  
  /**
   * Outputs a league table of the recorders ordered by records count  
   *
   * @param array $auth Authorisation tokens.
   * @param array $args Form arguments (the settings on the form edit tab).
   * @param string $tabalias The alias of the tab this is being loaded onto.
   * @param array $options The options passed to this control using @option=value settings in the form structure.
   * Options supported are those which can be passed to the report_helper::get_report_data method. In addition
   * provide a parameter @groupByRecorderName=true to use the recorder's name as a string in the report grouping,
   * rather than basing the report on the logged in user. Set @title=... to include a heading in the output.
   * @param string $path The page reload path, in case it is required for the building of links.
   * @return string HTML to insert into the page for the league table. JavaScript is added to the variables in helper_base.
   */
  public static function records_by_recorders_league($auth, $args, $tabalias, $options, $path) { 
    $label = empty($options['label']) ? 'Recorders' : $options['label'];
    $groupby = isset($options['groupByRecorderName']) && $options['groupByRecorderName'] ? 'recorder_name' : 'users';
    return self::league_table($auth, $args, $options, "library/$groupby/filterable_record_counts_league", $label);  
  }
  
  private static function league_table($auth, $args, $options, $report, $label) { 
    iform_load_helpers(array('report_helper'));
    $reportOptions = array_merge(
      iform_report_get_report_options($args, $auth['read']),
      array(
        'dataSource' => $report,
        'limit' => 20,
        'autoParamsForm' => false
      ),
      $options
    );
    if (hostsite_get_user_field('training')) 
      $reportOptions['extraParams']['training'] = 'true';
    $reportOptions['extraParams']['limit']=$reportOptions['limit'];
    $rows = report_helper::get_report_data($reportOptions);
    $r = self::output_title($options);
    $r .= "<table class=\"league\"><thead><th>Pos</th><th>$label</th><th>Species</th></thead><tbody>";
    if (count($rows)) {
      $pos = 1;
      $lastVal = $rows[0]['value'];
      foreach ($rows as $idx => $row) {
        if ($row['value']<$lastVal) {
          $pos = $idx+1; // +1 because zero indexed $idx
          $lastVal = $row['value'];
        }
        $r .= "<tr><td>$pos</td><td>{$row[name]}</td><td>{$row[value]}</td></tr>\n";
      }
    } else {
      $r .= '<td colspan="3">' . lang::get('No results yet') . '</td>';
    }
    $r .= '</tbody></table>';
    return $r;    
  }
  
  /**
   * Output a block that shows how many species you'd recorded in this event plus where you are in the league table
   * based on taxon counts. Set @title=... to include a heading in the output.
   */
  public static function species_by_recorders_league_position($auth, $args, $tabalias, $options, $path) { 
    return self::league_table_position($auth, $args, $options, "library/users/filterable_species_counts_league_position", 'species');  
  }
  
  /**
   * Output a block that shows how many species you'd recorded in this event plus where you are in the league table
   * based on record counts. Set @title=... to include a heading in the output.
   */
  public static function records_by_recorders_league_position($auth, $args, $tabalias, $options, $path) { 
    return self::league_table_position($auth, $args, $options, "library/users/filterable_record_counts_league_position", 'records');
  }
  
  private static function league_table_position($auth, $args, $options, $report, $label) {  
    $userId = hostsite_get_user_field('indicia_user_id');
    if (!$userId) 
      return '';
    iform_load_helpers(array('report_helper'));
    $reportOptions = array_merge(
      iform_report_get_report_options($args, $auth['read']),
      array(
        'dataSource' => $report,
        'autoParamsForm' => false
      ),
      $options
    );
    if (hostsite_get_user_field('training')) 
      $reportOptions['extraParams']['training'] = 'true';
    $reportOptions['extraParams']['user_id'] = $userId;
    $rows = report_helper::get_report_data($reportOptions);
    $r = self::output_title($options);
    if (count($rows)) {
      $r .= '<div>';
      $r .= '<div class="totals">'.lang::get('Position {1}', $rows[0]['position']).'</div>';
      $r .= '<div class="totals">'.lang::get('{1} species', $rows[0]['value']).'</div>';
      $r .= '</div>';
    }
    return $r;
  }
  
  /**
   * If the $options define a title, then output an h3 element to display the title.
   * @params array $options Options array passed to the current extension control.
   * @return string Heading HTML or empty string.
   */
  private function output_title($options) {
    return empty($options['title']) ? '' : "<h3>$options[title]</h3>\n";
  }
}