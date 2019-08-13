<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_admin', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelorganization extends ModelAdmin
{
    public function __construct($config = array())
    {
        $dimensions = array(
            array(
                'width' => 262,
                'height' => 198,
                'quality' => 55
            ),
            array(
                'width' => 90,
                'height' => 68,
                'quality' => 55
            )
        );
        $this->config['convert_image'] = array(
            'logo' => array(
                'dimensions' => $dimensions,
                'formats' => ['jpg', 'webp'],
                'retina' => true
            )
        );
        parent::__construct($config);
    }
    public $config = array(
        'check_image' => array(
            'logo'
        ),
        'translator' => array('title', 'description', 'type'),
        'object' => array(
            'social_networks' => array(
                'columns' => array('url')
            ), 'key_value' => false
        ),
        'sef_url' => array(
            'column' => 'alias',
            'space' => '_'
        ),
        'check_alias' => array(
            'column' => 'alias'
        ),
        'dir_images' => array(
            'columns' => array('logo')
        )
    );
}