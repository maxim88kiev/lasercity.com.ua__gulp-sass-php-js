<?php defined('_JEXEC') or die;

class LasercityViewrecords_client extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->document->setTitle('Мои заявки');
        return parent::display($tpl);
    }

}