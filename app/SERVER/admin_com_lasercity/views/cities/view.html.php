<?php defined('_JEXEC') or die;

jimport('helpers.libraries.view_legacy', JPATH_COMPONENT_ADMINISTRATOR);

class LaserCityViewCities extends ViewLegacy
{
    function setData()
    {
        $this->add_filters = true;
        $this->title = 'COM_LASERCITY_VIEW_NAME_CITIES';
        $this->tool_bar = 'list';
    }

    function setTableHEAD()
    {
        $this->getTH('order');
        $this->getTH('text', '№');
        $this->getTH('check-all');
        $this->getTH('sort', 'JSTATUS', 'published');
        $this->getTH('sort', 'COM_LAVERCITY_TABLE_TITLE', 'title', 30);
        $this->getTH('sort', 'COM_LAVERCITY_TABLE_ALIAS', 'alias', 30);
        //$this->getTH('sort', 'COM_LAVERCITY_TABLE_DEFAULT_LANGUAGE', 'default_language', 30);
        $this->getTH('sort', 'ID', 'id');
    }

    function setTableBODY($i, $item)
    {
        $this->getTD('order', $item->ordering);
        $this->getTD('text', $i + 1);
        $this->getTD('check', $item->id, $i);
        $this->getTD('publish', $item->published, $i);
        $this->getTD('link', $item->title, $item->id);
        $this->getTD('text', $item->alias);
        //$this->getTD('text', $item->default_language);
        $this->getTD('text', $item->id);
    }
}