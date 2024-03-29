<?php

/**
 * Implementation of hook_uuid_features_default_content().
 */
function my_images_uuid_features_default_content() {
  $nodes = array();

  $nodes[] = array(
  'type' => 'iform',
  'language' => '',
  'uid' => '1',
  'status' => '1',
  'comment' => '0',
  'promote' => '0',
  'moderate' => '0',
  'sticky' => '0',
  'tnid' => '0',
  'translate' => '0',
  'title' => 'My Images',
  'body' => '',
  'teaser' => '',
  'log' => '',
  'format' => '1',
  'name' => 'admin',
  'picture' => '',
  'data' => 'a:0:{}',
  'iform' => 'report_grid',
  'needs_review' => 3,
  'params' => array(
    'view_access_control' => '1',
    'permission_name' => 'online recording',
    'report_name' => 'library/occurrence_images/occurrence_image_list_for_cms_user',
    'param_presets' => 'survey_id=
user_id={user_id}
occattrs=
smpattrs=',
    'param_defaults' => 'date_from=
date_to=
taxon_group_id=',
    'output' => 'default',
    'report_group' => 'report',
    'params_in_map_toolbar' => '0',
    'refresh_timer' => '',
    'load_on_refresh' => '',
    'columns_config' => '[
  {
    "fieldname":"testing",
    "template":"<div class=\'gallery-img\'><a href=\'{imageFolder}{path}\' class=\'fancybox\'><img src=\'{imageFolder}thumb-{path}\'/><br/>\\n<em>{taxon}</em><br/>\\n{common}</a></div>"
  },
  {
    "fieldname":"path",
    "visible":false
  },
  {
    "fieldname":"caption",
    "visible":false
  },
  {
    "fieldname":"taxon",
    "visible":false
  },
  {
    "fieldname":"common",
    "visible":false
  },
  {
    "fieldname":"taxon_group",
    "visible":false
  },
  {
    "fieldname":"occurrence_id",
    "visible":false
  },
  {
    "fieldname":"entered_sref",
    "visible":false
  },
  {
    "fieldname":"date",
    "visible":false
  },
  {
    "fieldname":"zero_abundance",
    "visible":false
  }
]',
    'gallery_col_count' => '5',
    'items_per_page' => '20',
    'download_link' => '1',
    'footer' => '',
    'redirect_on_success' => '',
    'message_after_save' => '0',
  ),
  'path' => 'my-images',
  'taxonomy' => array(),
  'uuid' => '622f1ca5-bc25-11e0-a701-430b6d02c0c0'
);
  return $nodes;
}
