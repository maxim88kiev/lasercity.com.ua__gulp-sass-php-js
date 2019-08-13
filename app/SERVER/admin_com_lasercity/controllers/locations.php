<?php defined('_JEXEC') or die;

class lasercityControllerlocations extends JControllerAdmin
{
    protected $view_list = 'locations';

    public function getModel($name = 'location', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}