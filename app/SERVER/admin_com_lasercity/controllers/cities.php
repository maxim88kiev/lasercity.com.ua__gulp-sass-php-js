<?php defined('_JEXEC') or die;

class LaserCityControllerCities extends JControllerAdmin
{
    protected $view_list = 'cities';

    public function getModel($name = 'Citie', $prefix = 'LaserCityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}