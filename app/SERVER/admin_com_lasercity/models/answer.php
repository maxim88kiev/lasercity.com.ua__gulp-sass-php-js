<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_admin', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelanswer extends ModelAdmin
{
    public function __construct($config = array())
    {
        $dimensions = array(
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

        $this->config['convert_image'] = array(
            'image' => array(
                'dimensions' => $dimensions,
                'formats' => ['jpg', 'webp'],
                'retina' => true)
        );
        parent::__construct($config);
    }
    public $config = array(
        'check_image' => array(
            'image'
        ),
        //'translator' => array('title', 'description', 'head_description', 'conditions'),
        'translator' => array('title', 'description', 'head_description', 'conditions'),
        /*'object' => array(
            'foto' => array(
                'columns' => array(
                    'foto'
                ), 'key_value' => false
            )
        ),*/

        /*'sef_url' => array(
            'column' => 'alias',
            'space' => '_'
        ),
        'check_alias' => array(
            'column' => 'alias'
        ),*/
        'dir_images' => array(
            'columns' => array('image')
        )
    );
}