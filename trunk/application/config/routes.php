<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package  Core
 *
 * Sets the default route to "welcome"
 */
$config['_default'] = 'welcome';
$config['termlist'] = 'termlist/page/1/5';
$config['termlist/edit/([0-9]+)'] = 'termlist/edit/$1/1/5';
$config['website'] = 'website/page/1/5';
