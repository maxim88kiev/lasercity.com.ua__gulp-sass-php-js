<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_admin', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelaffiliate extends ModelAdmin
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
            ), 'main_image' => array(
                'dimensions' => $dimensions,
                'formats' => ['jpg', 'webp'],
                'retina' => true
            ), 'images' => array(
                'dimensions' => $dimensions,
                'formats' => ['jpg', 'webp'],
                'retina' => true
            )
        );
        parent::__construct($config);
    }

    public $config = array(
        'open-graph' => array(
            'og:image',
            'og:title',
            'og:description'
        ),
        'check_image' => array(
            'main_image', 'logo'
        ),
        'translator' => array(
            'title',
            'type',
            'head_description',
            'description'
        ),
        'object' => array(
            'phones' => array(
                'columns' => array(
                    'phone'
                ), 'key_value' => false
            ), 'comforts' => array(
                'columns' => array(
                    'comfort'
                ), 'key_value' => false
            ),
            'apparats' => array(
                'columns' => array(
                    'apparat'
                ), 'key_value' => false
            ),
            'prices' => array(
                'columns' => array(
                    'price'
                ), 'key_value' => false
            ),
            'schedule' => array(
                'columns' => array(
                    'monday', 'tuesday', 'wednesday', 'thursday',
                    'friday', 'saturday', 'sunday', 'time'
                ), 'key_value' => false
            ),
            'images' => array(
                'columns' => array(
                    'image'
                ), 'key_value' => false
            ),
            'social_networks' => array(
                'columns' => array(
                    'url'
                ), 'key_value' => false
            )
        ),
        'sef_url' => array(
            'column' => 'alias',
            'space' => '_'
        ),
        'check_alias' => array(
            'column' => 'alias'
        ),
        'dir_images' => array(
            'columns' => array('logo', 'images')
        )
    );
}