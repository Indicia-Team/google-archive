<?php defined('SYSPATH') or die('No direct script access.');

class valid extends valid_Core {

	/**
	 * Validate a spatial reference system is recognised, either an EPSG code or a notation.
	 *
	 * @param   string   system
	 * @return  boolean
	 * $todo Should we consider caching this?
	 */
	public static function sref_system($system)
	{
		return spatial_ref::is_valid_system($system);
	}

	/**
	 * Validate a spatial reference is a valid value for the system
	 *
	 * @param   string   sref
	 * @param   string   system
	 * @return  boolean
	 * $todo Should we consider caching the system?
	 */
	public static function sref($sref, $system)
	{
		$system = $system[0];
		return spatial_ref::is_valid($sref, $system);
	}

}

?>
