<?php defined('_JEXEC') or die;

class LasercityViewrecord_salon extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->document->setTitle('Заявки');
        return parent::display($tpl);
    }

}