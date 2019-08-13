<?php defined('_JEXEC') or die;

class LasercityViewservises_record extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->document->setTitle('Запись на услуги');
        return parent::display($tpl);
    }

}