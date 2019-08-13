<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelarticles extends ModelList
{
    public $filter_fields = array(
        'id', 'published', 'title', 'alias', 'ordering'
    );

    function getListQuery()
    {
        return $this->setQuery('SELECT * FROM `#__lasercity_articles`', true, array(array(
            'as' => 'title',
            'join' => 'id',
            'name' => 'article'
        )));
    }
}