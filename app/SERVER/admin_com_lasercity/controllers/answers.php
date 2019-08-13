<?php defined('_JEXEC') or die;

class LaserCityControllerAnswers extends JControllerAdmin
{
    protected $view_list = 'Answers';

    public function getModel($name = 'Answer', $prefix = 'LaserCityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}