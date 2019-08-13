<?php defined('_JEXEC') or die;

class lasercityControllercomforts extends JControllerAdmin
{
    protected $view_list = 'comforts';

    public function getModel($name = 'comfort', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}