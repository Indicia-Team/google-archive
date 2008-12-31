<?php
class Spatial_Controller extends Service_Base_Controller {

	/**
	 * Handle a service request to convert a spatial reference into WKT representing the reference
	 * using the internal SRID (normally spherical mercator since it is compatible with Google Maps)
	 */
	public function sref_to_wkt()
	{
		echo spatial_ref::sref_to_internal_wkt($_GET['sref'], $_GET['system']);
	}

	/**
	 * Handle a service request to convert a WKT representing the reference
	 * using the internal SRID (normally spherical mercator since it is compatible with Google Maps)
	 * into a spatial reference
	 */
	public function wkt_to_sref()
	{
		if (array_key_exists('precision',$_GET))
			$precision = $_GET['precision'];
		else
			$precision = null;
		echo spatial_ref::internal_wkt_to_sref($_GET['wkt'], $_GET['system'], $precision);
	}


}
?>
