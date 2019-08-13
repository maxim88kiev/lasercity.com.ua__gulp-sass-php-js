<?php defined('_JEXEC') or die;

class Translator
{
    private static $initialized = false;
    public static $component = null;
    private static $db = null;

    public static function initialization($component)
    {
        self::$initialized = true;
        self::$component = $component;
        self::$db = JFactory::getDbo();
        $db_name = JFactory::getApplication()->get('db');
        $prefix = JFactory::getApplication()->get('dbprefix');
        $db = JFactory::getDbo();
        $query = 'SELECT `lang_code` FROM `#__languages` WHERE `published`=1';
        $db->setQuery($query);
        $tmp = $db->loadObjectList();
        $languages = array();
        foreach ($tmp as $lang) {
            $languages[] = $lang->lang_code;
        }
        $query = "SHOW TABLES FROM `{$db_name}` LIKE '{$prefix}{$component}_translations_%'";
        $db->setQuery($query);
        $tmp = $db->loadAssocList();
        $tables_lang_code = array();
        foreach ($tmp as $data) {
            foreach ($data as $info) {
                $tables_lang_code[] = str_replace("{$prefix}{$component}_translations_", '', $info);
            }
        }
        foreach ($tables_lang_code as $lang_code) {
            foreach ($languages as $id => $code) {
                if ($lang_code == $code) {
                    unset($languages[$id]);
                    break;
                }
            }
        }
        $to_create = array();
        foreach ($languages as $language) {
            $to_create[] = "#__{$component}_translations_{$language}";
        }
        if (!empty($to_create)) {
            foreach ($to_create as $table_name) {
                $query = "CREATE TABLE `{$table_name}` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `object_name` varchar(32) NOT NULL,
 `object_column` varchar(32) NOT NULL,
 `object_id` int(11) NOT NULL,
 `value` text NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB, DEFAULT CHARSET=utf8";
                $db->setQuery($query)->execute();
            }
            JFactory::getApplication()->enqueueMessage(JText::_('COM_' . mb_strtoupper(self::$component) . '_WARNING_CREATE_TABLES') . implode(', ', $to_create), 'warning');
        }
    }

    public static function saveTranslations($object, $object_name, $object_columns)
    {
        if (self::$initialized) {
            $object_columns = (array)$object_columns;
            foreach ($object_columns as $object_column) {
                $query = 'SELECT `lang_code` FROM `#__languages` WHERE `published`=1';
                self::$db->setQuery($query);
                $tmp = self::$db->loadObjectList();
                $languages = array();
                foreach ($tmp as $lang) {
                    $languages[] = $lang->lang_code;
                }
                $id = $object['id'];
                $toSave = $object[$object_column];
                foreach ($languages as $key) {
                    if (!isset($toSave[$key])) {
                        $toSave[$key] = '';
                    }
                }
                foreach ($toSave as $lang_code => $value) {
                    self::saveTranslate($lang_code, $object_name, $object_column, $id, $value);
                }
                $delete = array();
                foreach ($languages as $language) {
                    if (!mb_strlen($toSave[$language])) {
                        $delete[] = $language;
                    }
                }
                $component_name = self::$component;
                foreach ($delete as $lang) {
                    $table_name = "`#__{$component_name}_translations_{$lang}`";
                    $query = self::$db->getQuery(true)
                        ->delete($table_name)
                        ->where(array(
                            "`object_name`='{$object_name}'",
                            "`object_column`='{$object_column}'",
                            "`object_id`='{$id}'",
                        ));
                    self::$db->setQuery($query)->execute();
                }
            }
        }
    }

    public static function saveTranslate($lang_code, $object_name, $object_column, $object_id, $value)
    {
        if (self::$initialized && $value != null) {
            $component_name = self::$component;
            $table_name = "`#__{$component_name}_translations_{$lang_code}`";
            $query = self::$db->getQuery(true)
                ->select('`id`')
                ->from($table_name)
                ->where("`object_name`='{$object_name}' AND `object_column`='{$object_column}' AND `object_id`='$object_id' LIMIT 1");
            self::$db->setQuery($query);
            $id = self::$db->loadResult();
            if ($id == null) {
                $query = self::$db->getQuery(true)
                    ->insert($table_name)
                    ->columns(array(
                            '`object_name`',
                            '`object_column`',
                            '`object_id`',
                            '`value`'
                        )
                    )->values(implode(',',
                            self::$db->quote(array(
                                    $object_name,
                                    $object_column,
                                    $object_id,
                                    $value
                                )
                            )
                        )
                    );
                self::$db->setQuery($query)->execute();
            } else {
                $query = self::$db->getQuery(true)
                    ->update($table_name)
                    ->set('`value`=' . self::$db->quote($value))
                    ->where("`id`={$id}");
                self::$db->setQuery($query)->execute();
            }
        }
    }

    public static function deleteTranslations($pks, $object_name)
    {
        if (self::$initialized) {
            $pks = (array)$pks;
            $component_name = self::$component;
            $query = 'SELECT `lang_code` FROM `#__languages` WHERE `published`=1';
            self::$db->setQuery($query);
            $tmp = self::$db->loadObjectList();
            $languages = array();
            foreach ($tmp as $lang) {
                $languages[] = $lang->lang_code;
                $table_name = "`#__{$component_name}_translations_{$lang->lang_code}`";
                $query = self::$db->getQuery(true)
                    ->delete($table_name)
                    ->where(array(
                        "`object_name`='{$object_name}'",
                        '`object_id` IN(' . implode(',', $pks) . ')',
                    ));
                self::$db->setQuery($query)->execute();
            }
        }
    }

    public static function getTranslations($object_name, $object_columns, $object_id, &$object)
    {
        if (self::$initialized) {
            $component_name = self::$component;
            $query = 'SELECT `lang_code` FROM `#__languages` WHERE `published`=1';
            self::$db->setQuery($query);
            $tmp = self::$db->loadObjectList();
            $languages = array();
            foreach ($tmp as $lang) {
                $languages[] = $lang->lang_code;
            }
            $object_columns = (array)$object_columns;
            foreach ($object_columns as $object_column) {
                $query = array();
                foreach ($languages as $language) {
                    $query[] = "(SELECT '{$language}' as `lang_code`, `value` as `value` FROM `#__{$component_name}_translations_{$language}` WHERE `object_name`='{$object_name}' AND `object_column`='{$object_column}' AND `object_id`={$object_id} LIMIT 1)";
                }
                $query = implode(' UNION ALL ', $query);
                self::$db->setQuery($query);
                $result = array();
                foreach (self::$db->loadObjectList() as $item) {
                    $result[$item->lang_code] = $item->value;
                }
                if (!empty($result)) {
                    $object->{$object_column} = $result;
                }
            }
        }
    }

    public static function setQuery($query, $lang, $data_columns)
    {
        $component = self::$component;
        $columns = array();
        $data_columns = (array)$data_columns;
        foreach ($data_columns as $object_column) {
            $object_column['object_column'] = isset($object_column['object_column']) ? $object_column['object_column'] : 'title';
            $columns[] = "(SELECT `value` FROM `#__{$component}_translations_{$lang}` WHERE `object_name`='{$object_column['name']}' AND `object_column`='{$object_column['object_column']}' AND `object_id`=`join`.`{$object_column['join']}`) as `{$object_column['as']}`";
        }

        $columns = implode(',', $columns);

        return "SELECT * FROM (SELECT {$columns}, `join`.* FROM ({$query}) as `join`) as `table`";
    }
}