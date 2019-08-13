<?php defined('_JEXEC') or die;

class lasercityViewarticle extends JViewLegacy
{
    public $item = null;

    function display($tpl = null)
    {
        $this->item = $this->get('Item');
        $this->document->setTitle($this->item->title);
        $this->document->setDescription($this->item->head_description);
        $params = OpenGraph::loadParams('article', $this->item->id);
        OpenGraph::autoSet($params);
        return parent::display($tpl);
    }
}
