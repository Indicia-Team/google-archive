<?php

$lang = array
(
    'title'       => 'Indicia Setup',
    'description' => 'Before you start, please create a PostGIS database using the template, for example using the script: <br />' .
        'CREATE DATABASE indicia TEMPLATE=template_postgis;<br />'.
        'Then, create a user login with the required access to this database. The schema you define below will be auto-created by the '.
        'setup procedure. For more information, see <a href="http://code.google.com/p/indicia/wiki/Installation">the Installation Guide</a>.',
    'database'    => 'Database',
    'db_schema'   => 'Schema for Indicia tables',
    'db_host'     => 'Host',
    'db_port'     => 'Port',
    'db_name'     => 'Database Name',
    'db_user'     => 'User',
    'db_password' => 'Password',
    'db_grant'    => 'Grant permission to additional users (comma separated)',
    'indicia_administrator'   => 'Create Indicia administrator',
    'indicia_login'           => 'Login',
    'indicia_password'        => 'Password',
    'start_setup_title'       => 'Launch setup',
    'start_setup_button'      => 'submit',
    'warning'                 => 'Warning!!!',
    'error'                   => 'Error',
    'error_config_folder'     => 'The config folder must be writeable by php scripts:',
    'error_upload_folder'     => 'The upload folder must be writeable by php scripts:',
    'error_db_wrong_postgres_version1'  => 'Installed postgres version:',
    'error_db_wrong_postgres_version2'  => 'At least version 8.2 required.',
    'error_db_unknown_postgres_version' => 'Unknown postgres version.',
    'error_db_wrong_schema'   => 'A schema must be defined and it must be named something other than "public"',
    'error_db_schema'         => 'Schema connection problem. Verify the schema name.',
    'error_db_postgis'        => 'It seems that postgis scripts arent installed in the public schema.',
    'error_db_file'           => 'The indicia setup sql file must be readable by php scripts:',
    'error_db_user'           => 'The following users dosent exists:',
    'error_db_connect'        => 'Could not connect to the database. Please verify database connection data.',
    'error_db_setup'          => 'Setup failed. Database transactions have been rolled back.',
    'error_db_database_config' => 'Setup failed. Database transactions have been rolled back. Could not write /application/config/database.php file. Please check file write permission.',
    'error_db_indicia_config'  => 'Setup failed. Database transactions have been rolled back. Could not create /application/config/indicia.php file. Please check file write permission.',
    'error_remove_folder'      => 'For continuing with the setup you have to remove or rename the config file /application/config/indicia.php',
    'error_file_read_permission'      => 'The following files must be readable by php scripts',
    'error_chars_not_allowed'         => 'wrong chars',
    'error_no_postgis'                => 'Postgis not installed',
    'error_wrong_postgis_version'     => 'Required Postgis version >= 1.3',
    'error_wrong_postgres_version'     => 'Required Postgresql version >= 8.2',
    'error_no_postgres_client_extension' => 'No php_pgsql extension found (postgresql). Check your php.ini file.',
    'error_no_php_curl_extension'        => 'No php_curl extension found. Check your php.ini file.',
    'error_upgrade_for_end_user'   => 'Fatal upgrade error. The system is in an unstable state. Please consult the error log file in /application/logs/. For further support, please refer to the <a href="http://forums.nbn.org.uk/viewforum.php?id=19">Indicia Forum</a>.',
    'error_downgrade_not_possible' => 'Current indicia script version is lower than the database version. Downgrade not possible.'
);

?>
