<?php defined('_JEXEC') or die;

class lasercityControllerzones extends JControllerAdmin
{
    protected $view_list = 'zones';

    public function getModel($name = 'zone', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}