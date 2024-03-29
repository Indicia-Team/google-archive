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
 * @package	Core
 * @subpackage Views
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL
 * @link 	http://code.google.com/p/indicia/
 */

$disabled_input=html::initial_value($values, 'metaFields:disabled_input');
$disabled = ($disabled_input=='YES') ? 'disabled="disabled"' : '';

$id = html::initial_value($values, 'location:id');
$parent_id = html::initial_value($values, 'location:parent_id');
$boundary_geom = html::initial_value($values, 'location:boundary_geom');
$centroid_geom = html::initial_value($values, 'location:centroid_geom');
require_once(DOCROOT.'client_helpers/map_helper.php');
require_once(DOCROOT.'client_helpers/data_entry_helper.php');
require_once(DOCROOT.'client_helpers/form_helper.php');
if (isset($_POST))
  data_entry_helper::dump_errors(array('errors'=>$this->model->getAllErrors()));
?>
<script type="text/javascript" src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1"></script>
<script type="text/javascript">

function setAsBoundaryFeature(feature) {
  feature.attributes = {type:"boundary"};
  feature.style.fillOpacity = 0;
  feature.style.strokeColor = "#0000ff";
  feature.style.strokeWidth = 3;
}

jQuery(document).ready(function() {

  mapInitialisationHooks.push(function(mapdiv) {
    
    jQuery('form.cmxform').submit(function() {
      $.each(mapdiv.map.editLayer.features, function(idx, feature) {
        if (feature.attributes.type=="boundary") {
          $('#boundary_geom').val(feature.geometry.toString());
        }
      });

    }); 

<?php if ($boundary_geom) : ?>
    var parser = new OpenLayers.Format.WKT();
    var feature = parser.read('<?php echo $boundary_geom; ?>');
    mapdiv.map.editLayer.addFeatures([feature]);
    setAsBoundaryFeature(feature);
    mapdiv.map.editLayer.redraw();
<?php endif; ?>
    mapdiv.map.editLayer.events.on({'featureadded': function(evt) {
      if (evt.feature.attributes.type!=='clickPoint') {
        var toRemove = [];
        $.each(mapdiv.map.editLayer.features, function(idx, feature) {
          if (feature.attributes.type=="boundary") {
            toRemove.push(feature);
          }
        });
        mapdiv.map.editLayer.removeFeatures(toRemove, {});
        setAsBoundaryFeature(evt.feature);
        mapdiv.map.editLayer.redraw();
      }
    }});
  });
  
  if ($('#location\\:public').attr('checked')) {
    $('#websites').hide();
  }
    $("#location\\:public").change(function() {
    if ($(this).attr('checked')) {
      $('input:checked[name^="joinsTo\\:website"]').attr('checked', false);
      $('#attrs').hide();  
    }
    $('#websites').toggle('slow');
  });
  
});
</script>

<p>
<?php if ($disabled_input==='YES') : ?>
The location is available to all websites so you don't have permission to change it. 
Please contact the warehouse owner to request changes.
<?php else : ?>
This page allows you to specify the details of a location.
<?php endif; ?>
</p>

<form class="cmxform" action="<?php echo url::site().'location/save'; ?>" method="post" id="location-edit">
<div id="details">
<?php echo $metadata; ?>
  <fieldset>
  <legend>Location details</legend>
  <input type="hidden" name="location:id" value="<?php echo html::initial_value($values, 'location:id'); ?>" />
  <?php
  echo data_entry_helper::text_input(array(
    'label' => 'Name',
    'fieldname' => 'location:name',
    'default' => html::initial_value($values, 'location:name'),
    'validation' => 'required',
    'disabled' => $disabled,
  ));
  echo data_entry_helper::text_input(array(
    'label' => 'Code',
    'fieldname' => 'location:code',
    'default' => html::initial_value($values, 'location:code'),
    'disabled' => $disabled,
  ));
  echo data_entry_helper::select(array(
    'label' => 'Type',
    'fieldname' => 'location:location_type_id',
    'default' => html::initial_value($values, 'location:location_type_id'),
    'lookupValues' => $other_data['type_terms'],
    'blankText' => '<Please select>',
    'disabled' => $disabled,
  ));
  echo data_entry_helper::textarea(array(
    'label' => 'Comment',
    'fieldname' => 'location:comment',
    'default' => html::initial_value($values, 'location:comment'),
    'disabled' => $disabled,
  ));
  echo data_entry_helper::sref_and_system(array(
    'label' => 'Spatial Ref',
    'fieldname' => 'location:centroid_sref',
    'geomFieldname' => 'location:centroid_geom',
    'default' => html::initial_value($values, 'location:centroid_sref'),
    'defaultGeom' => html::initial_value($values, 'location:centroid_geom'),
    'systems' => kohana::config('sref_notations.sref_notations'),
    'defaultSystem' => html::initial_value($values, 'location:centroid_sref_system'),
    'class' => 'control-width-3',
    'validation'=>'required',
    'disabled' => $disabled,
  ));
  ?>
  <input type="hidden" name="location:boundary_geom" id="boundary_geom" value="<?php echo $boundary_geom; ?>"/>
  <p class="instruct">Zoom the map in by double-clicking then single click on the location's centre to set the
  spatial reference. The more you zoom in, the more accurate the reference will be.</p>
  <?php
  $readAuth = data_entry_helper::get_read_auth(0-$_SESSION['auth_user']->id, kohana::config('indicia.private_key'));
  echo map_helper::map_panel(array(
    'readAuth' => $readAuth,
    'presetLayers' => array('virtual_earth'),
    'editLayer' => true,
    'layers' => array(),
    'initial_lat'=>52,
    'initial_long'=>-2,
    'initial_zoom'=>7,
    'width'=>870,
    'height'=>400,
    'initialFeatureWkt' => $centroid_geom,
    'standardControls' => ($disabled_input==='YES') ? array('layerSwitcher','panZoom'): array('layerSwitcher','panZoom','drawPolygon','drawLine','modifyFeature'),
  ));
  echo data_entry_helper::autocomplete(array(
    'label' => 'Parent location',
    'fieldname' => 'location:parent_id',
    'table' => 'location',
    'captionField' => 'name',
    'valueField' => 'id',
    'extraParams' => $readAuth,
    'default' => html::initial_value($values, 'location:parent_id'),
    'defaultCaption' => html::initial_value($values, 'parent:name'),
    'disabled' => $disabled,
  ));
      if ($this->auth->logged_in('CoreAdmin')) {
        //Only core admin can create public locations.
        echo data_entry_helper::checkbox(array(
          'label' => 'Available to all websites',
          'fieldname' => 'location:public',
          'default' => html::initial_value($values, 'location:public'),
          'disabled' => $disabled,
        ));
      }

  ?>
  </fieldset>
