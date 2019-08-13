<?php defined('_JEXEC') or die;

class LasercityViewprivacy extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->document->setTitle('Правила и конфиденциальность');
        return parent::display($tpl);
    }

}