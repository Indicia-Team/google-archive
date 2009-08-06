<?php defined('SYSPATH') or die('No direct script access.');

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
 * @subpackage Models
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL
 * @link 	http://code.google.com/p/indicia/
 */

/**
 * Model class for the Locations table.
 *
 * @package	Core
 * @subpackage Models
 * @link	http://code.google.com/p/indicia/wiki/DataModel
 */
class Location_Model extends ORM_Tree {

  protected $children = "locations";
  protected $has_and_belongs_to_many = array('websites');
  protected $has_many = array('samples', 'location_attribute_values');
  protected $belongs_to = array('created_by'=>'user', 'updated_by'=>'user');

  // Declare that this model has child attributes, and the name of the node in the submission which contains them
  protected $has_attributes=true;
  protected $attrs_submission_name='locAttributes';
  protected $attrs_field_prefix='locAttr';

  protected $search_field='name';

  public function validate(Validation $array, $save = FALSE) {
    $orig_values = $array->as_array();

    // uses PHP trim() to remove whitespace from beginning and end of all fields before validation
    $array->pre_filter('trim');
    $array->add_rules('name', 'required');
    $system = $orig_values['centroid_sref_system'];
    $array->add_rules('centroid_sref', 'required', "sref[$system]");
    $array->add_rules('centroid_sref_system', 'required', 'sref_system');

    // Explicitly add those fields for which we don't do validation
    $extraFields = array(
      'code',
      'parent_id',
      'deleted',
      'centroid_geom',
      'boundary_geom'
    );
    return parent::validate($array, $save, $extraFields);
  }

  /**
   * Override set handler to translate WKT to PostGIS internal spatial data.
   */
  public function __set($key, $value)
  {
    if (substr($key,-5) == '_geom')
    {
      if ($value) {
        $row = $this->db->query("SELECT ST_GeomFromText('$value', ".kohana::config('sref_notations.internal_srid').") AS geom")->current();
        $value = $row->geom;
      }
    }
    parent::__set($key, $value);
  }

  /**
   * Override get handler to translate PostGIS internal spatial data to WKT.
   */
  public function __get($column)
  {
    $value = parent::__get($column);

    if  (substr($column,-5) == '_geom') {
      $row = $this->db->query("SELECT ST_asText('$value') AS wkt")->current();
      $value = $row->wkt;
    }
    return $value;
  }

  /**
   * Override postSubmit to also store the list of location_website links
   */
  protected function postSubmit() {
    try {
      if (!is_null($this->gen_auth_filter))
        $websites = ORM::factory('website')->in('id',$this->gen_auth_filter['values'])->find_all();
      else
        $websites = ORM::factory('website')->find_all();
      foreach ($websites as $website) {
        $locations_website = ORM::factory('locations_website',
          array('location_id' => $this->id, 'website_id' => $website->id));
        if ($locations_website->loaded AND !isset($this->submission['fields']['website_'.$website->id])) {
          $locations_website->delete();
        } else if (!$locations_website->loaded AND isset($this->submission['fields']['website_'.$website->id])) {
          $save_array = array(
                 'id' => $locations_website->object_name
                ,'fields' => array('id' => array('value' => $locations_website->id)
                          ,'location_id' => array('value' => $this->id)
                          ,'website_id' => array('value' => $website->id)
                          )
                ,'fkFields' => array()
                ,'superModels' => array());
          $locations_website->submission = $save_array;
          $locations_website->submit();
        }
      }
      return true;
    } catch (Exception $e) {
      $this->errors['locations_websites']=$e->getMessage();
      kohana::log('error', $e->getMessage());
      return false;
    }
  }

}
