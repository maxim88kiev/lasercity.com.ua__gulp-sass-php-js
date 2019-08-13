<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelfaqs extends ModelList
{
    public $filter_fields = array(
        'id', 'published', 'title', 'alias', 'ordering'
    );

    function getListQuery()
    {
        return $this->setQuery('SELECT * FROM `#__lasercity_faq`', true, array(array(
            'as' => 'title',
            'join' => 'id',
            'name' => 'faq'
        )));
    }
}