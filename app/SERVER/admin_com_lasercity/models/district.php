<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_admin', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModeldistrict extends ModelAdmin
{
    public $config = array(
        'translator' => 'title',
        'sef_url' => array(
            'column' => 'alias',
            'space' => '_'
        )
	);
}