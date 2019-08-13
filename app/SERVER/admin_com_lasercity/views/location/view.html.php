<?php defined('_JEXEC') or die;

jimport('helpers.libraries.view_legacy', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityViewlocation extends ViewLegacy
{
    function setData()
    {
        $this->title = 'COM_LASERCITY_VIEW_NAME_LOCATION';
        $this->tool_bar = 'item';
        $this->column_duplicate = 'alias';
        $this->check_column_duplicate = 'type';
    }
}