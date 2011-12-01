<?php

/**
 * Implementation of hook_default_page_manager_pages().
 */
function mapping_portal_default_page_manager_pages() {
  $page = new stdClass;
  $page->disabled = FALSE; /* Edit this to true to make a default page disabled initially */
  $page->api_version = 1;
  $page->name = 'mapping_portal';
  $page->task = 'page';
  $page->admin_title = 'Mapping Portal';
  $page->admin_description = '<p>
	A page for querying records based on a map.</p>
';
  $page->path = 'mapping_portal';
  $page->access = array(
    'plugins' => array(
      0 => array(
        'name' => 'role',
        'settings' => array(
          'rids' => array(
            0 => 2,
          ),
        ),
        'context' => 'logged-in-user',
        'not' => FALSE,
      ),
    ),
    'logic' => 'and',
  );
  $page->menu = array(
    'type' => 'normal',
    'title' => 'Mapping Portal',
    'name' => 'primary-links',
    'weight' => '0',
    'parent' => array(
      'type' => 'none',
      'title' => '',
      'name' => 'navigation',
      'weight' => '0',
    ),
  );
  $page->arguments = array();
  $page->conf = array();
  $page->default_handlers = array();
  $handler = new stdClass;
  $handler->disabled = FALSE; /* Edit this to true to make a default handler disabled initially */
  $handler->api_version = 1;
  $handler->name = 'page_mapping_portal_panel_context';
  $handler->task = 'page';
  $handler->subtask = 'mapping_portal';
  $handler->handler = 'panel_context';
  $handler->weight = 0;
  $handler->conf = array(
    'title' => 'Panel',
    'no_blocks' => 0,
    'pipeline' => 'standard',
    'css_id' => '',
    'css' => 'table.report-grid {
  font-size: 90%;
}',
    'contexts' => array(),
    'relationships' => array(),
  );
  $display = new panels_display;
  $display->layout = 'flexible';
  $display->layout_settings = array();
  $display->panel_settings = array(
    'style_settings' => array(
      'default' => NULL,
      'center' => NULL,
    ),
  );
  $display->cache = array();
  $display->title = '';
  $display->content = array();
  $display->panels = array();
    $pane = new stdClass;
    $pane->pid = 'new-1';
    $pane->panel = 'center';
    $pane->type = 'custom';
    $pane->subtype = 'custom';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'admin_title' => 'params panel',
      'title' => '',
      'body' => '<?php
  iform_load_helpers(array(\'report_helper\'));
  // collapse the params panel
  drupal_add_js(\'misc/collapse.js\');
  // share read auth across panels
  global $readAuth;
  $readAuth = report_helper::get_read_auth(variable_get(\'indicia_website_id\',\'\'), variable_get(\'indicia_password\',\'\'));
  echo report_helper::report_grid(array(
    \'id\'=>\'params\',
    \'readAuth\' => $readAuth,
    \'dataSource\'=>\'library/occurrences/occurrence_list_for_boundary\',
    \'includeAllColumns\' => false,
    \'headers\' => false,
    \'galleryColCount\' => 4,
    \'fieldsetClass\' => \'collapsible\',
    \'extraParams\' => array(
      \'survey_id\'=>\'\',
      \'date_from\'=>\'\',
      \'date_to\'=>\'\',
      \'sref_max_size\'=>1000,
      \'smpattrs\'=>\'\',
      \'occattrs\'=>\'3,4,5\'
    ),
    \'paramsOnly\'=>true,
    \'reportGroup\'=>\'mapPortal\'
  ));
  report_helper::add_resource(\'defaultStylesheet\');
?>',
      'format' => '3',
      'substitute' => TRUE,
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 0;
    $display->content['new-1'] = $pane;
    $display->panels['center'][0] = 'new-1';
    $pane = new stdClass;
    $pane->pid = 'new-2';
    $pane->panel = 'center';
    $pane->type = 'custom';
    $pane->subtype = 'custom';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'admin_title' => 'map panel',
      'title' => '',
      'body' => '<?php
  iform_load_helpers(array(\'map_helper\'));
  global $readAuth;

  echo report_helper::report_map(array(
    \'id\'=>\'latest-photos\',
    \'readAuth\' => $readAuth,
    \'dataSource\'=>\'library/occurrences/occurrence_list_for_boundary\',
    \'includeAllColumns\' => false,
    \'headers\' => false,
    \'galleryColCount\' => 4,
    \'extraParams\' => array(
      \'survey_id\'=>\'\',
      \'date_from\'=>\'\',
      \'date_to\'=>\'\',
      \'sref_max_size\'=>1000,
      \'smpattrs\'=>\'\',
      \'occattrs\'=>\'\'
    ),
    \'autoParamsForm\'=>false,
    \'reportGroup\'=>\'mapPortal\',
    \'clickableLayersOutputMode\'=>\'report\'
  ));
  $options = $options = array(
    \'readAuth\' => $readAuth,
    \'presetLayers\' => array(\'google_physical\', \'google_satellite\', \'google_hybrid\', \'osm\', \'osm_th\'),
    \'editLayer\' => true,
    \'layers\' => array(),
    \'initial_lat\'=>variable_get(\'indicia_map_centroid_lat\', 55),
    \'initial_long\'=>variable_get(\'indicia_map_centroid_long\', -1),
    \'initial_zoom\'=>variable_get(\'indicia_map_zoom\', 5),
    \'width\'=>\'60%\',
    \'height\'=>400,
    \'standardControls\'=>array(\'layerSwitcher\',\'panZoomBar\'),
    \'rememberPos\'=>true,
    \'clickForSpatialRef\'=>false
  );
  echo map_helper::map_panel($options);
?>',
      'format' => '3',
      'substitute' => TRUE,
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 1;
    $display->content['new-2'] = $pane;
    $display->panels['center'][1] = 'new-2';
    $pane = new stdClass;
    $pane->pid = 'new-3';
    $pane->panel = 'center';
    $pane->type = 'custom';
    $pane->subtype = 'custom';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'admin_title' => 'dump_javascript',
      'title' => '',
      'body' => '<?php
  handle_resources();
?>',
      'format' => '3',
      'substitute' => TRUE,
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 2;
    $display->content['new-3'] = $pane;
    $display->panels['center'][2] = 'new-3';
    $pane = new stdClass;
    $pane->pid = 'new-4';
    $pane->panel = 'center';
    $pane->type = 'custom';
    $pane->subtype = 'custom';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'admin_title' => '',
      'title' => '',
      'body' => '<?php
  global $readAuth;
  ?>
  <fieldset class="collapsible collapsed">
  <legend>Records</legend>
  <?php
  echo report_helper::report_grid(array(
    \'id\'=>\'report-records\',
    \'readAuth\' => $readAuth,
    \'dataSource\'=>\'library/occurrences/occurrence_list_for_boundary\',
    \'extraParams\' => array(
      \'survey_id\'=>\'\',
      \'date_from\'=>\'\',
      \'date_to\'=>\'\',
      \'sref_max_size\'=>1000,
      \'smpattrs\'=>\'\',
      \'occattrs\'=>\'3,4,5\'
    ),
    \'autoParamsForm\'=>false,
    \'reportGroup\'=>\'mapPortal\'
  ));
  report_helper::add_resource(\'defaultStylesheet\');
  echo report_helper::dump_javascript();
?>
</fieldset>',
      'format' => '3',
      'substitute' => TRUE,
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 3;
    $display->content['new-4'] = $pane;
    $display->panels['center'][3] = 'new-4';
  $display->hide_title = PANELS_TITLE_FIXED;
  $display->title_pane = 'new-1';
  $handler->conf['display'] = $display;
  $page->default_handlers[$handler->name] = $handler;
  $pages['mapping_portal'] = $page;

 return $pages;

}
