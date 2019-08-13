<?php defined('_JEXEC') or die;

class lasercityControllerapparats extends JControllerAdmin
{
    protected $view_list = 'apparats';

    public function getModel($name = 'apparat', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}