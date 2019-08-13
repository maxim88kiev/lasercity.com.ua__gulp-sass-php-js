<?php defined('_JEXEC') or die;

jimport('helpers.libraries.view_legacy', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityViewmetro extends  ViewLegacy {
    function setData()
    {
        $this->title = 'COM_LASERCITY_VIEW_NAME_METRO';
		$this->tool_bar = 'item';
		$this->column_duplicate = 'alias';
    }
}