<?php
// $Id$

function sag_species_gallery($tid=false) {
  $vid = sag_get_vid();
  $load_on_demand = variable_get('sag_load_on_demand', false);
  if (!$vid) {
    return t('Before viewing the gallery, the site administrator must configure the species account gallery module');
  }
  $maxDepth = $load_on_demand ? 1 : null; 
  $tree = taxonomy_get_nested_tree($vid, $maxDepth, $tid);
  $gallery = variable_get('sag_gallery_mode', 'fancybox');
  if ($gallery==='fancybox') {
    drupal_add_js(drupal_get_path('module', 'iform').'/media/js/fancybox/jquery.fancybox.pack.js');
    drupal_add_css(drupal_get_path('module', 'iform').'/media/js/fancybox/jquery.fancybox.css');
    drupal_add_js('$(document).ready(function() {$("a.fancybox").fancybox();});','inline');
  }
  if (variable_get('sag_load_mode', 'full')==='ajax') {
    // add JS for tree loading on the fly
    drupal_add_js(drupal_get_path('module', 'species_account_gallery').'/ajaxTree.js');
  }
  drupal_add_css(drupal_get_path('module', 'species_account_gallery').'/stylesheets/species-account-gallery.css');
  return theme('gallery_tree', gallery_tree($tree));
}

function taxonomy_get_nested_tree($terms = array(), $max_depth = NULL, $parent = 0, $parents_index = array(), $depth = 0) {
  if (is_int($terms)) {
    $terms = taxonomy_get_tree($terms, $parent);
  }
  $return = array();
  foreach($terms as $term) {
    foreach($term->parents as $term_parent) {
      if ($term_parent == $parent) {
        $return[$term->tid] = $term;
      }
      else {
        $parents_index[$term_parent][$term->tid] = $term;
      }
    }
  }
  foreach($return as &$term) {
    if (isset($parents_index[$term->tid]) && (is_null($max_depth) || $depth < $max_depth)) {
      $term->children = taxonomy_get_nested_tree($parents_index[$term->tid], $max_depth, $term->tid, $parents_index, $depth + 1);
    }
  }
  return $return;
}

function sag_tree_ajax() {
  if (!isset($_GET['tid'])) {
    echo 'Error: no term ID provided';
  } else {
    echo sag_species_gallery($_GET['tid']);
  }
    
}