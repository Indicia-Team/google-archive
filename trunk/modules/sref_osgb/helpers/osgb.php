<?php

class osgb {

	/**
	 * Returns true if the spatial reference is a recognised British National Grid square.
	 *
	 * @param $sref string Spatial reference to validate
	 */
	public static function is_valid($sref)
	{
		$sq100 = strtoupper(substr($sref, 0, 2));
		if (!preg_match('(H[L-Z]|J[LMQR]|N[A-HJ-Z]|O[ABFGLMQRVW]|S[A-HJ-Z]|T[ABFGLMQRVW])', $sq100))
			return FALSE;
		$eastnorth=substr($sref, 2);
		// Remaining chars must be all numeric and an equal number, up to 10 digits
		if (!preg_match('/^[0-9]*$/', $eastnorth) || strlen($eastnorth) % 2 != 0 || strlen($eastnorth)>10)
			return FALSE;
		return TRUE;
	}

	public static function sref_to_wkt($sref)
	{
		if (!self::is_valid($sref))
			throw new Exception('Spatial reference is not a recognisable grid square.');
		$sq_100 = self::get_100k_square($sref);
		$coordLen = (strlen($sref)-2)/2;
  		// extract the easting and northing
  		$east  = substr($sref, 2, $coordLen);
  		$north = substr($sref, 2+$coordLen);
  		// if < 10 figure the easting and northing need to be multiplied up to the power of 10
  		$sq_size = pow(10, 5-$coordLen);
  		$east = $east * $sq_size;
  		$north = $north * $sq_size;
  		$westEdge=$east + $sq_100['x'];
  		$southEdge=$north + $sq_100['y'];
  		$eastEdge=$westEdge+$sq_size;
  		$northEdge=$southEdge+$sq_size;
  		return 	"POLYGON(($westEdge $southEdge,$westEdge $northEdge,".
				"$eastEdge $northEdge,$eastEdge $southEdge,$westEdge $southEdge))";
	}

	public static function wkt_to_sref($x, $y)
	{

	}

	/**
	 * Return the underying EPSG code for the datum this notation is based on (Airy 1830)
	 */
	public static function get_srid()
	{
		return 27700;
	}

	/** Retrieve the easting and northing of the sw corner of a
	 * 100km square, indicated by the first 2 chars of the grid ref.
	 *
	 * @param string $sref Spatial reference string to parse (OSGB)
	 * @return array Array containing (x, y)
	 */
	protected static function get_100k_square($sref)
	{
		$north = 0;
		$east = 0;
		$char1 =substr($sref, 0, 1);
		switch ($char1){
			case 'H':
				$north += 1000000;
				break;
			case 'N':
				$north += 500000;
				break;
			case 'O':
				$north += 500000;
    			$east  += 500000;
    			break;
    		case 'T':
    			$east += 500000;
    			break;
		}
  		$char2ord = ord(substr($sref, 1, 1));
		if ($char2ord > 73) $char2ord--; // Adjust for no I
		$east += (($char2ord - 65) % 5) * 100000;
  		$north += (4 - floor(($char2ord - 65) / 5)) * 100000;
  		$output['x']=$east;
  		$output['y']=$north;
		return $output;
	}

}
?>
