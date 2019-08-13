<?php defined('_JEXEC') or die;

class lasercityControllermicro_districts extends JControllerAdmin
{
    protected $view_list = 'micro_districts';

    public function getModel($name = 'micro_district', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}