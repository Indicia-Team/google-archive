<?php

/**
 * Implementation of hook_uuid_features_default_content().
 */
function indicia_welcome_uuid_features_default_content() {
  $nodes = array();

  $nodes[] = array(
  'type' => 'page',
  'language' => '',
  'uid' => '1',
  'status' => '1',
  'created' => '1310571890',
  'changed' => '1310571974',
  'comment' => '0',
  'promote' => '0',
  'moderate' => '0',
  'sticky' => '0',
  'tnid' => '0',
  'translate' => '0',
  'revision_uid' => '1',
  'title' => 'Welcome to Instant Indicia',
  'body' => 'Welcome to your new Instant Indicia website. This website is ready for you to setup online recording. This home page is a temporary page '.
      "designed to help you get started and can be replaced when your site is ready.\n".
      'Instant Indicia lets you choose from a number of ready made <em>features</em> to enable on your site, ranging from simple reports or data input forms to '.
      'complete site modules.'.
      '<ul><li><a href="admin/build/features">Select which features you want on your website.</a></li>'.
      '<li><a href="admin/settings/iform/settings">Change Indicia configuration settings for your website.</a></li>'.
      '<li><a href="admin/settings/iform/diagnostics">Perform some diagnostic tests to ensure your connection to the Warehouse works.</a></li></ul>',
  'teaser' => 'Welcome to your new Instant Indicia website. This website is ready for you to setup online recording.',
  'log' => '',
  'revision_timestamp' => '1310571974',
  'format' => '1',
  'name' => 'John van Breda',
  'picture' => '',
  'data' => 'a:0:{}',
  'path' => 'instant-indicia-welcome',
  'last_comment_timestamp' => '1310571890',
  'last_comment_name' => NULL,
  'comment_count' => '0',
  'taxonomy' => array(),
  'uuid' => '0b259f66-ad67-11e0-a0cc-f9de6c845310',
  'revision_uuid' => '0b25a948-ad67-11e0-a0cc-f9de6c845310',
  'pathauto_perform_alias' => FALSE,
  'date' => '2011-07-13 15:44:50 +0000',
);
  return $nodes;
}
