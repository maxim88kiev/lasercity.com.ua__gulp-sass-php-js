<?php defined('_JEXEC') or die;

class LasercityViewstocks extends JViewLegacy
{
    public $items = array();

    function display($tpl = null)
    {

        /*echo '<pre>';
        print_r(JFactory::$session->get('current_city_id'));
        echo '</pre>';*/
        $this->document->setTitle('Акции');
        $this->items = $this->get('Items');
        return parent::display($tpl);
    }
}