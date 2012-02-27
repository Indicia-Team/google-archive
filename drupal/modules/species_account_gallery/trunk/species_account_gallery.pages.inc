<?php
// $Id$

function sag_species_gallery($tid=false, $hide=false) {
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
    drupal_add_js(array('galleryAjaxPath'=>url('species_gallery/ajax')), 'setting');
  }
  drupal_add_css(drupal_get_path('module', 'species_account_gallery').'/stylesheets/species-account-gallery.css');
  $output = '';
  if (!$hide) {
    $output = '<div id="species-account-gallery">';
    if ($tid) {
      // If outputting the contents of a term level, and if there is a higher taxon with a node at the current top level, 
      // we want to output it's title and body, as long as we are outputting the first level not an AJAX call for children.
      $sql = 'SELECT n.nid, n.title, nr.body '.
          'FROM {term_node} tn '.          
          'INNER JOIN {node} n on n.nid=tn.nid AND n.type=\'higher_taxon\''.
          'INNER JOIN {node_revisions} nr ON nr.nid=n.nid '.
          'WHERE tn.tid=%d';  
      if ($n = db_fetch_object(db_query($sql, $tid))) {
        if (!empty($n->body)) 
          $output .= '<fieldset class="collapsible collapsed taxon-info"><legend class="info">i</legend>'.$n->body.'</fieldset>';
        drupal_set_title($n->title);
      }
    } else {
      // no parent term, so outputting the very top level. Output the header.
      $output .= variable_get('sag_header','');
    }
    drupal_add_js('misc/collapse.js');
  }
  $output = $output.theme('gallery_tree', gallery_tree($tree), $hide);
  if (!$hide)
    $output .= '</div>';
  else {
    // reattach collapsible behaviour to ajax content
    $output .= "<script type=\"text/javascript\">\n";
    $output .= "<!--//--><![CDATA[//><!--\n";
    $output .= "Drupal.attachBehaviors($('#species-account-gallery'));\n";
    $output .= "//--><!]]>\n";
    $output .= "</script>\n";
  }
  return $output;
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
    if (isset($parents_index[$term->tid])) {
      $children = taxonomy_get_nested_tree($parents_index[$term->tid], $max_depth, $term->tid, $parents_index, $depth + 1);
      if (is_null($max_depth) || $depth < $max_depth)
        $term->children = $children;
      $term->has_children = count($children)>0;
    }
  }
  return $return;
}

function sag_tree_ajax() {
  if (!isset($_GET['tid'])) {
    echo 'Error: no term ID provided';
  } else {
    echo sag_species_gallery($_GET['tid'], true);
  }
    
}