<?php

class spatial_ref {

	/**
	 * Provides a wrapper for dynamic calls to a spatial reference module's validate
	 * method.
	 */
	public static function is_valid($sref, $sref_system)
	{
		$system = strtolower($sref_system);
		if (is_numeric($system)) {
			// EPSG code, so check this is just a pair of numbers with a list separator
			$locale=localeconv();
			return (bool) preg_match(
					'/^[-+]?[0-9]*\\'.$locale['decimal_point'].'?[0-9]+'.
					Kohana::lang('misc.x_y_separator').
					'[ ]*[-+]?[0-9]*\\'.$locale['decimal_point'].'?[0-9]+$/D', $sref);
		} else {
			// validate the notation by calling the module which translates it for us
			return (bool) call_user_func("$system::is_valid", $sref);
		}
	}

	/**
	 * Returns true if a spatial reference system is recognisable as a notation
	 * or EPSG code.
	 */
	public static function is_valid_system($system)
	{
		$db = new Database();
		if (is_numeric($system)) {
			$found = $db->count_records('spatial_ref_sys', array('auth_srid' => $system));
		} else {
			$found = array_key_exists(strtolower($system), kohana::config('sref_notations.sref_notations'));
		}
		return $found>0;
	}

	/**
	 * Provides a wrapper for dynamic calls to a spatial reference module's
	 * spatial_ref_to_wkt method (produces well known text from an sref).
	 *
	 * @return string Well Known Text for the point or polygon described by the sref, in
	 * the WGS84 datum.
	 */
	public static function sref_to_wgs84($sref, $sref_system)
	{
		$system = strtolower($sref_system);
		if (is_numeric($system)) {
			$coords = explode(kohana::lang('misc.x_y_separator'), $sref);
			$wkt = 'POINT('.$coords[0].' '.$coords[1].')';
			$srid = $system;
		} else {
			$wkt = call_user_func("$system::sref_to_wkt", $sref);
			$srid = call_user_func("$system::get_srid");
		}
		return self::wkt_to_wgs84($wkt, $srid);
	}

	/**
	 * Converts WKT text in a known SRID, to WKT WGS84.
	 *
	 * @todo Consider moving PostGIS specific code into a driver.
	 */
	protected static function wkt_to_wgs84($wkt, $srid)
	{
		// WGS84 = srid 4326, so don't bother transforming if already there.
		if ($srid!=4326) {
			$db = new Database;
			$result = $db->query("SELECT ST_asText(ST_Transform(ST_GeomFromText('$wkt',$srid),4326)) AS wgs84;")->current();
			return $result->wgs84;
		}
	}





}

?>
