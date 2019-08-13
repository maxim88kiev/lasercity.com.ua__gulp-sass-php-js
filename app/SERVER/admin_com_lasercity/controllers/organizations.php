<?php defined('_JEXEC') or die;

class lasercityControllerorganizations extends JControllerAdmin
{
    protected $view_list = 'organizations';

    public function getModel($name = 'organization', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}