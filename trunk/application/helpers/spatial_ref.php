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
	public static function sref_to_internal_wkt($sref, $sref_system)
	{
		$system = strtolower($sref_system);
		$sref = strtoupper($sref);
		if (is_numeric($system)) {
			$coords = explode(kohana::lang('misc.x_y_separator'), $sref);
			$wkt = 'POINT('.$coords[0].' '.$coords[1].')';
			$srid = $system;
		} else {
			$wkt = call_user_func("$system::sref_to_wkt", $sref);
			$srid = call_user_func("$system::get_srid");
		}
		return self::wkt_to_internal_wkt($wkt, $srid);
	}

	/**
	 * Converts WKT text in a known SRID, to WKT in internally stored srid.
	 *
	 * @todo Consider moving PostGIS specific code into a driver.
	 */
	protected static function wkt_to_internal_wkt($wkt, $srid)
	{
		// WGS84 = same as internally stored values, so don't bother transforming if already there.
		if ($srid!=kohana::config('sref_notations.internal_srid')) {
			$db = new Database;
			$result = $db->query("SELECT ST_asText(ST_Transform(ST_GeomFromText('$wkt',$srid),".
				kohana::config('sref_notations.internal_srid').")) AS wkt;")->current();
			return $result->wkt;
		} else
			return $wkt;
	}

	/*
	 * Converts a internal WKT value to any output sref - either a notation, or a transformed WKT
	 */
	public static function internal_wkt_to_sref($wkt, $sref_system, $precision=null)
	{
		$system = strtolower($sref_system);
		if (is_numeric($system))
			$srid = $system;
		else
			$srid = call_user_func("$system::get_srid");
		$db = new Database;
		$result = $db->query("SELECT ST_asText(ST_Transform(ST_GeomFromText(" .
				"'$wkt',".kohana::config('sref_notations.internal_srid')."),$srid)) AS wkt;")->current();
		if (is_numeric($system))
			return self::point_to_lat_lon($result->wkt, $system);
		else
			return call_user_func("$system::wkt_to_sref", $result->wkt, $precision);
	}

	/**
	 * Convert a point wkt into a x, y representation.
	 * @param int $system The SRID of the system, used to determine the rounding that should be applied to the x,y values.
	 */
	protected static function point_to_lat_lon($wkt, $system)
	{
		$locale=localeconv();
		if ((bool) preg_match(
					'/^POINT\([-+]?[0-9]*\\'.$locale['decimal_point'].'?[0-9]+[ ]*[-+]?[0-9]*\\'.$locale['decimal_point'].'?[0-9]+\)$/D', $wkt)) {
			$coords = explode(' ', substr($wkt, 6, strlen($wkt)-7));
			$roundings = kohana::config('sref_notations.roundings');
			if (array_key_exists($system, $roundings))
				$round = $roundings[$system];
			else
				$round = 0;
			return round($coords[0],$round).Kohana::lang('misc.x_y_separator')." ".round($coords[1],$round);
		} else {
			throw new Exception('point_to_lat_long passed invalid wkt - '.$wkt);
		}
	}

}

?>
