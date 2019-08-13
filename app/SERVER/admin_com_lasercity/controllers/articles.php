<?php defined('_JEXEC') or die;

class lasercityControllerarticles extends JControllerAdmin
{
    protected $view_list = 'articles';

    public function getModel($name = 'article', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}