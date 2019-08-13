<?php defined('_JEXEC') or die;

class lasercityControllerfaqs extends JControllerAdmin
{
    protected $view_list = 'faqs';

    public function getModel($name = 'faq', $prefix = 'lasercityModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}