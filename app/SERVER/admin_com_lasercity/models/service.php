<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_admin', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelservice extends ModelAdmin
{
    public $config = array(
        'translator' => array(
            'title',
            'description'
        ),
        'object' => array(
            'zones' => array(
                'columns' => array(
                    'zone'
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