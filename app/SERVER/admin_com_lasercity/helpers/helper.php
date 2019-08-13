<?php defined('_JEXEC') or die;

class LaserCityHelper
{
    public static function import($json, $translate, $from_tables, $table, $object_name)
    {
        $db = JFactory::getDbo();

        $json_data = json_decode($json, true);

        foreach ($json_data as $item) {
            $to_table = array();
            $from_table = array();
            $to_translate = array();
            foreach ($item as $key => $info) {
                if (isset($from_tables[$key])) {
                    $from_table[] = $key;
                } else if (isset($translate[$key])) {
                    $to_translate[$key] = $translate[$key];
                } else {
                    $to_table[] = $key;
                }
            }

            if (!empty($from_table)) {
                foreach ($from_table as $column) {
                    $item[$column] = $db->setQuery("SELECT `id` FROM `{$from_tables[$column]}` WHERE `alias`='{$item[$column]}' LIMIT 1")->loadResult();
                }
            }
            if (!empty($to_table)) {
                $obj = new stdClass();
                foreach ($from_table as $column) {
                    $to_table[] = $column;
                }
                foreach ($to_table as $column) {
                    $obj->$column = $item[$column];
                }
                $obj->published = 1;
                $db->insertObject($table, $obj);
                if (!empty($to_translate)) {
                    $insert_id = $db->insertid();
                    foreach ($to_translate as $column => $info) {
                        Translator::saveTranslate($info['lang_code'], $object_name, $info['object_column'], $insert_id, $item[$column]);
                    }
                }
            }
        }
    }

    public static function getTranslatedJSON($column, $table, $name, $add_columns = null)
    {
        $value = JFactory::getApplication()->input->get($column);
        $db = JFactory::getDbo();
        $result = array();
        if (is_numeric($value)) {
            $lang = JFactory::getLanguage()->getTag();
            $query = Translator::setQuery("SELECT `id` as `value`{$add_columns} FROM `{$table}` WHERE `{$column}`={$value} AND `published`", $lang, array(array(
                'as' => 'text',
                'join' => 'value',
                'name' => $name
            )));
            $db->setQuery($query);
            $result = $db->loadObjectList();
        }
        return $result;
    }

    public static function showJSON($data)
    {
        $input = JFactory::getApplication()->input;
        echo new JResponseJson($data, null, false, $input->get('ignoreMessages', true, 'bool'));
        jexit();
    }
}