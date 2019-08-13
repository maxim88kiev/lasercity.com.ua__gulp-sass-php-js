<?php defined('_JEXEC') or die;

class LasercityViewarticles extends JViewLegacy
{
    public $items = array();

    function display($tpl = null)
    {
        $this->document->setTitle('Полезние материалы');
        $this->items = $this->get('Items');
        return parent::display($tpl);
    }
}