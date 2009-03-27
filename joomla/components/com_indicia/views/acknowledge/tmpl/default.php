<?php $params =& $mainframe->getPageParameters('com_indicia'); ?>

<?php
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'data_entry_helper.php');

if ($params->get('geoserver_url')) {
	echo "<h2>".JText::_('Distribution Map')."</h2>";
	echo "<p>".JText::_($params->get('map_intro'))."</p>";
	require_once(JPATH_COMPONENT.DS.'helpers'.DS.'map_helper.php');
	$map = new Map($params->get('geoserver_url'), array(Map::LAYER_GOOGLE_PHYSICAL, Map::LAYER_GOOGLE_SATELLITE));
	$map->width=400;
	$map->addIndiciaWMSLayer(JText::_('Wake Up Call Observations'), $params->get('map_feature_type'));
	$map->longitude=$params->get('init_x');
	$map->latitude=$params->get('init_y');
	$map->zoom=$params->get('init_zoom');
	echo $map->render();
}


?>