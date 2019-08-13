<?php defined('_JEXEC') or die;

jimport('helpers.libraries.view_legacy', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityViewprice extends  ViewLegacy {
    function setData()
    {
        JFactory::getDocument()->addStyleDeclaration('
            .width100px {
                max-width: 100px!important;
            }
        ');
        $this->title = 'COM_LASERCITY_VIEW_NAME_PRICE';
		$this->tool_bar = 'item';
		$this->column_duplicate = 'alias';
    }
}