<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelprices extends ModelList
{
    public $filter_fields = array(
        'id', 'published', 'title', 'lang_affiliate'
    );

    protected function getListQuery()
    {
        return $this->setQuery('SELECT * FROM `#__lasercity_prices`', true, array(array(
            'as' => 'title',
            'join' => 'id',
            'name' => 'price'
        )));
    }
}