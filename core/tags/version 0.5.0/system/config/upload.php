<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Core
 *
 * This path is relative to your index file. Absolute paths are also supported.
 */
$config['directory'] = DOCROOT.'upload';

/*
 * This path is relative to your index file. Absolute paths are also supported.
 */
$config['zip_extract_directory'] = DOCROOT.'extract';

/**
 * Enable or disable directory creation.
 */
$config['create_directories'] = FALSE;

/**
 * Remove spaces from uploaded filenames.
 */
$config['remove_spaces'] = TRUE;