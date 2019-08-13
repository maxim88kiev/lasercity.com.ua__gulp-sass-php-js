<?php defined('_JEXEC') or die;

class lasercityControllermetros extends JControllerAdmin
{
    protected $view_list = 'metros';

    public function getModel($name = 'metro', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}