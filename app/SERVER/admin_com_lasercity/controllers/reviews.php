<?php defined('_JEXEC') or die;

class LaserCityControllerReviews extends JControllerAdmin
{
    protected $view_list = 'reviews';

    public function getModel($name = 'Review', $prefix = 'LaserCityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}