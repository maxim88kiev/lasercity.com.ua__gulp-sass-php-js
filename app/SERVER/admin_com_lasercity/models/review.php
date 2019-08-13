<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_admin', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelreview extends ModelAdmin
{
    public function __construct($config = array())
    {
        $avatar = array(
            array(
                'width' => 45,
                'height' => 45,
                'quality' => 55
            ),
            array(
                'width' => 90,
                'height' => 90,
                'quality' => 55
            ),
            array(
                'width' => 180,
                'height' => 180,
                'quality' => 55
            )
        );
        $fotos = array(
            array(
                'width' => 82,
                'height' => 63,
                'quality' => 55
            ),
            array(
                'width' => 105,
                'height' => 80,
                'quality' => 55
            ),
            array(
                'width' => 164,
                'height' => 126,
                'quality' => 55
            ),
            array(
                'width' => 210,
                'height' => 160,
                'quality' => 55
            )
        );
        $this->config['convert_image'] = array(
            'avatar' => array(
                'dimensions' => $avatar,
                'formats' => ['jpg', 'webp'],
                'retina' => true
            ), 'foto' => array(
                'dimensions' => $fotos,
                'formats' => ['jpg', 'webp'],
                'retina' => true
            )
        );
        parent::__construct($config);
    }
    public $config = array(
        'check_image' => array(
            'avatar'
        ),
        //'translator' => array('title', 'description', 'head_description', 'conditions'),
        'translator' => array('title', 'description', 'head_description', 'conditions'),
        'object' => array(
            'foto' => array(
                'columns' => array(
                    'foto'
                ), 'key_value' => false
            )
        ),

        /*'sef_url' => array(
            'column' => 'alias',
            'space' => '_'
        ),
        'check_alias' => array(
            'column' => 'alias'
        ),*/
        'dir_images' => array(
            'columns' => array('avatar')
        )
    );
}