<?php defined('_JEXEC') or die;

class lasercityViewfaq_item extends JViewLegacy
{
    public $item = null;
    public $answers = array();

    function display($tpl = null)
    {

        $this->item = $this->get('Item');
        $this->item->description = strip_tags($this->item->description);
        $this->document->setTitle($this->item->title);
        $this->document->setDescription($this->item->head_description);

        $this->answers = lasercityModelfaq_item::getAnswers($this->item->id);

        $params = OpenGraph::loadParams('faq', $this->item->id);
        OpenGraph::autoSet($params);
        return parent::display($tpl);
    }
}