</div>

<?php 
  if (is_null($id) || $this->auth->logged_in('CoreAdmin') || $values['location:public'] != true) :
  //No need to display for public locations unless core admin. 
?>  
<div id="websites">
  <fieldset>
  <legend>Location Websites</legend>
  <ol>
  <?php
    $websiteIds = $this->get_allowed_website_id_list('editor');
    $linkedWebsites = array();
    if (!is_null($websiteIds))
      $websites = ORM::factory('website')->in('id', $websiteIds)->where('deleted', 'false')->orderby('title','asc')->find_all();
    else
      $websites = ORM::factory('website')->where('deleted', 'false')->orderby('title','asc')->find_all();        
    foreach ($websites as $website) {
      echo '<li><label for="website_'.$website->id.'" class="wide">'.$website->title.'</label>';
      echo '<input type="checkbox" name="joinsTo:website:'.$website->id.'" ';
      if(!is_null($id)){      
        if (array_key_exists('joinsTo:website:'.$website->id, $values)) {
          echo "checked=\"checked\"";
          $linkedWebsites[] = $website->id;
        }
      }
      echo '></li>';
    }  
  ?>
  </ol>
  </fieldset>
</div>
<?php endif; ?>

<?php 
  if (!is_null($id) && $values['location:public'] != "t") : 
  //No need to display for new locations or public locations.
  ?>
<div id="attrs">
  <fieldset>
   <legend>Additional Attributes</legend>
   <ol>
   <?php
  foreach ($values['attributes'] as $attr) {
    $name = 'locAttr:'.$attr['location_attribute_id'];
    // if this is an existing attribute, tag it with the attribute value record id so we can re-save it
    if ($attr['id']) $name .= ':'.$attr['id'];
    switch ($attr['data_type']) {
      case 'D':
      case 'V':
        echo data_entry_helper::date_picker(array(
          'label' => $attr['caption'],
          'fieldname' => $name,
          'default' => $attr['value']
        ));
        break;
      case 'L':
        echo data_entry_helper::date_picker(array(
          'label' => $attr['caption'],
          'fieldname' => $name,
          'default' => $attr['raw_value'],
          'lookupValues' => $values['terms_'.$attr['termlist_id']]
        ));
        break;
      case 'B':
        echo data_entry_helper::checkbox(array(
          'label' => $attr['caption'],
          'fieldname' => $name,
          'default' => $attr['value']
        ));
        break;
      default:
        echo data_entry_helper::text_input(array(
          'label' => $attr['caption'],
          'fieldname' => $name,
          'default' => $attr['value']
        ));
    }
  }
   ?>
   </ol>
   </fieldset>
</div>
<?php 
endif;
echo html::form_buttons(html::initial_value($values, 'location:id')!=null, $disabled_input==='YES');
data_entry_helper::$dumped_resources[] = 'jquery';
data_entry_helper::$dumped_resources[] = 'jquery_ui';
data_entry_helper::$dumped_resources[] = 'fancybox';
data_entry_helper::enable_validation('location-edit');
data_entry_helper::link_default_stylesheet();
echo data_entry_helper::dump_javascript();
?>
</form>
  