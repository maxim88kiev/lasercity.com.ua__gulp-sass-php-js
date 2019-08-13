<?php defined('_JEXEC') or die;

class LasercityViewhome extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->document->setTitle(JText::_('COM_LASERCITY_TITLE_HOME'));
        $this->prices = $this->get('FilterServices');

        /*JHtml::_('script', 'templates/lasercity/js/index.js', array('version' => 'auto'), array('defer' => 'defer'));
        JHtml::_('script', 'templates/lasercity/js/filter.js', array('version' => 'auto'), array('defer' => 'defer'));*/
//        JHtml::_('script', 'templates/lasercity/js/scroller.js', array('version' => 'auto'));
        return parent::display($tpl);
    }

}