<?php

//defined(_JEXEC) or die('Restricted Access');
jimport('joomla.application.component.view');

class IndiciaViewMapPicker extends JView {

	function display()
	{
		global $mainframe;
		require_once(JPATH_COMPONENT.DS.'helpers'.DS.'prefix.php');
		$this->assign('nextUri','index.php?option=com_indicia&view=otherdata&Itemid=' .
				JRequest::getCmd('Itemid'));

		parent::display();

		require_once(JPATH_COMPONENT.DS.'helpers'.DS.'suffix.php');
		require_once(JPATH_COMPONENT.DS.'helpers'.DS.'disable_map_scroll_wheel.php');
	}

}

?>
