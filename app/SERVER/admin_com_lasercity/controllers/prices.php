<?php defined('_JEXEC') or die;

class lasercityControllerprices extends JControllerAdmin
{
    protected $view_list = 'prices';

    public function getModel($name = 'price', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}