<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_list', JPATH_COMPONENT_ADMINISTRATOR);

class LaserCityModelCities extends ModelList
{
    public $filter_fields = array(
        'id', 'published', 'title', 'alias'/*, 'default_language'*/, 'ordering'
    );

    function getListQuery()
    {
        return $this->setQuery('SELECT `city`.`id`,
  `city`.`published`,
  `city`.`ordering`,
  `lang`.`title_native` as `default_language`,
  `city`.`alias` 
FROM `#__lasercity_cities` as `city`
LEFT JOIN `#__languages` as `lang` ON `lang`.`lang_code`=`city`.`default_language`', true, array(array(
            'as' => 'title',
            'join' => 'id',
            'name' => 'citie'
        )));
    }
}