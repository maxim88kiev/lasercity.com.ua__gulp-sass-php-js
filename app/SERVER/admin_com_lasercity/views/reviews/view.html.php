<?php defined('_JEXEC') or die;

jimport('helpers.libraries.view_legacy', JPATH_COMPONENT_ADMINISTRATOR);

class LaserCityViewReviews extends ViewLegacy
{
    function setData()
    {
        $this->add_filters = true;
        $this->title = 'COM_LASERCITY_VIEW_NAME_REVIEWS';
        $this->tool_bar = 'list';
    }

    function setTableHEAD()
    {
        //$this->getTH('order');
        $this->getTH('text', '№');
        $this->getTH('check-all');
        $this->getTH('sort', 'JSTATUS', 'published');
        $this->getTH('sort', 'Имя', 'name');
        $this->getTH('sort', 'Дата добавления', 'date_added');
        $this->getTH('sort', 'Email', 'email');
        $this->getTH('sort', 'Коментарий', 'koment');
        $this->getTH('sort', 'ID', 'id');
    }

    function setTableBODY($i, $item)
    {
        //$this->getTD('order', $item->ordering);
        $this->getTD('text', $i + 1);
        $this->getTD('check', $item->id, $i);
        $this->getTD('publish', $item->published, $i);
        $this->getTD('link', $item->name, $item->id);
        $this->getTD('text', $item->date_added);
        $this->getTD('text', $item->email);
        $this->getTD('text', $item->koment);
        $this->getTD('text', $item->id);
    }
}