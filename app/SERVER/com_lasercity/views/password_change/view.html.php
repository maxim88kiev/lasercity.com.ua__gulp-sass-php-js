<?php defined('_JEXEC') or die;

class LasercityViewpassword_change extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->document->setTitle(JText::_('COM_LASERCITY_DONT_KNOW_PASSWORD_SMENA'));
        return parent::display($tpl);
    }

}