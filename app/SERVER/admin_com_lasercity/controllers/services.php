<?php defined('_JEXEC') or die;

class lasercityControllerservices extends JControllerAdmin
{
    protected $view_list = 'services';

    public function getModel($name = 'service', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}