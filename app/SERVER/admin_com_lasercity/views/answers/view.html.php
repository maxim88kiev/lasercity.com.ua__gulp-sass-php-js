<?php defined('_JEXEC') or die;

jimport('helpers.libraries.view_legacy', JPATH_COMPONENT_ADMINISTRATOR);

class LaserCityViewAnswers extends ViewLegacy
{
    function setData()
    {
        $this->add_filters = true;
        $this->title = 'COM_LASERCITY_VIEW_NAME_ANSWERS';
        $this->tool_bar = 'list';
    }

    function setTableHEAD()
    {
        //$this->getTH('order');
        $this->getTH('text', '№');
        $this->getTH('check-all');
        $this->getTH('sort', 'JSTATUS', 'published');
        $this->getTH('sort', 'answer', 'answer');
        $this->getTH('sort', 'Дата добавления', 'date_added');
        $this->getTH('sort', 'ID', 'id');
    }

    function setTableBODY($i, $item)
    {
        //$this->getTD('order', $item->ordering);
        $this->getTD('text', $i + 1);
        $this->getTD('check', $item->id, $i);
        $this->getTD('publish', $item->published, $i);
        $this->getTD('link', $item->answer, $item->id);
        $this->getTD('text', $item->date_added);
        $this->getTD('text', $item->id);
    }
}