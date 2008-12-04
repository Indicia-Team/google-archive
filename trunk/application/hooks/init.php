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

}

Event::add('system.ready', array('Indicia', 'Init'));

?>
