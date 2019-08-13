<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModellocations extends ModelList
{
    public $filter_fields = array(
        'published', 'title', 'lang_city', 'id', 'type'
    );

    function getListQuery()
    {
        return $this->setQuery('SELECT * FROM `#__lasercity_locations`', true, array(array(
            'join' => 'id',
            'name' => 'location',
            'as' => 'title'
        ), array(
            'join' => 'city',
            'name' => 'citie',
            'as' => 'lang_city'
        )));
    }
}