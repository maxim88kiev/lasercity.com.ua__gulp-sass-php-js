<?php defined('_JEXEC') or die;

class LaserCityController extends JControllerLegacy
{
    protected $default_view = 'cities';

    public function display($cachable = false, $urlparams = array())
    {
        // Устанавливаем представление по умолчанию, если оно не было установлено.
        $input = JFactory::getApplication()->input;
        $input->set('view', $input->getCmd('view', $this->default_view));

        parent::display($cachable);
    }

    function getMicroDistricts()
    {
        //LaserCityHelper::getAjaxJSON('district', '#__lasercity_micro_districts', 'micro_district');
        $data = LaserCityHelper::getTranslatedJSON('district', '#__lasercity_micro_districts', 'micro_district');
        LaserCityHelper::showJSON($data);
    }

    function getMetro()
    {
        //LaserCityHelper::getAjaxJSON('city', '#__lasercity_metro', 'metro');
        $data = LaserCityHelper::getTranslatedJSON('city', '#__lasercity_metro', 'metro');
        LaserCityHelper::showJSON($data);
    }

    function getDistricts()
    {
        //LaserCityHelper::getAjaxJSON('city', '#__lasercity_districts', 'district');
        $data = LaserCityHelper::getTranslatedJSON('city', '#__lasercity_districts', 'district');
        LaserCityHelper::showJSON($data);
    }

    function getLocations()
    {
        $data = LaserCityHelper::getTranslatedJSON('city', '#__lasercity_locations', 'location', ', `type`');
        foreach ($data as $id => $option) {
            $data[$id]->text = JText::_('COM_LASERCITY_TABLE_' . mb_strtoupper($option->type) . '_S') . ' ' . $option->text;
        }
        LaserCityHelper::showJSON($data);
    }
}