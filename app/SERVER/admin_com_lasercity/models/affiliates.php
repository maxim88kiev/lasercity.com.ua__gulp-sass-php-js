<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelaffiliates extends ModelList
{
    public $filter_fields = array(
        'id', 'published', 'title', 'ordering', 'lang_city'
    );

    protected function getListQuery()
    {
        return $this->setQuery('SELECT * FROM `#__lasercity_affiliates`', true, array(array(
            'as' => 'title',
            'join' => 'id',
            'name' => 'affiliate'
        ), array(
            'as' => 'lang_city',
            'join' => 'city',
            'name' => 'citie'
        )));
    }
}