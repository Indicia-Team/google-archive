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
 * @subpackage Config
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL
 * @link 	http://code.google.com/p/indicia/
 */

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Sets the default route to the home page
 */
$config['_default'] = 'home';

// Termlist
$config['termlist'] = 'termlist/page/1/10';
$config['termlist/edit/([0-9]+)'] = 'termlist/edit/$1/1/10';
// Taxon list
$config['taxon_list'] = 'taxon_list/page/1/10';
$config['taxon_list/edit/([0-9]+)'] = 'taxon_list/edit/$1/1/10';
// Website
$config['website'] = 'website/page/1/10';
// Survey
$config['survey'] = 'survey/page/1/10';
// Taxon Group
$config['taxon_group'] = 'taxon_group/page/1/10';
// Language
$config['language'] = 'language/page/1/10';
// Location
$config['location'] = 'location/page/1/10';
// Termlists_term
$config['termlists_term'] = 'termlists_term/page/1/1/10';
$config['termlists_term/([0-9]+)'] = 'termlists_term/page/$1/1/10';
$config['termlists_term/page/([0-9]+)'] = 'termlists_term/page/$1/1/10';
$config['termlists_term/edit/([0-9]+)'] = 'termlists_term/edit/$1/1/10';
// Person
$config['person'] = 'person/page/1/10';
// User
$config['user'] = 'user/page/1/10';
// Taxa_taxon_list
$config['taxa_taxon_list'] = 'taxa_taxon_list/page/1/1/10';
$config['taxa_taxon_list/([0-9]+)'] = 'taxa_taxon_list/page/$1/1/10';
$config['taxa_taxon_list/page/([0-9]+)'] = 'taxa_taxon_list/page/$1/1/10';
$config['taxa_taxon_list/edit/([0-9]+)'] = 'taxa_taxon_list/edit/$1/1/10';
// Custom Attribute
$config['occurrence_attribute'] = 'occurrence_attribute/page/1/10';
$config['sample_attribute'] = 'sample_attribute/page/1/10';
$config['location_attribute'] = 'location_attribute/page/1/10';
// Entered Data
$config['occurrence'] = 'occurrence/page/1/10';
$config['occurrence/edit/([0-9]+)'] = 'occurrence/edit/$1/1/10';
$config['sample'] = 'sample/page/1/10';
$config['sample/edit/([0-9]+)'] = 'sample/edit/$1/1/10';
// Title
$config['title'] = 'title/page/1/10';

// Reports
$config['report'] = 'report_viewer';
$config['report/local/(.+)'] = 'report_viewer/local/$1';
$config['report/resume/([a-z0-9]+)'] = 'report_viewer/resume/$1';
