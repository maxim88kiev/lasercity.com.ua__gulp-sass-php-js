<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class LaserCityModelReviews extends ModelList
{
    public $filter_fields = array(
        'published', 'name', 'email', 'koment', 'phone', 'date_added', 'id'
    );

    protected function getListQuery()
    {
        return $this->setQuery('SELECT * FROM (SELECT *, `name` as `title` FROM `#__lasercity_reviews`) as `table`', false, array(''));
    }
}