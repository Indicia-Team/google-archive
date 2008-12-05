<?php

$lang = array
(
	'title'       => 'Indicia Setup',
	'description' => 'Before continue here you must first create a postgresql database and may a schema.',
	'database'    => 'Database',
	'db_schema'   => 'Schema',
	'db_host'     => 'Host',
	'db_port'     => 'Port',
	'db_name'     => 'Name',
	'db_user'     => 'User',
	'db_password' => 'Password',
	'indicia_administrator'   => 'Create Indicia administrator',
	'indicia_login'           => 'Login',
	'indicia_password'        => 'Password',
	'start_setup_title'       => 'Launch setup',
	'start_setup_button'      => 'submit',
	'warning'                 => 'Warning!!!',
	'error'                   => 'Error',
	'error_config_folder'     => 'The config folder must be writeable by php scripts:',
	'error_upload_folder'     => 'The upload folder must be writeable by php scripts:',
	'error_db_file'           => 'The indicia setup sql file must be readable by php scripts:',
	'error_db_connect'        => 'Could not connect to the database. Please verify database connection data.',
	'error_db_setup'          => 'Setup failed. Rollback database transactions.',
	'error_db_database_config' => 'Setup failed. Rollback database transactions. Could not write /application/config/database.php file. Please check file write permission.',
	'error_db_indicia_config'  => 'Setup failed. Rollback database transactions. Could not create /application/config/indicia.php file. Please check file write permission.',
	'error_remove_folder'      => 'For continuing with the setup you have to remove or rename the config file /application/config/indicia.php',
	'error_file_read_permission'      => 'The following files must be readable by php scripts',
	'error_chars_not_allowed'         => 'wrong chars',
	'error_no_postgis'                => 'Postgis not installed',
	'error_wrong_postgis_version'     => 'Required Postgis version >= 1.3',
	'error_wrong_postgres_version'     => 'Required Postgresql version >= 8.2',
	'error_no_postgres_client_extension' => 'No PHP Postgresql extension found',
);

?>
