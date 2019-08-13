<?php defined('_JEXEC') or die;

class lasercityControllerdistricts extends JControllerAdmin
{
    protected $view_list = 'districts';

    public function getModel($name = 'district', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}