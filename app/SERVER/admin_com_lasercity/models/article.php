<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_admin', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelarticle extends ModelAdmin
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
            'main_image' => array(
                'dimensions'  => $dimensions,
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
            'main_image'
        ),
        'translator' => array(
            'title', 'description', 'head_description'
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