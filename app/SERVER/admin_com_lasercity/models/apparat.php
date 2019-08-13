<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_admin', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelapparat extends ModelAdmin
{
    public $config = array(
        'translator' => array(
            'description'
        ),
        'object' => array(
            'images' => array(
                'columns' => array(
                    'image'
                ), 'key_value' => false
            )
        ),
        'sef_url' => array(
            'column' => 'alias',
            'space' => '_'
        ),
        'check_alias' => array(
            'column' => 'alias'
        )
	);
}