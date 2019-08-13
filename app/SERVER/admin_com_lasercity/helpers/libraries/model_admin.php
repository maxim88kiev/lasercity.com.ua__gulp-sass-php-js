<?php defined('_JEXEC') or die;

jimport('helpers.libraries.open_graph', JPATH_COMPONENT_ADMINISTRATOR);
jimport('helpers.libraries.image_conversions', JPATH_COMPONENT_ADMINISTRATOR);

class ModelAdmin extends JModelAdmin
{
    public $config = array();

    #region Переопределённые функции

    /**
     * Загрузка формы. Значения беруться с название модели
     * @param array $data
     * @param bool $loadData
     * @return bool|JForm
     */
    function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm(
            "{$this->option}.{$this->name}",
            $this->name,
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );

        return empty($form) ? false : $form;
    }

    /**
     * Загрузка таблицы. Значения беруться с название модели
     * @param string $type
     * @param string $prefix
     * @param array $config
     * @return bool|\Joomla\CMS\Table\Table|JTable
     */
    function getTable($type = '', $prefix = '', $config = array())
    {
        $table = str_replace('com_', '', $this->option);
        return JTable::getInstance($this->name, $table . 'Table', $config);
    }

    /**
     * Загрузка значений для формы
     * @return array|bool|JObject
     */
    function loadFormData()
    {
        return $this->getItem();
    }

    function getItem($pk = null)
    {
        if (isset($this->config['translator']) || isset($this->config['object']) || isset($this->config['open-graph'])) {
            $id = $this->getState("{$this->name}.id");
            $item = parent::getItem($pk);
            if ($id != null) {
                if (isset($this->config['translator'])) {
                    Translator::getTranslations($this->name, $this->config['translator'], $id, $item);
                }
                if (isset($this->config['object'])) {
                    $this->getObject($item, $this->config['object']);
                }
                if (isset($this->config['open-graph'])) {
                    $params = OpenGraph::loadParams($this->name, $id);
                    if (!empty($params)) {
                        foreach ($this->config['open-graph'] as $column) {
                            $item->$column = $params[$column];
                        }
                    }
                }
            }
            return $item;
        }
        return parent::getItem($pk);
    }

    function save($data)
    {
        if (isset($this->config['sef_url'])) {
            $data[$this->config['sef_url']['column']] = $this->setAlias($data[$this->config['sef_url']['column']],
                isset($this->config['sef_url']['space']) ? $this->config['sef_url']['space'] : '-');
        }
        if (isset($this->config['dir_images'])) {
            $alias = isset($this->config['dir_images']['alias']) ? $this->config['directoryImages']['alias'] : 'alias';
            $directory = $this->getName() . 's';
            $table_name = $this->getTable()->getTableName();

            $new_dir = $data[$alias];
            $db_old_data = $this->_db->setQuery("SELECT `alias`, `published` FROM `{$table_name}` WHERE `id`={$data['id']}")->loadObject();
            $old_dir = null;
            $do_it = false;
            if ($db_old_data->published) {
                $old_dir = $db_old_data->alias;
                $do_it = true;
            }
            if ($do_it) {
                $item_dir = JPATH_SITE . '/' . "images/{$directory}";

                if (!file_exists($item_dir)) {
                    mkdir($item_dir);
                }

                $path_image_new = "images/{$directory}/{$new_dir}";
                $path_new = JPATH_SITE . '/' . $path_image_new;

                $path_image_old = "images/{$directory}/{$old_dir}";
                $path_old = JPATH_SITE . '/' . $path_image_old;

                if ($old_dir == null) {
                    mkdir($path_new);
                } else {
                    if ($path_new != $path_old) {
                        rename($path_old, $path_new);
                        $columns = (array)$this->config['dir_images']['columns'];
                        foreach ($columns as $column) {
                            if (isset($data[$column])) {
                                file_put_contents('check', json_encode($data[$column]));
                                if (is_array($data[$column])) {
                                    foreach ($data[$column] as $parent_key => $obj_column) {
                                        foreach ($obj_column as $child_key => $maybe_image) {
                                            $data[$column][$parent_key][$child_key] = str_replace($path_image_old, $path_image_new, $data[$column][$parent_key][$child_key]);
                                        }
                                    }
                                } else {
                                    $data[$column] = str_replace($path_image_old, $path_image_new, $data[$column]);
                                }
                            }
                        }
                    } else if (!file_exists($path_new)) {
                        mkdir($path_new);
                    }
                }
            }
        }
        if (isset($this->config['check_alias'])) {
            $check_published = isset($this->config['check_alias']['check_published']) ? $this->config['check_alias']['check_published'] : true;
            $check_column = isset($this->config['check_alias']['check_column']) ? $this->config['check_alias']['check_column'] : '';
            $check_tables = isset($this->config['check_alias']['check_tables']) ? $this->config['check_alias']['check_tables'] : array();
            if ($this->checkAlias(
                    $data,
                    $this->config['check_alias']['column'],
                    $check_published, $check_column, $check_tables) && $data['published']) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_LASERCITY_WARNING_REMOVE_FROM_THE_POST'), 'warning');
                $data['published'] = 0;
            }
        }
        if (isset($this->config['object'])) {
            $this->setObject($data, $this->config['object']);
        }
        if (isset($this->config['convert_image'])) {
            $images = array();

            foreach ($this->config['convert_image'] as $key => $item) {
                if (isset($data[$key])) {
                    if ($this->isJson($data[$key])) {
                        foreach (json_decode($data[$key]) as $datum) {
                            $images[$key]['images'][] = JPATH_SITE . '/' . $datum;
                        }
                    } else {
                        $images[$key]['images'][] = JPATH_SITE . '/' . $data[$key];
                    }
                }
                $images[$key]['dimensions'] = $item['dimensions'];
                $images[$key]['formats'] = $item['formats'];
                $images[$key]['retina'] = $item['retina'];
            }

            foreach ($images as $datum) {
                foreach ($datum['images'] as $image) {
                    $imageConversion = new ImageConversion($image);
                    $imageConversion->convert($datum['dimensions'], $datum['formats'], $datum['retina']);
                }
            }
        }
        if (isset($this->config['check_image'])) {
            foreach ($this->config['check_image'] as $key) {
                if ($data[$key] == '') {
                    $data[$key] = 'images/placehold.jpg';
                }
            }
        }
        if (isset($this->config['open-graph'])) {
            $openGraphParams = array();
            foreach ($this->config['open-graph'] as $column) {
                $openGraphParams[$column] = $data[$column];
            }
            OpenGraph::save($this->name, $data['id'], $openGraphParams);
        }
        if (isset($this->config['translator'])) {
            if (parent::save($data)) {
                $id = $this->getState("{$this->name}.id");
                $data['id'] = $id;
                Translator::saveTranslations($data, $this->name, $this->config['translator']);
                return true;
            }
            return false;
        }
        return parent::save($data);
    }

    function delete(&$pks)
    {
        if (isset($this->config['translator'])) {
            if (parent::delete($pks)) {
                Translator::deleteTranslations($pks, $this->name);
                return true;
            }
            return false;
        }
        return parent::delete($pks);
    }

    function publish(&$pks, $value = 1)
    {
        if (isset($this->config['check_alias'])) {
            $check_published = isset($this->config['check_alias']['check_published']) ? $this->config['check_alias']['check_published'] : true;
            $check_column = isset($this->config['check_alias']['check_column']) ? $this->config['check_alias']['check_column'] : '';
            $check_tables = isset($this->config['check_alias']['check_tables']) ? $this->config['check_alias']['check_tables'] : array();
            $this->setPublished($this->config['check_alias']['column'], $pks, $value, $check_published, $check_column, $check_tables);
            return true;
        }
        return parent::publish($pks, $value);
    }
    #endregion;

    #region Кастомные функции
    /**
     * Функция для нормализации слов (Например еслли в строке есть слово "Новый" и если будква "Н" английская то оно удалит ее из слова)
     * @param $str
     * @return string
     */
    function replaceKyLat($str)
    {
        $run_array = explode(' ', $str);
        $out_array = array();

        foreach ($run_array as $word) {
            $mask = array(
                'cyrillic' => preg_replace('/[^а-яА-ЯёЁ.-]/u', '', $word),
                'latin' => preg_replace('/[^a-zA-Z.-]/u', '', $word),
                'symbol' => preg_replace("/[^0-9()%№.,\'-]/u", '', $word)
            );

            $counts = array(
                'cyrillic' => mb_strlen($mask['cyrillic']),
                'latin' => mb_strlen($mask['latin']),
                'symbol' => mb_strlen($mask['symbol'])
            );

            $max = -1;
            $type = '';

            foreach ($counts as $key => $count) {
                if ($count > $max) {
                    $max = $count;
                    $type = $key;
                }
            }

            $out_array[] = $mask[$type];
        }

        return implode(' ', $out_array);
    }

    /**
     * Функция для приведения строки к виду алиаса
     * @param $str - любая строка
     * @param string $space - символ который заменит "-"
     * @return mixed|string
     */
    function setAlias($str, $space = '-')
    {
        $str = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe($str);
        if ($space != '-') {
            $str = str_replace('-', $space, $str);
        }
        return $str;
    }

    function checkAlias(&$obj, $column, $check_published = true, $check_column = '', $tables = array())
    {
        $table = $this->getTable();
        $current_table_name = $table->getTableName();
        if (empty($tables)) {
            $db_name = JFactory::getApplication()->get('db');
            $prefix = JFactory::getApplication()->get('dbprefix');
            $component = str_replace('com_', '', $this->option);
            $query = "SELECT DISTINCT TABLE_NAME as `name` FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME LIKE '{$prefix}{$component}%' AND COLUMN_NAME = '{$column}' AND TABLE_SCHEMA='{$db_name}'";
            $this->_db->setQuery($query);
            $tables_list = $this->_db->loadObjectList();
            if (!empty($tables_list)) {
                foreach ($tables_list as $table) {
                    $table_name = str_replace($prefix, '#__', $table->name);
                    $tables[] = $table_name;
                }
            }
        }
        $value = $obj[$column];
        $value = $this->_db->quote($value);
        $query = array();
        $id = $obj['id'];
        $check_published = $check_published ? ' AND `published`' : '';
        $check_value = is_numeric($obj[$check_column]) ? $obj[$check_column] : $this->_db->quote($obj[$check_column]);
        $check_column = $check_column != '' ? " AND `{$check_column}`={$check_value}" : '';
        foreach ($tables as $table) {
            $not_id = $table == $current_table_name && is_numeric($id) ? " AND `id`<>{$id}" : '';
            $check_column_q = $table == $current_table_name ? $check_column : '';
            $query[] = "(SELECT COUNT(*) as `count` FROM `$table` WHERE `{$column}`={$value}{$not_id}{$check_column_q}{$check_published})";
        }
        $query = implode(' UNION ALL ', $query);
        $this->_db->setQuery($query);
        $counts = $this->_db->loadObjectList();
        $res = 0;
        foreach ($counts as $count) {
            $res += $count->count;
        }
        return $res > 0 ? true : false;
    }

    /**
     * Запрежает публикацию записей с однаковыми значениями
     * Ножно вызивать в переопределённой функции publish и вертать true
     * @param $column - Колонка по которой сравнивать
     * @param $pks
     * @param $value
     * @param bool $check_published
     * @param string $check_column
     * @param array $tables
     * @throws Exception
     */
    function setPublished($column, $pks, $value, $check_published = true, $check_column = '', $tables = array())
    {
        $dispatcher = \JEventDispatcher::getInstance();
        \JPluginHelper::importPlugin($this->events_map['change_state']);
        $pks = (array)$pks;
        $table = $this->getTable()->getTableName();
        $count_unpublished = 0;
        if (!empty($pks)) {
            $check_text = $check_column != '' ? ",`{$check_column}`" : '';
            foreach ($pks as $pk) {
                $query = "SELECT `id`,`{$column}`{$check_text},`published` FROM `{$table}` WHERE `id`={$pk}";
                $this->_db->setQuery($query);
                $obj = $this->_db->loadAssoc();
                if ($this->checkAlias($obj, $column, $check_published, $check_column, $tables)) {
                    $query = $this->_db->getQuery(true)->update("`{$table}`")->set('`published`=0')->where("`id`={$pk}");
                    $this->_db->setQuery($query)->execute();
                    if ($obj->published == 0 && $value == 1) {
                        $count_unpublished++;
                    }
                } else {
                    $query = $this->_db->getQuery(true)->update("`{$table}`")->set("`published`={$value}")->where("`id`={$pk}");
                    $this->_db->setQuery($query)->execute();
                }
            }
        }
        if ($count_unpublished > 0) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_LASERCITY_WARNING_DUPLICATES') . ' ' . $count_unpublished, 'warning');
        }
        $context = $this->option . '.' . $this->name;
        $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));
    }

    /**
     * Приведение данных из бд для repeatable
     * Визывать можно в getItem
     * @param $object
     * @param $fields_data
     */
    function getObject(&$object, $fields_data)
    {
        foreach ($fields_data as $name => $field_data) {
            if (isset($object->{$name})) {
                if ($this->isJson($object->{$name})) {
                    $key_value = isset($field_data['key_value']) ? $field_data['key_value'] : true;
                    $field = json_decode($object->{$name});
                    if ($key_value) {
                        $object->{$name} = $field;
                    } else {
                        $out_field = array();
                        $columns = isset($field_data['columns']) ? $field_data['columns'] : $field_data;
                        if(is_array($field)) {
                            foreach ($field as $key => $value) {
                                $field[$key] = (array)$value;
                                $std_field = array();
                                foreach ($columns as $id => $column) {
                                    $std_field[$column] = $field[$key][$id];
                                }
                                $out_field[] = $std_field;
                            }
                        }
                        $object->{$name} = $out_field;
                    }
                }
            }
        }
    }

    /**
     * Приведения данних из repeatable в формат сохранения в бд
     * При $key_value true данние будут сохраняться в масиве json - нов
     * При $key_value false данние будут сохраняться в масиве масивов
     * Вызывать можно в save
     * @param $object
     * @param $fields_data
     */
    function setObject(&$object, $fields_data)
    {
        foreach ($fields_data as $name => $field_data) {
            $key_value = isset($field_data['key_value']) ? $field_data['key_value'] : true;
            $field = $object[$name];
            if ($key_value) {
                $to_encode = array();
                foreach ($field as $item) {
                    $to_encode[] = $item;
                }
                $object[$name] = json_encode($to_encode);
            } else {
                $to_encode = array();
                $columns = isset($field_data['columns']) ? $field_data['columns'] : $field_data;
                foreach ($field as $key => $value) {
                    $field[$key] = (array)$value;
                    $array = array();
                    foreach ($columns as $id => $column) {
                        $array[] = is_numeric($field[$key][$column]) ? (int)$field[$key][$column] : $field[$key][$column];
                    }
                    if (!empty($array)) {
                        if (count($array) == 1) {
                            $implode = implode($array);
                            $value = is_numeric($implode) ? (int)$implode : $implode;
                            $to_encode[] = $value;
                        } else {
                            $to_encode[] = $array;
                        }
                    }
                }
                $object[$name] = json_encode($to_encode);
            }
        }
    }

    /**
     * Проверка строки на JSON
     * @param $string
     * @return bool
     */
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    #endregion;
}