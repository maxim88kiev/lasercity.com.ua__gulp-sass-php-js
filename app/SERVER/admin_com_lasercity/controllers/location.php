<?php defined('_JEXEC') or die;

class lasercityControllerlocation extends JControllerForm {
    protected $view_list = 'locations';

    function getDistricts()
    {
        LaserCityHelper::getAjaxJSON('city', '#__lasercity_districts', 'district');
    }
}