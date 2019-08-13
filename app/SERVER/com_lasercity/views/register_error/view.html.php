<?php defined('_JEXEC') or die;

class LasercityViewregister_error extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->document->setTitle('Ошибка при регестрации');
        return parent::display($tpl);
    }

}