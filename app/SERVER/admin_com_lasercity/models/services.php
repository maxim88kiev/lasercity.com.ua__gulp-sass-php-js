<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelservices extends ModelList
{
    public $filter_fields = array(
        'id', 'published', 'title'
    );

    protected function getListQuery()
    {
        return $this->setQuery('SELECT * FROM `#__lasercity_services`', true, array(array(
            'as' => 'title',
            'join' => 'id',
            'name' => 'service'
        )));
    }
}