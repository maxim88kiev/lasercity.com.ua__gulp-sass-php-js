<?php defined('_JEXEC') or die;

class LaserCityControllerStocks extends JControllerAdmin
{
    protected $view_list = 'stocks';

    public function getModel($name = 'Stock', $prefix = 'LaserCityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}