<?php $params =& $mainframe->getPageParameters('com_indicia'); ?>

<?php
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'data_entry_helper.php');

$data = data_entry_helper::extract_session_array();
$sampleMod = data_entry_helper::wrap($data, 'sample');
$occurrenceMod = data_entry_helper::wrap($data, 'occurrence');
$occurrenceMod['superModels'][] = array(
	'fkId' => 'sample_id',
	'model' => $sampleMod
	);
$submission = array('submission' => array('entries' => array(
	array ( 'model' => $occurrenceMod )
)));
$response = data_entry_helper::forward_post_to('save', $submission);
if (array_key_exists('success', $response)) {
	$mainframe->redirect( 'index.php?option=com_indicia&view=acknowledge&taxa_taxon_list_id='.$data['taxa_taxon_list_id'].'&Itemid='.JRequest::getCmd('Itemid'),
					JText::_($params->get('thank_you')));
} else {
	echo '<p class="error">'.data_entry_helper::dump_errors($response).'</p>';
}


?>