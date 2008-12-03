<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package  Core
 *
 * Sets the default route to "welcome"
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

