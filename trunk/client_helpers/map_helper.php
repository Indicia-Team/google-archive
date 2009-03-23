<?php

require_once "helper_config.php";
/**
* <p> Class abstracting a mapping component - will support methods to add layers,
* controls etc.</p>
*/
Class Map extends helper_config
{

  // Name of the control
  public $name = 'map';
  // Internal object name
  private $internalObjectName;
  // Height of the map control
  public $height = '600px';
  // Width of the map control
  public $width = '850px';
  // Latitude
  public $latitude = -100000;
  // Longitude
  public $longitude = 6700000;
  // Zoom
  public $zoom = 7;
  // Base URL of the Indicia Core GeoServer instance to use - defaults to localhost
  public $indiciaCore = 'http://localhost:8080/geoserver/';
  // Proxy host to use for cross-site requests - false if not to use
  public $proxy = 'http://localhost/cgi-bin/proxy.cgi?url=';
  // Private array of options - passed in similar style to the javascript
  private $options = Array
  (
  'projection' => 'new OpenLayers.Projection("EPSG:900913")',
  'displayProjection' => 'new OpenLayers.Projection("EPSG:4326")',
  'units' => '"m"',
  'numZoomLevels' => '18',
  'maxResolution' => '156543.0339',
  'maxExtent' => 'new OpenLayers.Bounds(-20037508,-20037508,20037508,20037508.34)'
  );
  // Map display format
  public $format = 'image/png';
  // Private array of layers
  private $layers = Array();
  // Private array of controls
  private $controls = Array();
  // Private array of libraries which may be included
  private $library_sources = Array();
  private $libraries = Array();
  private $haskey = Array('google' => true, 'multimap' => true);

  // Constants used to add default layers
  const LAYER_GOOGLE_PHYSICAL = 0;
  const LAYER_GOOGLE_STREETS = 1;
  const LAYER_GOOGLE_SATELLITE = 2;
  const LAYER_GOOGLE_HYBRID = 3;
  const LAYER_OPENLAYERS_WMS = 4;
  const LAYER_NASA_MOSAIC = 5;
  const LAYER_VIRTUAL_EARTH = 6;
  const LAYER_MULTIMAP_DEFAULT = 7;
  const LAYER_MULTIMAP_LANDRANGER = 8;

  /**
  * <p>Returns a new map. This will not display the map until the render() method is
  * called.</p>
  *
  * @param String $indiciaCore URL of the Indicia Core geoServer instance.
  * @param Mixed $layers Indicates preset layers to load automatically - by default will load
  * all preset layers (calling true) but may also specify a single layer or array of
  * layers to display. Non-preset layers should be added later.
  */
  public function __construct($indiciaCore = null, $layers = true, $options = null)
  {
    if ($indiciaCore != null) $this->indiciaCore = $indiciaCore;
    if ($options != null) $this->options = array_merge($this->options, $options);
    $google_api_key = parent::$google_api_key;
    // if ($google_api_key == '...') $this->haskey['google'] = false;
    $multimap_api_key = parent::$multimap_api_key;
    if ($multimap_api_key == '...') $this->haskey['multimap'] = false;
    $this->library_sources = Array
    (
    'openLayers' => parent::$base_url.'/media/js/OpenLayers.js',
    'google' => "http://maps.google.com/maps?file=api&v=2&key=$google_api_key",
    'virtualearth' => 'http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1',
    'multimap' => "http://developer.multimap.com/API/maps/1.2/$multimap_api_key"
    );
    $lta = array();
    $this->addLibrary('openLayers');
    if ($layers === true)
    {
      $lta = array(0,1,2,3,4,5,6);
    }
    else if (is_array($layers))
    {
      $lta = $layers;
    }
    else
    {
      $lta = array($layers);
    }
    foreach ($lta as $layer)
    {
      $this->addPresetLayer($layer);
    }
    $this->internalObjectName = "map".rand();
  }

  public function addPresetLayer($layer)
  {
    switch ($layer)
    {
      case self::LAYER_GOOGLE_PHYSICAL:
	if ($this->haskey['google'])
	{
	  $this->addLayer("OpenLayers.Layer.Google
	  (
	  'Google Physical',
					    {type: G_PHYSICAL_MAP, 'sphericalMercator': 'true'})");
					    $this->addLibrary('google');
	}
	break;
      case self::LAYER_GOOGLE_STREETS:
	if ($this->haskey['google'])
	{
	  $this->addLayer("OpenLayers.Layer.Google('Google Streets',
					    {numZoomLevels : 20, 'sphericalMercator': true})");
					    $this->addLibrary('google');
	}
	break;
      case self::LAYER_GOOGLE_HYBRID:
	if ($this->haskey['google'])
	{
	  $this->addLayer("OpenLayers.Layer.Google('Google Hybrid',
		       {type: G_HYBRID_MAP, numZoomLevels: 20, 'sphericalMercator': true})");
		       $this->addLibrary('google');
	}
	break;
      case self::LAYER_GOOGLE_SATELLITE:
	if ($this->haskey['google'])
	{
	  $this->addLayer("OpenLayers.Layer.Google('Google Satellite',
	  {type: G_SATELLITE_MAP, numZoomLevels: 20, 'sphericalMercator': true})");
	  $this->addLibrary('google');
	}
	break;
      case self::LAYER_OPENLAYERS_WMS:
	$this->addLayer("OpenLayers.Layer.WMS('OpenLayers WMS',
			     'http://labs.metacarta.com/wms/vmap0',
			     {layers: 'basic', 'sphericalMercator': true})");
			     break;
      case self::LAYER_NASA_MOSAIC:
	$this->addLayer("OpenLayers.Layer.WMS('NASA Global Mosaic',
	'http://t1.hypercube.telascience.org/cgi-bin/landsat7',
	{layers: 'landsat7', 'sphericalMercator': true})");
	break;
      case self::LAYER_VIRTUAL_EARTH:
	$this->addLayer("OpenLayers.Layer.VirtualEarth('Virtual Earth',
	{'type': VEMapStyle.Aerial, 'sphericalMercator': true})");
	$this->addLibrary('virtualearth');
	break;
      case self::LAYER_MULTIMAP_DEFAULT:
	if ($this->haskey['multimap'])
	{
	  $this->addLayer("OpenLayers.Layer.MultiMap(
	  'MultiMap', {sphericalMercator: true})");
	  $this->addLibrary('multimap');
	}
	break;
      case self::LAYER_MULTIMAP_LANDRANGER:
	if ($this->haskey['multimap'])
	{
	  $this->addLayer("OpenLayers.Layer.MultiMap(
	  'OS Landranger', {sphericalMercator: true, dataSource: 904})");
	  $this->addLibrary('multimap');
	}
	break;
  }
}

/**
* <p> Adds a WMS layer from the Indicia Core to the map. </p>
*/
public function addIndiciaWMSLayer($title, $layer, $base = false)
{
  $base = $base ? 'true' : 'false';
  $this->addLayer("OpenLayers.Layer.WMS('$title',
  '".$this->indiciaCore."wms',
  { layers: '$layer', transparent: true },
  { isBaseLayer: $base, sphericalMercator: true})");
}

/**
* <p> Adds a layer from the Indicia Core to the map control.</p>
*/
public function addIndiciaWFSLayer($title, $type)
{
  $this->addLayer("OpenLayers.Layer.WFS('$title', '".$this->indiciaCore."wfs',
  { typename: '$type', request: 'GetFeature' },
  { sphericalMercator: true }
  )");
}

/**
* <p> Adds a layer to the map control.</p>
*
* @param String $layerDef Javascript definition (appropriate to the OpenLayers
* library) for the layer to be added. This will be called as a new object and
* as such should be parsable in this way.
*/
public function addLayer($layerDef)
{
  $this->layers[] = $layerDef;
}

/**
* <p> Adds a control to the map.</p>
*
* @param String $controlDef Javascript definition for the control to be added. This will be called
* as a new object and should be parsable in this way.
*/
public function addControl($controlDef)
{
  $this->controls[] = $controlDef;
}

/**
* <p> Adds a library to the libraries collection. </p>
*/
private function addLibrary($libName)
{
  if (! array_key_exists($libName, $this->libraries))
  {
    if (array_key_exists($libName, $this->library_sources))
    {
      $this->libraries[$libName] = $this->library_sources[$libName];
    }
  }
}

// Renders the control
public function render()
{
  $r = "";
  $intLayers = array();
  $ion = $this->internalObjectName;
  foreach ($this->options as $key => $val)
  {
    $opt[] = $key.": ".$val;
  }
  // Renders the libraries
  foreach ($this->libraries as $lib)
  {
    $r .= "<script type='text/javascript' src='$lib' ></script>\n";
  }
  // Render the main javascript
  $r .= "<script type='text/javascript'>";
  $r .= "var map = null;";
  $r .= "var format = '$this->format';\n"
  ."function init(){\n"
  ."var options = {".implode(",\n", $opt)."};\n";
  if ($this->proxy) $r .= "OpenLayers.ProxyHost = '".$this->proxy."';\n";
  $r .= "$ion = new OpenLayers.Map('".$this->name."', options);\n";
  foreach ($this->layers as $layer)
  {
    $a = "layer".rand();
    $intLayers[] = $a;
    $r .= "var $a = new $layer;\n";
  }
  $r .= "$ion.addLayers([".implode(',', $intLayers)."]);\n";
  if (count($this->layers) >=2 )
  {
    $r .= "$ion.addControl(new OpenLayers.Control.LayerSwitcher());\n";
  }
  list ($lat, $long, $zoom) = array($this->latitude, $this->longitude, $this->zoom);
  $r .= "$ion.setCenter(new OpenLayers.LonLat($long,$lat),$zoom);";
  $r .= "}";
  $r .= "</script>\n";
  $r .= "<div class='smallmap' id='".$this->name
  ."' style='width: ".$this->width."; height: "
  .$this->height.";'></div>\n";
  $r .= "<script type='text/javascript'>init();</script>";
  return $r;
}

}
