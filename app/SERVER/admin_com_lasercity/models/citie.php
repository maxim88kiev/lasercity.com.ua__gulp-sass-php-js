<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_admin', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelcitie extends ModelAdmin
{
    public $config = array(
        'translator' => 'title',
        'sef_url' => array(
            'column' => 'alias',
            'space' => '_'
        ),
        'check_alias' => array(
            'column' => 'alias'
        )
    );
}