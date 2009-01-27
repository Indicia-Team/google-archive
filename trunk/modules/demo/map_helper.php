<?php

include "helper_config.php";
/**
 * <p> Class abstracting a mapping component - will support methods to add layers, controls etc.
 */
public Class map {

	// Private array of options - passed in similar style to the javascript
	private $options = Array(
		'projection' => 'new OpenLayers.Projection("EPSG:900913")',
		'displayProjection' => 'new OpenLayers.Projection("EPSG:4326")',
		'units' => '"m"',
		'numZoomLevels' => '18',
		'maxResolution' => '156543.0339',
		'maxExtent' => 'new OpenLayers.Bounds(-20037508,-20037508,20037508,20037508.34)'
	);

	// Google API Key
	private $googleApiKey = $config['google_api_key'];
	// Private array of layers
	private $layers = Array();
	// Private array of libraries which may be included 
	private $library_sources = Array(
		'openLayers' => '../../media/js/OpenLayers.js',
		'google' => "http://maps.google.com/maps?file=api&v=2&key=$googleApiKey",
		'virtualearth' => 'http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'
	);
	private $libraries;

	// Constants used to add default layers
	define("LAYER_GOOGLE_PHYSICAL", 0);
	define("LAYER_GOOGLE_STREETS", 1);
	define("LAYER_GOOGLE_SATELLITE", 2);
	define("LAYER_GOOGLE_HYBRID", 3);
	define("LAYER_OPENLAYERS_WMS", 4);
	define("LAYER_NASA_MOSAIC", 5);
	define("LAYER_VIRTUAL_EARTH", 6);

	public function addPresetLayer($layer){
		switch ($layer){
		case self::LAYER_GOOGLE_PHYSICAL:
			$this->addLayer("OpenLayers.Layer.Google(
				'Google Physical',
		{type: G_PHYSICAL_MAP, 'sphericalMercator': 'true'})");
			$this->libraries[] = $this->library_sources['google'];
			break;
		case self::LAYER_GOOGLE_STREETS:
			$this->addLayer("OpenLayers.Layer.Google(
				'Google Streets',
		{numZoomLevels : 20, 'sphericalMercator': true})");
			$this->libraries[] = $this->library_sources['google'];
			break;
		case self::LAYER_GOOGLE_HYBRID:
			$this->addLayer("OpenLayers.Layer.Google(
				'Google Hybrid',
		{type: G_HYBRID_MAP, numZoomLevels: 20, 'sphericalMercator': true})");
			$this->libraries[] = $this->library_sources['google'];
			break;
		case self::LAYER_GOOGLE_SATELLITE:
			$this->addLayer("OpenLayers.Layer.Google(
				'Google Satellite',
		{type: G_SATELLITE_MAP, numZoomLevels: 20, 'sphericalMercator': true})");
			$this->libraries[] = $this->library_sources['google'];
			break;
		case self::LAYER_OPENLAYERS_WMS:
			$this->addLayer("OpenLayers.Layer.WMS(
				'OpenLayers WMS',
				'http://labs.metacarta.com/wms/vmap0',
		{layers: 'basic', 'sphericalMercator': true})");
			break;
		case self::LAYER_NASA_MOSAIC:
			$this->addLayer("OpenLayers.Layer.WMS(
				'NASA Global Mosaic',
				'http://t1.hypercube.telascience.org/cgi-bin/landsat7',
		{layers: 'landsat7', 'sphericalMercator': true})");
			break;
		case self::LAYER_VIRTUAL_EARTH:
			$this->addLayer("OpenLayers.Layer.VirtualEarth(
				'Virtual Earth',
		{'type': VEMapStyle.Aerial, 'sphericalMercator': true})");
			$this->libraries[] = $this->library_sources['virtualearth'];
			break;
			break;
		}
	}

	// Renders the control
	public function render(){
	}


}
