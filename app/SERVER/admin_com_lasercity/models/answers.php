<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class LaserCityModelAnswers extends ModelList
{
    public $filter_fields = array(
        'published', 'answer', 'date_added', 'id'
    );

    protected function getListQuery()
    {
        return $this->setQuery('SELECT * FROM (SELECT *, `answer` as `title` FROM `#__lasercity_answer`) as `table`', false, array(''));
    }
}