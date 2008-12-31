<?php echo html::script(array(
	'media/js/jquery.ajaxQueue.js',
	'media/js/jquery.bgiframe.min.js',
	'media/js/thickbox-compressd.js',
	'media/js/jquery.autocomplete.js',
	'http://openlayers.org/api/OpenLayers.js'
), FALSE); ?>
<script src='http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'></script>

<script type="text/javascript">
    // making this a global variable so that it is accessible for
    // debugging/inspecting in Firebug
    var map = null;
    var editlayer = null;
    var format = 'image/png';
    var current_sref=null;

    OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
                defaultHandlerOptions: {
                    'single': true,
                    'double': false,
                    'pixelTolerance': 0,
                    'stopSingle': false,
                    'stopDouble': false
                },

                initialize: function(options) {
                    this.handlerOptions = OpenLayers.Util.extend(
                        {}, this.defaultHandlerOptions
                    );
                    OpenLayers.Control.prototype.initialize.apply(
                        this, arguments
                    );
                    this.handler = new OpenLayers.Handler.Click(
                        this, {
                            'click': this.trigger
                        }, this.handlerOptions
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
                    $.get("<?php echo url::base(); ?>/index.php/services/spatial/wkt_to_sref",
                    		{wkt: "POINT(" + lonlat.lon + "  " + lonlat.lat + ")",
                    		 system: document.getElementById("centroid_sref_system").value,
                    		 precision: precision },
                    	function(data){
  							$("#centroid_sref").attr('value', data);
							editlayer.destroyFeatures();
							// TODO - Json encode this into the previous response so we don't call twice
							$.get("<?php echo url::base(); ?>/index.php/services/spatial/sref_to_wkt",
								{sref: data, system: document.getElementById("centroid_sref_system").value },
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
    	if (current_sref!=document.getElementById("centroid_sref").value) {
    		// Send an AJAX request for the wkt to draw on the map
    		$.get("<?php echo url::base(); ?>/index.php/services/spatial/sref_to_wkt",
            	{sref: document.getElementById("centroid_sref").value, system: "osgb"},
            	function(data){
					show_wkt_feature(data);
            	}
			);
    	}

    }

	// When entering an sref control, store its current value so we can detect changes.
    function enter_sref() {
    	current_sref = document.getElementById("centroid_sref").value;
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
	    // limit zoom to 12 as this is limit of Virtual Earth
	    if (map.getZoom()>12)
	    	map.zoomTo(12);
    }

    function init_map(){

			var boundary_style = OpenLayers.Util.applyDefaults({
	             strokeWidth: 1,
	             strokeColor: "#ff0000",
	             fillOpacity: 0.3,
	             fillColor:"#ff0000"
	        }, OpenLayers.Feature.Vector.style['default']);

            var options = {
                projection: new OpenLayers.Projection("EPSG:900913"),
                displayProjection: new OpenLayers.Projection("EPSG:4326"),
                units: "m",
                numZoomLevels: 18,
                maxResolution: 156543.0339,
                maxExtent: new OpenLayers.Bounds(-20037508, -20037508,
                                                 20037508, 20037508.34)
            };
            map = new OpenLayers.Map('map', options);
			editlayer = new OpenLayers.Layer.Vector("Current location boundary",
                                                    {style: boundary_style, 'sphericalMercator': true});

			var velayer = new OpenLayers.Layer.VirtualEarth(
            	"Virtual Earth",
            	{'type': VEMapStyle.Aerial, 'sphericalMercator': true}
            );
            map.addLayers([velayer, editlayer]);
            map.addControl(new OpenLayers.Control.LayerSwitcher());
            <?php
            	if (!empty($model->centroid_geom)) {
            		echo "show_wkt_feature('$model->centroid_geom');";
			} else { ?>
				map.setCenter(new OpenLayers.LonLat(-100000,7300000),4);
			<?php } ?>
            var click = new OpenLayers.Control.Click();
            map.addControl(click);
            click.activate();
        }

$(document).ready(function() {

	init_map();

	$("input#parent").autocomplete("<?php echo url::site() ?>index.php/services/data/location", {
		minChars : 1,
		mustMatch : true,
		extraParams : {
			orderby : "name",
			mode : "json"
		},
		parse: function(data) {
			var results = [];
			var obj = JSON.parse(data);
			$.each(obj, function(i, item) {
				results[results.length] = {
					'data' : item,
					'value' : item.id,
					'result' : item.name };
			});
			return results;
		},
		formatItem: function(item) {
			return item.name;
		},
		formatResult: function(item) {
			return item.id;
		}
	});
	$("input#parent").result(function(event, data){
		$("input#parent_id").attr('value', data.id);
	});
});
</script>
<p>This page allows you to specify the details of a location.</p>
<form class="cmxform" action="<?php echo url::site().'location/save'; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<fieldset>
<legend>Location details</legend>
<ol>
<li>
<label for="name">Name</label>
<input id="name" name="name" value="<?php echo html::specialchars($model->name); ?>" />
<?php echo html::error_message($model->getError('name')); ?>
</li>
<li>
<label for="code">Code</label>
<input id="code" name="code" value="<?php echo html::specialchars($model->code); ?>" />
<?php echo html::error_message($model->getError('code')); ?>
</li>
<li>
<label for="centroid_sref">Spatial Ref:</label>
<input id="centroid_sref" class="narrow" name="centroid_sref"
	value="<?php echo html::specialchars($model->centroid_sref); ?>"
	onblur="exit_sref();"
	onclick="enter_sref();"/>
<select class="narrow" id="centroid_sref_system" name="centroid_sref_system">
<?php foreach (kohana::config('sref_notations.sref_notations') as $notation=>$caption) {
	if ($model->centroid_sref_system==$notation)
		$selected=' selected="selected"';
	else
		$selected = '';
	echo "<option value=\"$notation\"$selected>$caption</option>";}
?>
</select>
<div id="sref"></div>
<?php echo html::error_message($model->getError('centroid_sref')); ?>
<?php echo html::error_message($model->getError('centroid_sref_system')); ?>
<p class="instruct">Zoom the map in by double-clicking then single click on the location's centre to set the
spatial reference. The more you zoom in, the more accurate the reference will be.</p>
<div id="map" class="smallmap" style="width: 600px; height: 350px;"></div>
</li>
<li>
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo html::specialchars($model->parent_id); ?>" />
<label for="parent">Parent Location</label>
<input id="parent" name="parent" value="<?php echo (($model->parent_id != null) ? html::specialchars(ORM::factory('location', $model->parent_id)->name) : ''); ?>" />
</li>
</ol>
</fieldset>
<fieldset>
<legend>Location Websites</legend>
<ol>
<?php
	if (!is_null($this->gen_auth_filter))
		$websites = ORM::factory('website')->in('id',$this->gen_auth_filter['values'])->orderby('title','asc')->find_all();
	else
		$websites = ORM::factory('website')->orderby('title','asc')->find_all();
	foreach ($websites as $website) {
		echo '<li><label for="website_'.$website->id.'">'.$website->title.'</label>';
		echo '<INPUT TYPE=CHECKBOX NAME="website_'.$website->id.'" ';
		if(!is_null($model->id)){
			$locations_website = ORM::factory('locations_website', array('website_id' => $website->id, 'location_id' => $model->id));
			if(ORM::factory('locations_website', array('website_id' => $website->id, 'location_id' => $model->id))->loaded) echo "CHECKED";
		}
		echo '></li>';
	}
?>
</ol>
</fieldset>
<?php echo $metadata ?>
<input type="submit" value="Save" name="submit"/>
</form>
