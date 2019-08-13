<?php defined('_JEXEC') or die;

class lasercityControlleraffiliates extends JControllerAdmin
{
    protected $view_list = 'affiliates';

    public function getModel($name = 'affiliate', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}