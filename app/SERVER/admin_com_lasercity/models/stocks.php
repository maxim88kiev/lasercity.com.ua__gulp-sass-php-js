<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class LaserCityModelStocks extends ModelList
{
    public $filter_fields = array(
        'published', 'title', 'alias', 'discount', 'date_added', 'date_remove', 'ordering', 'id'
    );

    protected function getListQuery()
    {
        return $this->setQuery('SELECT * FROM `#__lasercity_stock` as `table`', true, array(array(
            'as' => 'title',
            'join' => 'id',
            'name' => 'stock'
        )));
    }
}