<?php
global $mainframe;
$params =& $mainframe->getPageParameters('com_indicia');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'data_entry_helper.php');

if ($params->get('geoserver_url')) {
	echo "<h2>".JText::_('Distribution Map')."</h2>";
	require_once(JPATH_COMPONENT.DS.'helpers'.DS.'map_helper.php');
	// request the species name and image from the data services
	$url = $params->get('indicia_url')."index.php/services/data";
	$request="$url/taxa_taxon_list?mode=json&id=".$_GET['taxa_taxon_list_id'];
	$readAuth=data_entry_helper::get_read_auth($params->get('website_id'), $params->get('password'));

	$request .= "&nonce=".$readAuth['nonce']."&auth_token=".$readAuth['auth_token'];
	$session=curl_init($request);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	$taxon = json_decode(array_pop(explode("\r\n\r\n",curl_exec($session))), true);
	// We only want the first entry in the list, as there should be only one.
	$taxon=$taxon[0];
	$map = new Map($params->get('geoserver_url'), array(Map::LAYER_GOOGLE_PHYSICAL, Map::LAYER_GOOGLE_SATELLITE));
	$map->width=400;
	$map->addIndiciaWMSLayer(JText::_('Wake Up Call Observations'), $params->get('map_feature_type'));

	$map->addLayer("OpenLayers.Layer.WMS($taxon['taxon'], '//localhost:8080/geoserver/wms', " .
		"{ layers: 'indicia:wake_up_call', transparent: true, " .
		"styles: 'distribution_point', " .
		"filter:'<ogc:Filter xmlns:gml=\"http://www.opengis.net/gml\" xmlns:ogc=\"http://www.opengis.net/ogc\">" .
		"<ogc:PropertyIsEqualTo><ogc:PropertyName>taxa_taxon_list_id</ogc:PropertyName><ogc:Literal>".$_GET['taxa_taxon_list_id']."</ogc:Literal></ogc:PropertyIsEqualTo></ogc:Filter>'".
		"}, { isBaseLayer: false, sphericalMercator: true})");
	$map->longitude=$params->get('init_x');
	$map->latitude=$params->get('init_y');
	$map->zoom=$params->get('init_zoom');
	echo "<p>".JText::sprintf($params->get('map_intro'), $taxon['taxon'])."</p>";
	echo $map->render();
}


?>