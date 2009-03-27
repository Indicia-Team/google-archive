<script type="text/javascript">

jQuery(document).ready(function() {
	jQuery('.species_radio').hide();
	jQuery('.species_anchor').click(function() {
    	jQuery(this).prev().attr('checked', true);
    	jQuery(this).parent().css('background-color', '#ffffaa');
    	jQuery(this).parent().siblings().css('background-color', '#ededed');
	});
});
</script>
<?php
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'data_entry_helper.php');
$params =& $mainframe->getPageParameters('com_indicia');
?>
<h1><?php echo $params->get('title'); ?></h1>
<p><?php echo JText::_($params->get('intro')); ?></p>
<form class="indicia" method="POST" action="<?php echo $this->nextUri; ?>" >
<fieldset><legend><?php echo JText::_('Select the species'); ?></legend>
<label for="actaxa_taxon_list_id"><?php echo JText::_('Species'); ?>:</label>
<?php echo $this->species_picker; ?>
<br />
<br />
<input class="auto" type="submit" value="<?php echo JText::_('Next'); ?>"/>
</fieldset>
</form>