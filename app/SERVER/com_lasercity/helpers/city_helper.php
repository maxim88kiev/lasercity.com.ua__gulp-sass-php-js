<?php defined('_JEXEC') or die;

class CityHelper
{
    private static $list = null;
    private static $alias = null;

    public static function getList()
    {
        if (self::$list != null) {
            return self::$list;
        }
        $id = JFactory::getApplication()->input->get('current_city_id');
        $db = JFactory::getDbo();
        $query = Translator::setQuery('select * FROM `#__lasercity_cities` as `city` WHERE (SELECT COUNT(*) FROM #__lasercity_affiliates as `t2` WHERE `t2`.`city`=`city`.`id`)', JFactory::getLanguage()->getTag(), array(array(
            'as' => 'title',
            'join' => 'id',
            'name' => 'citie'
        )));
        $db->setQuery($query);

        $cities = $db->loadObjectList();
        $city_current = array();

        foreach ($cities as $key => $city) {
            if ($city->id == $id) {
                $city->current = true;
                $city_current[$key] = $city;
                unset($cities[$key]);
            }
        }

        $cities = array_merge($city_current, $cities);

        self::$list = $cities;

        return $cities;
    }

    public static function getCurrent() {
        if (self::$alias != null) {
            return self::$alias;
        }
        $list = self::$list == null ? self::getList() : self::$list;
        $alias = null;
        foreach ($list as $city) {
            if (isset($city->current)) {
                $alias = $city;
                break;
            }
        }
        self::$alias = $alias;
        return $alias;
    }
}