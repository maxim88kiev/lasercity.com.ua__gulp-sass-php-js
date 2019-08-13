<?php defined('_JEXEC') or die;

class LasercityViewregister_success extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->document->setTitle('Регестрация пройшла успешно');
        return parent::display($tpl);
    }

}