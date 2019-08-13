<?php defined('_JEXEC') or die;

class LasercityViewregister_step extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->document->setTitle(JText::_('COM_LASERCITY_REGISTER_BY_STEP_TITLE'));
        return parent::display($tpl);
    }

}