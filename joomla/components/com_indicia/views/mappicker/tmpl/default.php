<?php
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'data_entry_helper.php');
$params =& $mainframe->getPageParameters('com_indicia');
$readAuth = data_entry_helper::get_read_auth($params->get('website_id'), $params->get('password'));
?>
<script src="http://maps.google.com/maps?file=api&h1=fr&amp;v=2&amp;key=<?php echo $params->get('google_map_key'); ?>"
      type="text/javascript"></script>
<h1><?php echo JText::_('Location Details'); ?></h1>
<p><?php echo JText::_('Location Intro'); ?></p>
<form class="indicia" method="POST" action="<?php echo $this->nextUri; ?>" >
<fieldset><legend><?php echo JText::_('Locality'); ?></legend>
<label for="location_name"><?php echo JText::_('Description of locality'); ?>:</label>
<input name="location_name" class="wide" value="<?php echo data_entry_helper::get_from_session('location_name'); ?>" /><br />
<label for="place_search"><?php echo JText::_('Search for place on map'); ?>:</label>
<?php echo data_entry_helper::geoplanet_search('place_search', 'find on map',
		$params->get('pref_area'), $params->get('country')); ?>
<br/>
<label for="entered_sref"><?php echo JText::_('Spatial Reference'); ?>:</label>
<?php echo data_entry_helper::map_picker
(
'entered_sref', 'geom',
array('2169'=>'Luxembourg Gauss','4326'=>'Latitude and Longitude (WGS84)'),
array('inc_google'=>'true','inc_virtual_earth'=>'false', 'init_layer'=>'Google Physical',
	'init_long'=>$params->get('init_x'), 'init_lat'=>$params->get('init_y'), 'init_zoom'=>9, 'height'=>420),
data_entry_helper::get_from_session('geom')
); ?>
<br/>
<input class="auto" type="submit" value="<?php echo JText::_('Next'); ?>" />
</fieldset>
</form>