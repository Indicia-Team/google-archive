<?php

class Indicia
{
    public function init()
    {
        set_error_handler(array('Indicia', 'indicia_exception_handler'));
    }

    /**
     * Convert PHP errors to exceptions so that they can be handled nicely.
     */
    public static function indicia_exception_handler($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * set schema search path for postgresql
     */
    public static function set_schema_search_path()
    {
        $db = Database::instance();

        $_schema = Kohana::config('database.default.schema');

        if(!empty($_schema))
        {
            $result = $db->query('SET search_path TO ' . $_schema . ', public, pg_catalog');
        }
    }

    /**
     * check if setup wasnt done
     */
    public static function _check_setup()
    {
        $uri = URI::instance();

        if($uri->segment(1) == 'setup')
        {
            return;
        }

        // load indicia system information
        //
        $system = Kohana::config('indicia.system', false, false);

        // check if the general system setup was done
        // The setup wasnt done if the indicia.php config file dosent exists.
        //
        if($system === null)
        {
            unset($_COOKIE);
            url::redirect('setup');
        }
    }

}

Event::add('system.routing', array('Indicia', '_check_setup'));
Event::add('system.ready',   array('Indicia', 'Init'));
Event::add('system.routing', array('Indicia', 'set_schema_search_path'));


?>
