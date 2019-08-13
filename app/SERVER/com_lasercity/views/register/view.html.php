<?php defined('_JEXEC') or die;

class LasercityViewregister extends JViewLegacy
{
    public $type;

    function display($tpl = null)
    {
        $input = JFactory::$application->input;
        $type = $input->getString('type', 'client');
        if ($type != 'client' && $type != 'organization') {
            $type = 'client';
        }
        $this->type = $type;
        $this->document->setTitle(JText::_('COM_LASERCITY_ALL_FORM_REGISTER_ZAGOL_IN_SITE'));
        return parent::display($tpl);
    }

}