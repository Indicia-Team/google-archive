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
 * @package	Data Cleaner
 * @subpackage Plugins
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL
 * @link 	http://code.google.com/p/indicia/
 */

/**
 * Hook into the data cleaner to declare checks for the test of a species against a list of allowed locations. 
 * Allows for 2 levels of sample hierarchy and 2 levels of location hierarchy.
 * @return type array of rules.
 */
function data_cleaner_species_location_data_cleaner_rules() {
  return array(
    'testType' => 'SpeciesLocation',
    'optional' => array('Metadata'=>array('Tvk','Taxon','TaxonMeaningId')),
    'required' => array('Metadata'=>array('Locations','SurveyId')),    
    'queries' => array(
      array(
        'joins' => 
            "join verification_rule_metadata vrm on (upper(vrm.value)=upper(co.taxa_taxon_list_external_key) and upper(vrm.key)='TVK') 
            or (upper(vrm.value)=upper(co.preferred_taxon) and upper(vrm.key)='TAXON') 
            or (vrm.value=cast(co.taxon_meaning_id as character varying) and vrm.key='TaxonMeaningId') 
            join verification_rules vr on vr.id=vrm.verification_rule_id and vr.test_type='SpeciesLocation' 
            join verification_rule_metadata vrml on vrml.verification_rule_id = vr.id and vrml.deleted=false and upper(vrml.key)='LOCATIONNAMES' 
            join samples s on s.id=co.sample_id and s.deleted=false 
            left join samples sp on sp.id=s.parent_id and sp.deleted=false
            join locations l on l.id in (s.location_id, sp.location_id) and l.deleted=false
            left join locations lp on lp.id=l.parent_id and lp.deleted=false
            join verification_rule_metadata vrsurvey on vrsurvey.verification_rule_id=vr.id and vrsurvey.key='SurveyId' 
                and vrsurvey.value=cast(co.survey_id as character varying) and vrsurvey.deleted=false",
        'where' =>
            "not (array[upper(lp.name)] <@ string_to_array(upper(vrml.value), ',') or 
                array[upper(l.name)] <@ string_to_array(upper(vrml.value), ','))"
      )
    )
  );
}

?>