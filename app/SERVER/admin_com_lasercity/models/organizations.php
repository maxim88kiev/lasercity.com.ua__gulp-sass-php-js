<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelorganizations extends ModelList
{
    public $filter_fields = array(
        'published', 'title', 'id', 'ordering'
    );

    protected function getListQuery()
    {
        return $this->setQuery('SELECT * FROM `#__lasercity_organizations`', true, array(array(
            'as' => 'title',
            'join' => 'id',
            'name' => 'organization'
        )));
    }
}