<?php
/**
 * INDICIA
 *
 * Contains system information
 *
 * @link http://code.google.com/p/indicia/
 *
 * @package Indicia
 * @license http://www.gnu.org/licenses/gpl.html GPL
 */
$config['system'] = array
(
    'version'      => '0.1',
    'name'         => '',
    'repository'   => 'http://indicia.googlecode.com/svn/tag/version_0_1',
    'release_date' => '2009-01-15'
);

$config['private_key'] = 'Indicia'; // Change this to a unique value for each Indicia install
$config['nonce_life'] = 1200;		// life span of an authentication token for services, in seconds
$config['maxUploadSize'] = '1M'; // Maximum size of an upload
?>
