<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelmetros extends ModelList
{
    public $filter_fields = array(
        'published', 'title', 'lang_city', 'lang_district', 'id', 'alias', 'line'
    );

    protected function getListQuery()
    {
        return $this->setQuery('SELECT * FROM `#__lasercity_metro`', true, array(array(
            'join' => 'id',
            'name' => 'metro',
            'as' => 'title'
        ), array(
            'join' => 'city',
            'name' => 'citie',
            'as' => 'lang_city'
        ), array(
            'join' => 'district',
            'name' => 'district',
            'as' => 'lang_district'
        )));
    }
}