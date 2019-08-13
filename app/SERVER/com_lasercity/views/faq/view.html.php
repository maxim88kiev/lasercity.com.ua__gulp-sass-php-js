<?php defined('_JEXEC') or die;

class LasercityViewfaq extends JViewLegacy
{
    public $items = array();
    
    function display($tpl = null)
    {
        $this->document->setTitle('Вопросы и ответы');
        $this->items = $this->get('Items');
        return parent::display($tpl);
    }

}