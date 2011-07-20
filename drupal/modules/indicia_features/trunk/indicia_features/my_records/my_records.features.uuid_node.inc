<?php

/**
 * Implementation of hook_uuid_features_default_content().
 */
function my_records_uuid_features_default_content() {
  $nodes = array();

  $nodes[] = array(
  'type' => 'iform',
  'language' => '',
  'uid' => '1',
  'status' => '1',
  'created' => '1311017514',
  'changed' => '1311017695',
  'comment' => '0',
  'promote' => '0',
  'moderate' => '0',
  'sticky' => '0',
  'tnid' => '0',
  'translate' => '0',
  'revision_uid' => '1',
  'title' => 'My Records',
  'body' => '',
  'teaser' => '',
  'log' => '',
  'revision_timestamp' => '1311017695',
  'format' => '1',
  'name' => 'admin',
  'picture' => '',
  'data' => 'a:0:{}',
  'iform' => 'report_grid',
  'needs_review' => 3,
  'params' => array(
    'website_id' => '1',
    'password' => 'password',
    'view_access_control' => '1',
    'report_name' => 'library/occurrences/occurrences_list_for_cms_user',
    'param_presets' => 'date_from=
date_to=
survey_id=
taxon_group_id=
user_id={user_id}',
    'param_defaults' => '',
    'output' => 'default',
    'report_group' => 'report',
    'params_in_map_toolbar' => '0',
    'refresh_timer' => '',
    'load_on_refresh' => '',
    'columns_config' => '[

]',
    'gallery_col_count' => '1',
    'items_per_page' => '20',
    'download_link' => '1',
    'redirect_on_success' => '',
  ),
  'last_comment_timestamp' => '1311017514',
  'last_comment_name' => NULL,
  'comment_count' => '0',
  'taxonomy' => array(),
  'uuid' => '390b070b-b175-11e0-8f78-c5fda54158f2',
  'revision_uuid' => '390d544e-b175-11e0-8f78-c5fda54158f2',
  'date' => '2011-07-18 20:31:54 +0100',
);
  return $nodes;
}
