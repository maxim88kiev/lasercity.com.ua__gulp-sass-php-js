<?php defined('_JEXEC') or die;

class LasercityViewrecord_client extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->document->setTitle('Заявки');
        return parent::display($tpl);
    }

}