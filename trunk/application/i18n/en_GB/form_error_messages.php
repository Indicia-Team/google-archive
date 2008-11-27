<?php defined('SYSPATH') or die('No direct access allowed.');

$lang = array (
	'title' => Array (
		'required' => 'The title cannot be blank.',
		'standard_text' => 'Only standard characters are allowed.',
		'length' => 'The title must be between 1 and 100 letters.',
		'default' => 'Invalid Input.',
	),
	'description' => Array (
		'standard_text' => 'Only standard characters are allowed.',
		'default' => 'Invalid Input.',
	),
	'deleted' => Array (
		'has_terms' => 'There are terms belonging to this list.',
		'has_taxa' => 'There are species belonging to this list.',
	),
	'iso' => Array (
		'default' => 'Invalid ISO 639-2 language code.',
	),
	'website_id' => Array (
		'required' => 'The website cannot be blank.',
	),
	'surname' => Array (
		'required' => 'The surname cannot be blank.',
		'length' => 'The surname must be between 1 and 30 letters.',
		'default' => 'Invalid Input.',
	),
	'email_address' => Array (
		'required' => 'The email address cannot be blank.',
		'email' => 'This must be a valid email address.',
		'default' => 'Invalid Input.',
	),
	'url' => Array (
		'required' => 'The website URL cannot be blank.',
		'url' => 'This must be a valid URL including the http:// prefix.',
		'default' => 'Invalid Input.',
	),
	'taxon_id' => array (
		'default' => 'Unable to create a valid taxon entry',
	),

);

?>
