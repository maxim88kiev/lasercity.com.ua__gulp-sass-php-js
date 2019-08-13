<?php defined('_JEXEC') or die;

class LasercityViewrecords_salon extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->document->setTitle('Заявки организации');
        return parent::display($tpl);
    }

}