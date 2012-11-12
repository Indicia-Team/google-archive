<?php

function instant_indicia_preprocess_page(&$variables) {
  // Check path to determine widget pages
  $pathargs = explode('/',drupal_get_path_alias($_GET['q']));
  if ($pathargs[0] == 'external') {
     // Use template: page__widgets
     drupal_set_message('iframe');
     $variables['template_file'] = 'page-iframe';
  }
}
