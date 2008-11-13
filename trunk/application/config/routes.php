<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package  Core
 *
 * Sets the default route to "welcome"
 */
$config['_default'] = 'welcome';
$config['termlist'] = 'termlist/page/1/5';
$config['termlist/edit/([0-9]+)'] = 'termlist/edit/$1/1/5';
$config['taxon_list/edit/([0-9]+)'] = 'taxon_list/edit/$1/1/5';
$config['website'] = 'website/page/1/5';
$config['taxon_group'] = 'taxon_group/page/1/5';
$config['taxon_list'] = 'taxon_list/page/1/5';
