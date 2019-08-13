<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_admin', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelstock extends ModelAdmin
{
    public function __construct($config = array())
    {
        $dimensions = array(
            array(
                'width' => 1596,
                'height' => 734,
                'quality' => 55
            ),
            array(
                'width' => 798,
                'height' => 367,
                'quality' => 55
            ),
            array(
                'width' => 716,
                'height' => 516,
                'quality' => 55
            ),
            array(
                'width' => 640,
                'height' => 300,
                'quality' => 55
            ),
            array(
                'width' => 524,
                'height' => 396,
                'quality' => 55
            ),
            array(
                'width' => 358,
                'height' => 258,
                'quality' => 55
            ),
            array(
                'width' => 320,
                'height' => 150,
                'quality' => 55
            ),
            array(
                'width' => 262,
                'height' => 198,
                'quality' => 55
            )
        );
        $this->config['convert_image'] = array(
            'image' => array(
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
            'image'
        ),
        'translator' => array('title', 'description', 'head_description', 'conditions'),
        'object' => array(
            'images' => array(
                'columns' => array(
                    'images'
                ), 'key_value' => false
            ),
            'affiliate_id' => array(
                'columns' => array(
                    'affiliate_id'
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
            'columns' => array('image')
        )
    );

    public function save($data)
    {
        file_put_contents('test', $data['date_added'] . ' ' . $data['date_remove']);
        return parent::save($data);
    }
}