<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelmicro_districts extends ModelList
{
    public $filter_fields = array(
        'id', 'published', 'lang_city', 'lang_district', 'title'
    );

    protected function getListQuery()
    {
        return $this->setQuery('SELECT * FROM `#__lasercity_micro_districts`', true, array(array(
            'join' => 'id',
            'name' => 'micro_district',
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