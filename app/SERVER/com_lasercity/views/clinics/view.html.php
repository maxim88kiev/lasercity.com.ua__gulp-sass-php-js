<?php defined('_JEXEC') or die;

jimport('helpers.search', 'components/com_lasercity');

class lasercityViewclinics extends JViewLegacy
{
    public $items = array();
    public $usersCount = array();

    function display($tpl = null)
    {
        $this->document->setTitle('Список клиник');
        $this->items = $this->get('Items');

        $db = JFactory::getDbo();
        $db->setQuery("SELECT COUNT(*) FROM `#__lasercity_last_visit` WHERE `object_type`='home'");
        $this->usersCount = $db->loadResult();

        return parent::display($tpl); //
    }
}