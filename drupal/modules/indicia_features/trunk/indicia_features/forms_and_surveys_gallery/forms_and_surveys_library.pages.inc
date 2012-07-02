<?php

function library_view() {
  // Search box, filters the following ul element contains to forms containing the name or in the form's description. 
  // Expands the appropriate fieldsets.
  // 
  // Output unordered list of sections contained in collapsed fieldsets
  // My local forms and surveys (dorset) contains a list of forms designed for your preferred locality. Omitted if no locality in user profile
  // Other local forms and surveys contains a list of forms designed for locations but not your preferred. 
  //   Renamed to Local forms and surveys if no locality in profile
  // My species group forms and surveys (if any in profile)
  // Other species group forms (or just Species group forms and surveys)
  drupal_add_css(drupal_get_path('module', 'forms_and_surveys_library').'/forms_and_surveys_library-styles.css', 'module');
  $r = '<label>Search:<input type="text" id="library-search"/></label><br/>';
  global $user;
  profile_load_profile($user);
  $selected = empty($user->profile_favourite_library_forms) ? array() : unserialize($user->profile_favourite_library_forms);
  $checked = count($selected)>0 ? ' checked="checked"' : '';
  $r .= "<label>Show only my favourite forms: <input type=\"checkbox\" id=\"only-favourites\"$checked/> </label>\n";
  $r .= '<ul>';
  global $user;
  profile_load_profile($user);
  // find all the library form details
  $result = db_query("SELECT n.nid, l.library_title, l.library_description, l.location_ids, l.taxon_group_ids FROM {node} n
INNER JOIN {forms_and_surveys_library} l on l.nid=n.nid");
  // create arrays to hold the different sections of forms we will display
  $forms = array();
  // loop through the forms, work out a weighting score
  while ($row=db_fetch_object($result)) {
    $formLocations = unserialize($row->location_ids);
    $formTaxonGroups = unserialize($row->taxon_group_ids);
    // @todo Only add to the list of species forms or location forms if the form does not clash. In fact, why not
    // create a score for each form based on the total number of hits (location/species groups). Score zero
    // if match one but specifies the other and does not match. Score negative if no matches at all. Then can
    // display all forms that are > 0 in order.
    // scoring - - 1000 if from a different location, + 3 if any location overlap, + 1 for each taxon group overlap
    $score = 0;
    if (!empty($user->profile_location) && !empty($formLocations)) {
      if (in_array($user->profile_location, $formLocations)) {
        // a local form - that's a good thing
        $score += 3;
        $row->matchLocation=true;
      } else
        // form from somewhere else, so not really of interest
        $score -= 1000;
    }
    if (!empty($user->profile_taxon_groups) && !empty($formTaxonGroups)) {
      $userTaxonGroups = unserialize($user->profile_taxon_groups);
      if (array_intersect($userTaxonGroups, $formTaxonGroups)) {
        // a local form - that's a good thing
        $score += count(array_intersect($userTaxonGroups, $formTaxonGroups));
        $row->matchTaxonGroups=true;
      }
      else
        // form from somewhere else, so not really of interest
        $score -= 100;
    }
    $row->score = $score;
    $forms[] = $row;
  }
  uasort($forms, 'library_compare_rows');
  $r .= library_output_form_list($forms, $selected);
  drupal_add_js(drupal_get_path('module', 'forms_and_surveys_library').'/forms_and_surveys_library.js');
  drupal_add_js('indiciaData={"ajaxUrl":"'.url('forms_and_surveys_library/set_favourite').'",
  "addCaption": "'.t('Add to favourites').'",
  "removeCaption": "'.t('Remove from favourites').'",
};
', 'inline');
  return $r;
}

/** 
 * Sort function for the form scores. 
 */
function library_compare_rows($a, $b) {
  if ($a->score == $b->score) {
    return 0;
  }
  return ($a->score > $b->score) ? -1 : 1;
}

function library_output_form_list($forms, $selected) {
  $r = '';
  if (!empty($forms)) {
    $r .= '<ul id="library-form-list" class="gallery">';
    foreach($forms as $form) {
      $classes=array();
      if (count($selected)>0 && !in_array($form->nid, $selected))
        $classes[] = 'hidden';
      if (in_array($form->nid, $selected)) {
        $classes[] = 'selected';
      }
      $class = (count($classes)) ? ' class="'.implode(' ', $classes).'"' : '';
      $r .= '<li id="'.str_replace(' ', '_', strtolower($form->library_title)).'"'.$class.'>';
      $r .= '<h3>'.l(t($form->library_title), 'node/'.$form->nid).'</h3>';
      $r .= $form->library_description;
      $buttonCaption = (in_array($form->nid, $selected)) ? 'Remove from favourites' : 'Add to favourites'; 
      $r .= '<button type="button" class="button-active" id="btn-'.$form->nid.'">'.t($buttonCaption).'</button>';
      $r .= '</li>';
    }
    $r .= '</ul>';
    
  }
  return $r;
}
