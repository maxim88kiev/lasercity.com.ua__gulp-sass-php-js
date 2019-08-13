<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModeldistricts extends ModelList
{
    public $filter_fields = array(
        'id', 'published', 'lang_city', 'title'
    );

    protected function getListQuery()
    {
        return $this->setQuery('SELECT * FROM `#__lasercity_districts`', true, array(array(
            'join' => 'id',
            'name' => 'district',
            'as' => 'title'
        ), array(
            'join' => 'city',
            'name' => 'citie',
            'as' => 'lang_city'
        )));
    }
}