var map = null;
var editlayer = null;
var format = 'image/png';
var current_sref=null;
var indicia_url;
var input_field_name;
var geoplanet_api_key;

OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
	defaultHandlerOptions: {
		'single': true,
		'double': false,
		'pixelTolerance': 0,
		'stopSingle': false,
		'stopDouble': false
	},

	initialize: function(options) {
		this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions);
		OpenLayers.Control.prototype.initialize.apply(this, arguments);
		this.handler = new OpenLayers.Handler.Click(
			this,
			{'click': this.trigger},
			this.handlerOptions
		);
	},

	trigger: function(e) {
		var lonlat = map.getLonLatFromViewPortPx(e.xy);
		// get approx metres accuracy we can expect from the mouse click - about 5mm accuracy.
		var precision = map.getScale()/200;
		// now round to find appropriate square size
		if (precision<30) {
			precision=8;
		} else if (precision<300) {
			precision=6;
		} else if (precision<3000) {
			precision=4;
		} else {
			precision=2;
		}
		$.get(indicia_url + "/index.php/services/spatial/wkt_to_sref",
			{wkt: "POINT(" + lonlat.lon + "  " + lonlat.lat + ")",
			system: document.getElementById(input_field_name + "_system").value,
			precision: precision },
			function(data){
				$("#"+input_field_name).attr('value', data);
				editlayer.destroyFeatures();
				// TODO - Json encode this into the previous response so we don't call twice
				$.get(indicia_url + "/index.php/services/spatial/sref_to_wkt",
					{sref: data, system: document.getElementById(input_field_name + "_system").value },
					function(data){
						var parser = new OpenLayers.Format.WKT();
						var feature = parser.read(data);
						editlayer.addFeatures([feature]);
					}
				);
			}
		);
	}
});

// When exiting an sref control, if the value was manually changed, update the map.
function exit_sref() {
	if (current_sref!=document.getElementById(input_field_name).value) {
	   	// Send an AJAX request for the wkt to draw on the map
	   	$.get(indicia_url + "/index.php/services/spatial/sref_to_wkt",
			{sref: document.getElementById(input_field_name).value, system: "osgb"},
			function(data){
				show_wkt_feature(data);
			}
		);
	}
}

// When entering an sref control, store its current value so we can detect changes.
function enter_sref() {
	current_sref = document.getElementById(input_field_name).value;
}

function show_wkt_feature(wkt) {
	var parser = new OpenLayers.Format.WKT();
	var feature = parser.read(wkt);
	editlayer.destroyFeatures();
	editlayer.addFeatures([feature]);
	var bounds=feature.geometry.getBounds();
	// extend the boundary to include a buffer, so the map does not zoom too tight.
	dy = (bounds.top-bounds.bottom)/1.5;
	dx = (bounds.right-bounds.left)/1.5;
	bounds.top = bounds.top + dy;
	bounds.bottom = bounds.bottom - dy;
	bounds.right = bounds.right + dx;
	bounds.left = bounds.left - dx;
	// Set the default view to show something triple the size of the grid square
	map.zoomToExtent(bounds);
	// if showing a point, don't zoom in too far
	if (dy==0 && dx==0) {
		map.zoomTo(10);
	}

}

// When the document is ready, initialise the map. This needs to be passed the base url for services and the
// wkt of the object to display if any. If Google=TRUE, then the calling page must have the Google Maps API
// link in the header with a valid API key.
function init_map(base_url, wkt, field_name, google, geoplanet) {
	// store a couple of globals for future use
	indicia_url=base_url;
	input_field_name=field_name;
	geoplanet_api_key=geoplanet;

	var boundary_style = OpenLayers.Util.applyDefaults({
		strokeWidth: 1,
		strokeColor: "#ff0000",
		fillOpacity: 0.3,
		fillColor:"#ff0000"
	}, OpenLayers.Feature.Vector.style['default']);

	var options = {
		projection: new OpenLayers.Projection("EPSG:900913"),
		displayProjection: new OpenLayers.Projection("EPSG:27700"),
		units: "m",
		numZoomLevels: 18,
		maxResolution: 156543.0339,
		maxExtent: new OpenLayers.Bounds(
			-20037508, -20037508,
			20037508, 20037508.34)
	};
	map = new OpenLayers.Map('map', options);

	editlayer = new OpenLayers.Layer.Vector("Current location boundary",
		{style: boundary_style, 'sphericalMercator': true});

	var velayer = new OpenLayers.Layer.VirtualEarth(
		"Virtual Earth",
		{'type': VEMapStyle.Aerial, 'sphericalMercator': true}
		);

	if (google) {
		var gphy = new OpenLayers.Layer.Google(
			"Google Physical",
 			{type: G_PHYSICAL_MAP, 'sphericalMercator': true}
		);
		var gmap = new OpenLayers.Layer.Google(
			"Google Streets", // the default
			{numZoomLevels: 20, 'sphericalMercator': true}
		);
		var ghyb = new OpenLayers.Layer.Google(
			"Google Hybrid",
			{type: G_HYBRID_MAP, numZoomLevels: 20, 'sphericalMercator': true}
		);
		var gsat = new OpenLayers.Layer.Google(
			"Google Satellite",
			{type: G_SATELLITE_MAP, numZoomLevels: 20, 'sphericalMercator': true}
		);

		map.addLayers([velayer, gphy, gmap, ghyb, gsat, editlayer]);
	}
	else
		map.addLayers([velayer, editlayer]);

  	map.addControl(new OpenLayers.Control.LayerSwitcher());
	if (wkt!=null) {
		show_wkt_feature(wkt);
	} else {
		map.setCenter(new OpenLayers.LonLat(-100000,7300000),4);
	}
	var click = new OpenLayers.Control.Click();
	map.addControl(click);
	click.activate();
}

// Method called to find a place using the GeoPlanet API.
// Pref_area is the text to suffix to location searches to help keep them within the target region, e.g. gb or Dorset.
function find_place(pref_area)
{
	$('#place_search_box').hide();
	$('#place_search_output').empty();
	var ref;
	var searchtext = $('#place_search').attr('value');
	var request = 'http://where.yahooapis.com/v1/places.q("' +
			searchtext + ' ' + pref_area + ');count=10';
    $.getJSON(request + "?format=json&appid="+geoplanet_api_key+"&callback=?", function(data){
    	if (data.places.count==1 && data.places.place[0].name.toLowerCase()==searchtext.toLowerCase()) {
    		ref=data.places.place[0].centroid.longitude + ', ' + data.places.place[0].centroid.latitude;
			show_found_place(ref);
    	} else if (data.places.count!=0) {
    		$('<p>Select from the following places that were found matching your search:</p>').appendTo('#place_search_output');
    		var ol=$('<ol>');
	  		$.each(data.places.place, function(i,place){
	  			ref="'" + place.centroid.longitude + ', ' + place.centroid.latitude + "'";
				$('<li><a href="#" onclick="show_found_place('+ref+');">'+place.name+' (' + place.placeTypeName + '), '+place.admin1 + '\\' + place.admin2 + '</a></li>').appendTo(ol);
	  		});
	  		ol.appendTo('#place_search_output');
	  		$('#place_search_box').show("slow");
    	} else {
    		$('<p>No locations found with that name. Try a nearby town name.</p>').appendTo('#place_search_output');
			$('#place_search_box').show("slow");
    	}
	});
}

// Once a place has been found, places the spatial reference as a point on the map.
function show_found_place(ref) {
	$.get(indicia_url + "/index.php/services/spatial/sref_to_wkt",
		{sref: ref, system: "4326"},
		function(data){
			show_wkt_feature(data);
		}
	);
}