<?php defined('_JEXEC') or die;

/**
 * Класс для фильтра клиник
 * @author Kreng Coder <ihor.lischuk@gmail.com>
 * @version 1.0
 * @since 1.0
 */
class Search
{
    /**
     * Обьект ввода
     * @var \JInput
     * @since 1.0
     */
    private static $input;

    /**
     * Обьект БД
     * @var \JDatabaseDriver
     * @since 1.0
     */
    private static $db;

    /**
     * Указывает лимит на количество елементов
     * @var int
     * @since 1.0
     */
    private static $limit;

    /**
     * Указывает номер текужей страницы
     * @var int
     * @since 1.0
     */
    private static $page;

    /**
     * Язык на котором осуществляеться поиск
     * @var string
     * @since 1.0
     */
    private static $lang;

    /**
     * Информация по последнему поиску
     * @var array
     * @since 1.0
     */
    private static $info;

    /**
     * Тип поиска
     * @var string
     * @since 1.0
     */
    private static $type;

    /**
     * Результат поиска
     * @var string
     * @since 1.0
     */
    private static $result = null;

    /**
     * Список елементов для фильтра
     * @var string
     * @since 1.0
     */
    private static $list = null;

    /**
     * Задает дефолтные значения конфигурации поиска
     * @since 1.0
     */
    private static function auto_set()
    {
        try {
            self::$db = JFactory::getDbo();
            self::$input = JFactory::getApplication()->input;
            self::$limit = 10;
            self::$page = 1;
            self::$info = array();
            self::$type = 'filter';
            self::$lang = JFactory::getLanguage()->getTag();
        } catch (Exception $ex) {
            die('Error auto set');
        }
    }

    /**
     * Переопределение переменных в массиве
     * @param array $config массив для переопределения переменных
     * @since 1.0
     */
    private static function updateConfig($config)
    {
        if (is_array($config)) {
            foreach ($config as $key => $value) {
                if (isset(self::$$key) && $key !== 'info') {
                    self::$$key = $value;
                }
            }
        }
    }

    /**
     * Добавить информацию в массив
     * @param string $key ключ
     * @param mixed $value значение
     * @since 1.0
     */
    private static function setInfo($key, $value)
    {
        self::$info[$key] = $value;
    }

    /**
     * Задает значение по нужному формату
     * @param $value
     * @param $format
     * @return null or ($format)
     * @since 1.0
     */
    private static function setValue($value, $format)
    {
        switch ($format) {
            case 'int': // число должно бить больше нуля
                return is_numeric($value) ? ($value > 0 ? $value : 1) : null;
                break;
            case 'json': // превращение json в масив
                if (!self::isJSON($value)) {
                    return array();
                }
                $value = json_decode($value);
                foreach ($value as $id => $text) {
                    $value[$id] = preg_replace("/[^0-9a-z_-]/", '', $text);
                    if (!mb_strlen($value[$id])) {
                        unset($value[$id]);
                    }
                }
                return array_values($value);
                break;
            case 'string': // оставляем в строке тольуо символи
                return preg_replace("/[^А-Яа-яіІїЇёЁ, -]/u", '', $value);
            default:
                return null;
                break;
        }
    }

    /**
     * Получить список блоков по которым нужно искать. Беруться с GET или POST
     * @return array
     * @since 1.0
     */
    private static function searchBlockList()
    {
        $search_blocks = array(
            'autocomplete' => array(
                'config' => array(),
                'input' => array(
                    'salon_name' => array(
                        'format' => 'string'
                    ),
                    'organization_name' => array(
                        'format' => 'string'
                    ),
                    'location_name' => array(
                        'format' => 'string'
                    ),
                    'article_name' => array(
                        'format' => 'string'
                    )
                )
            ),
            'filter' => array(
                'config' => array(
                    'columns' => array( // какие колонки виберать с таблици
                        '`id`', '`alias`', '`metro`', '`location`', '`apparats`', '`comforts`', '`organization`', '`main_image`', '`logo`', '`phones`',
                        '`schedule`', '`coordinate`', '`premium`', '`prices`',
                        '(SELECT `type` FROM `#__lasercity_locations` as `sub_table` WHERE 
                            `sub_table`.`id`=`main_table`.`location`
                        ) as `location_type`',
                        '(SELECT `line` FROM `#__lasercity_metro` as `sub_table` WHERE 
                            `sub_table`.`id`=`main_table`.metro
                        ) as `metro_line`'
                    ),
                    'table' => '`#__lasercity_affiliates` as `main_table`', // с какой таблици виберать
                    'data_columns' => array( // подтягивание нужной информации на основе вибраних данних из table. Ключ массива => колонка с бд (columns)
                        'organization' => array(
                            'object_name' => 'organization', // обект в таблице переводов
                            'object_column' => array( // колонки с таблици переводов
                                'type'
                            )
                        ),
                        'id' => array(
                            'object_name' => 'affiliate',
                            'object_column' => array(
                                'title', 'description'
                            ),
                            'unset' => true // удалять елемент с результата после присвоения в основной массив
                        ),
                        'metro' => array(
                            'object_name' => 'metro',
                            'object_column' => array(
                                'title'
                            ),
                            'save_key_column' => true // заменить значение переводом
                        ),
                        'location' => array(
                            'object_name' => 'location',
                            'object_column' => array(
                                'title'
                            ),
                            'save_key_column' => true
                        ),
                        'comforts' => array(
                            'json' => true, // данние в колонке сохраняються в формате json, нужно превратить в масив
                            'object_name' => 'comfort',
                            'object_column' => array(
                                'title'
                            ),
                            'save_key_column' => true
                        ),
                        'apparats' => array(
                            'json' => true,
                            'object_name' => 'apparats', // название колонки
                            'no_translate' => true, // не переводить
                            'column' => 'title', // какое имя присвоить для значения
                            'table' => '#__lasercity_apparats', // с какой таблици виберать
                            'save_key_column' => true
                        )
                    ),
                    'default' => array(
                        'where_column' => '`alias`' // по какой таблице производить поиск
                    ),
                    'normalize' => array(
                        'comforts' => array(
                            'type' => 'union', // добавление в подзапрос
                            'columns' => array( // колонки какие нужно вибрать
                                '`icon`'
                            ),
                            'table' => '`#__lasercity_comforts`', // с какой таблици
                            'save_column' => 'title' // ка сохранить поле
                        ),
                        'schedule' => array(
                            'type' => 'json', // перевести json в array
                            'sub_type' => 'schedule' // подтип
                        ),
                        'phones' => array(
                            'type' => 'json'
                        ),
                        'prices' => array(
                            'type' => 'json'
                        ),
                        'metro_line' => array(
                            'type' => 'in', // закинуть внутрь
                            'column' => 'metro', // в какую таблицу закинуть
                            'value_column' => 'title', // какой ключ дать елементу в присваемой колонке
                            'in_column' => 'line' // какой ключ дать присваемому значению
                        ),
                        'location_type' => array(
                            'type' => 'in',
                            'column' => 'location',
                            'value_column' => 'title',
                            'in_column' => 'type',
                            'translate' => true, // перевести (keyTranslates)
                            'translate_type' => 'location' // тип для keyTranslates
                        ),
                        'coordinate' => array(
                            'type' => 'google-map' // ссилка на гугл карту по координатам
                        )
                    )
                ),
                'input' => array( // данние которие парсятся с GET или POST для where
                    'page' => array(
                        'update_config' => true, // занести значение в конфигурацию
                        'format' => 'int' // формат значения
                    ),
                    'apparatus' => array(
                        'format' => 'json', // данние сохраняються в формате json
                        'type' => 'column',  // ето колонка
                        'column' => '`apparats`', // для какой колонки делать where
                        'table' => '`#__lasercity_apparats`', // в какой таблице искать,
                        'regex' => true // строить запрос в режиме регулярного виражения
                    ),
                    'comforts' => array(
                        'format' => 'json',
                        'type' => 'column',
                        'column' => '`comforts`',
                        'table' => '`#__lasercity_comforts`',
                        'regex' => true
                    ),
                    'districts' => array(
                        'format' => 'json',
                        'type' => 'column',
                        'table' => '`#__lasercity_districts`',
                        'column' => '`district`'
                    ),
                    'locations' => array(
                        'format' => 'json',
                        'type' => 'column',
                        'table' => '`#__lasercity_locations`',
                        'column' => '`location`'
                    ),
                    'metro' => array(
                        'format' => 'json',
                        'type' => 'column',
                        'table' => '`#__lasercity_metro`',
                        'column' => '`metro`'
                    ),
                    'micro_districts' => array(
                        'format' => 'json',
                        'type' => 'column',
                        'table' => '`#__lasercity_micro_districts`',
                        'column' => '`micro_district`'
                    ),
                    'services' => array(
                        'format' => 'json',
                        'type' => 'sub_query',
                        'column' => '`prices`',
                        'table' => '`#__lasercity_services`',
                        'id_get' => '`price_id`',
                        'id_column' => '`service_id`',
                        'alias_table' => '`#__lasercity_to_filters`'
                    )/*,
                    'zones' => array(
                        'format' => 'json',
                        'type' => 'union',
                        'name' => '`zone`',
                        'table' => '`#__lasercity_zones`'
                    )*/
                )
            )
        );
        $search_block = array();
        if (isset($search_blocks[self::$type])) { // вибераеться блок настроек по типу
            try {
                $input = JFactory::getApplication()->input;
                $search_block = $search_blocks[self::$type]['input'];
                foreach (array_keys($search_block) as $key) {
                    $value = $input->getString($key); // получение параметра из get или post
                    if ($value == null) {
                        unset($search_block[$key]);
                    } else {
                        $format = $search_block[$key]['format'];
                        unset($search_block[$key]['format']);
                        if (isset($search_block[$key]['update_config'])) {
                            self::$$key = self::setValue($value, $format);
                            unset($search_block[$key]);
                        } else {
                            $search_block[$key]['value'] = self::setValue($value, $format);
                        }
                    }
                }
                $from_router_data = $input->get('to_filter_data', array());
                if (!empty($from_router_data)) {
                    $key = $from_router_data[0];
                    $value = $from_router_data[1];
                    if (isset($search_block[$key])) {
                        if (!in_array($value, $search_block[$key]['value'])) {
                            $search_block[$key]['value'][] = $value;
                        }
                    } else {
                        $search_block[$key] = $search_blocks[self::$type]['input'][$key];
                        unset($search_block[$key]['format']);
                    }
                    $search_block[$key]['value'][] = $value;
                }
            } catch (Exception $e) {
                return $search_block;
            }
        }
        $search_blocks[self::$type]['input'] = $search_block;
        foreach ($search_block as $key => $item) {
            $search_block[$key] = $item['value'];
        }
        self::setInfo('search_block', $search_block);
        return $search_blocks[self::$type];
    }

    /**
     * Получение перевода определенного типа по ключу
     * @param string $type тип перевода
     * @param string $key ключ перевода
     * @return string
     * @since 1.0
     */
    public static function keyTranslates($type, $key)
    {
        $translates = array(
            'location' => array(
                'street' => 'COM_LASERCITY_TABLE_STREET_S',
                'boulevard' => 'COM_LASERCITY_TABLE_BOULEVARD_S',
                'lane' => 'COM_LASERCITY_TABLE_LANE_S',
                'prospectus' => 'COM_LASERCITY_TABLE_PROSPECTUS_S',
                'area' => 'COM_LASERCITY_TABLE_AREA_S',
                'embankment' => 'COM_LASERCITY_TABLE_EMBANKMENT_S',
                'descent' => 'COM_LASERCITY_TABLE_DESCENT_S',
                'line' => 'COM_LASERCITY_TABLE_LINE_S',
                'highway' => 'COM_LASERCITY_TABLE_HIGHWAY_S',
                'massive' => 'COM_LASERCITY_TABLE_MASSIVE_S',
                'passage' => 'COM_LASERCITY_TABLE_PASSAGE_S'
            ),
            'schedule' => array(
                0 => 'Пн',
                1 => 'Вт',
                2 => 'Ср',
                3 => 'Чт',
                4 => 'Пт',
                5 => 'Сб',
                6 => 'Нд',
            )
        );
        return isset($translates[$type][$key]) ? JText::_($translates[$type][$key]) : $key;
    }

    /**
     * Получение результата для автокомплита
     * @since 1.0
     */
    private static function autocomplete()
    {
        return 'autocomplete';
    }

    /**
     * Возвращяет запрос для виборки филиалов по заданим фильтрам
     * @return array
     * @since 1.0
     */
    private static function filter()
    {
        $search_block_list = self::searchBlockList();
        $block_config = array(); // блок настроек
        foreach ($search_block_list['input'] as $block) { // форматирование массива
            $type = $block['type'];
            unset($block['type']);
            if (!isset($block_config[$type])) {
                $block_config[$type] = array();
            }
            $block_config[$type][] = $block;
        }

        $where = array( // блок с условиями виборки филиалов
            '`city`=' . JFactory::$session->get('current_city_id', 0),
            '`published`'
        );/*$where = array( // блок с условиями виборки филиалов
            '`city`=' . self::$input->get('current_city_id'),
            '`published`'
        );*/
        foreach ($block_config as $block => $config) { // добавления условий по входним данним из GET или POST (inputs)
            switch ($block) {
                case 'column': // если тип колонка
                    foreach ($config as $info) {
                        if (count($info['value'])) {
                            if (isset($info['regex'])) {
                                $where[] = "{$info['column']} REGEXP CONCAT('[^0-9](', (SELECT GROUP_CONCAT(id SEPARATOR '|') FROM {$info['table']} WHERE {$search_block_list['config']['default']['where_column']} IN(" . implode(',', self::$db->quote($info['value'])) . ')),\')[^0-9]\')';
                            } else {
                                $where[] = "{$info['column']} IN ((SELECT id FROM {$info['table']} WHERE {$search_block_list['config']['default']['where_column']} IN(" . implode(',', self::$db->quote($info['value'])) . ')))';
                            }
                        }
                    }
                    break;
                case 'sub_query': // если поиск по прайсу
                    foreach ($config as $info) {
                        if (count($info['value'])) {
                            $where[] = "{$info['column']} REGEXP CONCAT('[^0-9](', (SELECT GROUP_CONCAT({$info['id_get']} SEPARATOR '|') FROM {$info['alias_table']} WHERE {$info['id_column']} IN((SELECT id FROM {$info['table']} WHERE `alias` IN(" . implode(',', self::$db->quote($info['value'])) . ')))),\')[^0-9]\')';
                        }
                    }
                    //todo: реализовать поиск по прайсу
                    break;
            }
        }
        /*echo '<pre>';
        print_r($where);
        echo '</pre>';*/
        $columns = implode(',', $search_block_list['config']['columns']);
        $where = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        // todo: тут добавити order order
        $query = "SELECT {$columns} FROM {$search_block_list['config']['table']} {$where} ORDER BY `premium` DESC, `ordering` DESC";
        /*echo '<pre>';
        print_r($query);
        echo '</pre>';*/
        //die;
        self::$db->setQuery($query);
        $affiliates = self::$db->loadObjectList();
        $count = count($affiliates);
        $pages = ceil($count / self::$limit);
        self::$page = self::$page > $pages ? $pages : self::$page; // если страница из запроса больше количества страниц устанавливаем значение на количество страниц
        self::setInfo('page', self::$page);
        self::setInfo('pages', $pages);
        self::setInfo('count', $count);
        $affiliates = array_slice($affiliates, (self::$page - 1) * self::$limit, self::$limit);
        if (!empty($affiliates)) { // если филиалы существуют
            $data_columns = $search_block_list['config']['data_columns']; // блок с модификацией колонок
            foreach ($affiliates as $affiliate_id => $affiliate) {
                foreach ($data_columns as $column => $data_column) { // заполняем values
                    if (!isset($data_columns[$column]['values'])) {
                        $data_columns[$column]['values'] = array();
                    }
                    if (isset($data_columns[$column]['json'])) { // если данние в формате json перевести в масив и добавить
                        if (self::isJSON($affiliate->$column)) {
                            $ids = json_decode($affiliate->$column);
                            $affiliates[$affiliate_id]->$column = $ids;
                            foreach ($ids as $id) {
                                if ($id && !in_array($id, $data_columns[$column]['values'])) {
                                    $data_columns[$column]['values'][] = $id;
                                }
                            }
                        } else {
                            $affiliates[$affiliate_id]->$column = array();
                        }
                    } else { // добавление id в values (уникально)
                        if ($affiliate->$column && !in_array($affiliate->$column, $data_columns[$column]['values'])) {
                            $data_columns[$column]['values'][] = $affiliate->$column;
                        }
                    }
                }
            }
            $query = array();
            foreach ($data_columns as $key => $data_column) { // Создание подзапроса на виборку дополнительной информации для колонок филиалов
                if (!empty($data_column['values'])) {
                    if (isset($data_column['no_translate'])) {
                        $query[] = "(SELECT `id`, '{$data_column['object_name']}' as `name`, NULL as `column`, `{$data_column['column']}` as `value` FROM `{$data_column['table']}` WHERE `id` IN(" . implode(',', $data_column['values']) . '))';
                    } else {
                        $query[] = "(SELECT `object_id` as `id`, `object_name` as `name`, `object_column` as `column`, `value` FROM `#__lasercity_translations_" . self::$lang . "` WHERE `object_name`='{$data_column['object_name']}' AND `object_column` IN(" . implode(',', self::$db->quote($data_column['object_column'])) . ') AND `object_id` IN(' . implode(',', $data_column['values']) . '))';
                    }
                }
            }
            if (!empty($query)) { // если запрос не пустой заносим информацию в филиали
                $query = implode(' UNION ALL ', $query);
                self::$db->setQuery($query);
                $to_affiliates = self::$db->loadObjectList();
                foreach ($affiliates as $key => $affiliate) {
                    foreach ($data_columns as $column => $data_column) {
                        $is_json = isset($data_column['json']);
                        $ids = $is_json ? $affiliate->$column : array($affiliate->$column);
                        $values = array();
                        foreach ($to_affiliates as $id => $info) {
                            if ($info->name == $data_column['object_name'] && in_array($info->id, $ids)) {
                                $save_key_column = isset($data_column['save_key_column']) ? $column : $info->column;
                                $value = $info->value;
                                if ($is_json) {
                                    if (!isset($values[$save_key_column])) {
                                        $values[$save_key_column] = array();
                                    }
                                    $values[$save_key_column][$info->id] = $value;
                                } else {
                                    $values[$save_key_column] = $value;
                                }
                                if (isset($data_column['unset'])) {
                                    unset($to_affiliates[$id]);
                                }
                            }
                        }
                        foreach ($values as $save_column => $value) {
                            $affiliates[$key]->$save_column = $value;
                        }
                    }
                }
            }
            $normalize = $search_block_list['config']['normalize']; // приводим данние в нужний нам вид
            $to_union = array();
            foreach ($affiliates as $id => $affiliate) {
                foreach ($normalize as $column => $config) {
                    switch ($config['type']) {
                        case 'union':
                            foreach ($affiliate->$column as $id_to_column => $trash) {
                                if (!isset($to_union[$column])) {
                                    $to_union[$column] = array();
                                }
                                if (!in_array($id_to_column, $to_union[$column])) {
                                    $to_union[$column][] = $id_to_column;
                                }
                            }
                            break;
                        case 'json':
                            if (self::isJSON($affiliates[$id]->$column)) {
                                $affiliates[$id]->$column = json_decode($affiliate->$column);
                                if (isset($config['sub_type'])) {
                                    if ($config['sub_type'] == 'schedule') {
                                        self::setSchedule($affiliates[$id]->$column);
                                        /*$schedule = $affiliates[$id]->$column;
                                        foreach ($schedule as $key => $info) {
                                            $first = -1;
                                            $last = -1;
                                            for ($i = 0; $i < 7; $i++) {
                                                if ($info[$i] == 1) {
                                                    $first = $first == -1 ? $i : $first;
                                                    $last = $i;
                                                }
                                            }
                                            $new_schedule['days'] = $first == $last ? self::keyTranslates('schedule', $first) : self::keyTranslates('schedule', $first) . '-' . self::keyTranslates('schedule', $last);
                                            $new_schedule['time'] = $info[7];
                                            $schedule[$key] = $new_schedule;
                                            $affiliates[$id]->$column = $schedule;
                                        }*/
                                    }
                                }
                            }
                            break;
                        case 'in':
                            $column_value = $affiliate->{$config['column']};
                            $in_value = isset($config['translate']) ? self::keyTranslates($config['translate_type'], $affiliate->$column) : $affiliate->$column;
                            unset($affiliates[$id]->$column);
                            $affiliates[$id]->{$config['column']} = array(
                                $config['value_column'] => $column_value,
                                $config['in_column'] => $in_value,
                            );
                            break;
                        case 'google-map':
                            $affiliates[$id]->$column = mb_strlen($affiliate->$column) ? "http://maps.google.com/?q={$affiliate->$column}" : null;
                            break;
                    }
                }
            }
            $union = array();
            foreach ($to_union as $key => $arr_ids) {
                $normalize[$key]['columns'][] = "'{$key}' as `column_in_affiliate`";
                $union[] = '(SELECT `id`,' . implode(',', $normalize[$key]['columns']) . " FROM {$normalize[$key]['table']} WHERE `id` IN(" . implode(',', self::$db->quote($arr_ids)) . "))";
            }
            if (!empty($union)) {
                $query = implode(' UNION ALL ', $union);
                self::$db->setQuery($query);
                $result_union = self::$db->loadAssocList();
                $block_columns = array();
                foreach ($result_union as $item) {
                    $column_in_affiliate = $item['column_in_affiliate'];
                    unset($item['column_in_affiliate']);
                    $id = $item['id'];
                    unset($item['id']);
                    $block_columns[$column_in_affiliate][$id] = $item;
                }
                foreach ($affiliates as $id => $affiliate) {
                    foreach ($block_columns as $column => $block) {
                        if (!empty($affiliate->$column)) {
                            $data = $affiliate->$column;
                            foreach ($data as $key => $val) {
                                $to_save = $val;
                                $data[$key] = array();
                                $data[$key][$normalize[$column]['save_column']] = $to_save;
                            }
                            foreach ($block as $id_block => $items) {
                                if (isset($data[$id_block])) {
                                    foreach ($items as $col => $item) {
                                        $data[$id_block][$col] = $item;
                                    }
                                }
                            }
                            $affiliates[$id]->$column = $data;
                        }
                    }
                }
            }
        }
        return $affiliates;
    }

    public static function setSchedule(&$schedule)
    {
        foreach ($schedule as $key => $info) {
            $first = -1;
            $last = -1;
            for ($i = 0; $i < 7; $i++) {
                if ($info[$i] == 1) {
                    $first = $first == -1 ? $i : $first;
                    $last = $i;
                }
            }
            $new_schedule['days'] = $first == $last ? self::keyTranslates('schedule', $first) : self::keyTranslates('schedule', $first) . '-' . self::keyTranslates('schedule', $last);
            $new_schedule['time'] = $info[7];
            $schedule[$key] = $new_schedule;
        }
    }

    /**
     * Проверка строки на валидность json
     * @param string $string входной json
     * @return bool
     * @since 1.0
     */
    public static function isJSON($string)
    {
        return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
    }

    /**
     * Получить результат поиска
     * @param array $config массив для функции updateConfig()
     * @return array|string
     * @since 1.0
     */
    public static function getResult($config = array())
    {
        if (self::$result != null) {
            return self::$result;
        }
        $time_start = microtime(true);
        $memory_start = memory_get_usage();
        self::auto_set();
        self::updateConfig($config);

        switch (self::$type) {
            case 'autocomplete':
                $result = self::autocomplete();
                break;
            case 'filter':
                $result = self::filter();
                break;
            default:
                $result = array();
                break;
        }

        /*$result = self::loadObjectList($query);*/

        self::setInfo('config', array(
            'limit' => self::$limit,
            'type' => self::$type,
            'lang' => self::$lang
        ));
        self::setInfo('debug', array(
            'time' => microtime(true) - $time_start . ' sec',
            'memory' => ((memory_get_usage() - $memory_start) / 1024) . ' KB'
        ));
        
        echo '<pre>';
        print_r(self::$info);
        echo '</pre>';

        echo '<pre>';
        print_r($result);
        echo '</pre>';

        self::$result = $result;

        return $result;
    }

    /**
     * Получить информацию по последнему поиску
     * @param $key string ключ к информации
     * @return array
     * @since 1.0
     */
    public static function getInfo($key = null)
    {
        if ($key != null) {
            return self::$info[$key];
        }
        return self::$info;
    }

    /**
     * Вибор списка фильтров
     * @param array $get
     * @return array
     * @since 1.0
     */
    public static function getFiltersList($get = array())
    {
        if (self::$list != null) {
            return (array)self::$list;
        }

        $result = array();
        $db = JFactory::getDbo();
        try {
            $input = JFactory::getApplication()->input;
        } catch (Exception $e) {
            die($e);
        }
        $city_id = $input->get('current_city_id', '0');
        $lang_tag = JFactory::getLanguage()->getTag();

        if (empty($get)) {
            $result['services'] = self::getFilterServices($db, $city_id, $lang_tag);
            $result['metro'] = self::getFilterMetro($db, $city_id, $lang_tag);
            $result['districts'] = self::getFilterDistrictsAndMicroDistricts($db, $city_id, $lang_tag);
            $result['comforts'] = self::getFilterComforts($db, $city_id, $lang_tag);
            $result['apparatus'] = self::getFilterApparatus($db, $city_id);
            $result['locations'] = self::getFilterLocations($db, $city_id, $lang_tag);
            self::$list = $result;
        } else {
            foreach ($get as $item) {
                switch ($item) {
                    case 'services':
                        $result['services'] = self::getFilterServices($db, $city_id, $lang_tag);
                        break;
                    case 'metro':
                        $result['metro'] = self::getFilterMetro($db, $city_id, $lang_tag);
                        break;
                    case 'districts':
                        $result['districts'] = self::getFilterDistrictsAndMicroDistricts($db, $city_id, $lang_tag);
                        break;
                    case 'comforts':
                        $result['comforts'] = self::getFilterComforts($db, $city_id, $lang_tag);
                        break;
                    case 'apparatus':
                        $result['apparatus'] = self::getFilterApparatus($db, $city_id);
                        break;
                    case 'locations':
                        $result['locations'] = self::getFilterLocations($db, $city_id, $lang_tag);
                        break;
                }
            }
        }

        return $result;
    }

    /**
     * Вибор списка местонахождений для getFiltersList
     * @param $city_id
     * @param $lang_tag
     * @return array
     * @var $db \JDatabaseDriver
     * @since 1.0
     */
    private static function getFilterLocations($db, $city_id, $lang_tag)
    {
        $query = "SELECT (
    SELECT `value`
    FROM `#__lasercity_translations_{$lang_tag}`
    WHERE
        `object_name`='location' AND
        `object_column`='title' AND
        `object_id`=`location`.`id`
) as `title`, `type`,`alias`
FROM `#__lasercity_locations` as `location`
WHERE `city`={$city_id} AND `published`";

        $db->setQuery($query);
        $locations = $db->loadObjectList();

        foreach ($locations as $location) {
            $type = $location->type;
            unset($location->type);
            $location->title = JText::_('COM_LASERCITY_TABLE_' . mb_strtoupper($type) . '_S') . ' ' . $location->title;
        }

        return $locations;
    }

    /**
     * Вибор списка филиалов для getFiltersList
     * @param $city_id
     * @return array
     * @since 1.0
     * @var $db \JDatabaseDriver
     */
    private static function getFilterApparatus($db, $city_id)
    {
        $db->setQuery("SELECT * FROM (SELECT `title`, `type`, `alias`, (
  SELECT COUNT(*) 
  FROM `#__lasercity_affiliates` 
  WHERE 
    `city`='{$city_id}' AND 
    `apparats` REGEXP(CONCAT('[^0-9]', `t1`.`id`,'[^0-9]'))
  LIMIT 1
) as `count`
FROM `#__lasercity_apparats` as `t1`) as `t` WHERE `t`.`count`");
        $result = array();
        $apparatus = $db->loadObjectList();
        $translate = array(
            'diode' => 'Диодный',
            'neodymium' => 'Неодимовый',
            'alexandrite' => 'Александритовый',
            'ruby' => 'Гибридный',
        );
        foreach ($apparatus as $key => $item) {
            $type = $item->type;
            unset($item->type);
            $type = isset($translate[$type]) ? $translate[$type] : $type;
            $result[$type][] = $item;
        }
        return $result;
    }

    /**
     * Вибор списка комфортов для getFiltersList
     * @param $city_id
     * @param $lang_tag
     * @return array
     * @var $db \JDatabaseDriver
     * @since 1.0
     */
    private static function getFilterComforts($db, $city_id, $lang_tag)
    {
        $db->setQuery("SELECT (
    SELECT `value` 
    FROM `#__lasercity_translations_{$lang_tag}`
    WHERE
      `object_name`='comfort' AND 
      `object_column`='title' AND
      `object_id`=`t1`.`id`
  ) as `title`, 
  `alias` 
FROM `#__lasercity_comforts` as `t1` 
WHERE (
  SELECT COUNT(*) 
  FROM `#__lasercity_affiliates` 
  WHERE
    `city`='{$city_id}' AND 
    `comforts` REGEXP(CONCAT('[^0-9]', `t1`.`id`,'[^0-9]'))
  LIMIT 1
)");
        return $db->loadObjectList();
    }

    /**
     * Вибор списка ройонов по ключам для getFiltersList
     * @param $city_id
     * @param $lang_tag
     * @return array
     * @var $db \JDatabaseDriver
     * @since 1.0
     */
    private static function getFilterDistrictsAndMicroDistricts($db, $city_id, $lang_tag)
    {
        $db->setQuery("(SELECT id, 'district' as `type`, NULL as `district`, (
    SELECT `value` 
    FROM `#__lasercity_translations_{$lang_tag}`
    WHERE
      `object_name`='district' AND 
      `object_column`='title' AND
      `object_id`=`t1`.`id`
    LIMIT 1 
  ) as `title`,
  `alias` 
FROM `#__lasercity_districts` as `t1`
WHERE 
  `t1`.`city`={$city_id} AND 
  (SELECT COUNT(*) FROM `#__lasercity_affiliates` WHERE `district`=`t1`.`id` LIMIT 1)
) UNION ALL (
  SELECT `id`, 'micro_district', `district`, (
    SELECT `value` 
    FROM `#__lasercity_translations_{$lang_tag}`
    WHERE
      `object_name`='micro_district' AND 
      `object_column`='title' AND
      `object_id`=`t1`.`id`
  ), `alias`
  FROM `#__lasercity_micro_districts` as `t1`
  WHERE
    `t1`.`city`={$city_id} AND 
    (SELECT COUNT(*) FROM `#__lasercity_affiliates` WHERE `micro_district`=`t1`.`id` LIMIT 1)
)");
        $districts_and_micro_districts = $db->loadObjectList();
        $result = array();
        foreach ($districts_and_micro_districts as $item) {
            switch ($item->type) {
                case 'district':
                    $result[$item->id]['title'] = $item->title;
                    $result[$item->id]['alias'] = $item->alias;
                    $result[$item->id]['micro_districts'] = array();
                    break;
                case 'micro_district':
                    $micro_district['title'] = $item->title;
                    $micro_district['alias'] = $item->alias;
                    $result[$item->district]['micro_districts'][] = $micro_district;
                    break;
            }
        }
        return $result;
    }

    /**
     * Вибор списка метро по ключам для getFiltersList
     * @param $city_id
     * @param $lang_tag
     * @return array
     * @var $db \JDatabaseDriver
     * @since 1.0
     */
    private static function getFilterMetro($db, $city_id, $lang_tag)
    {
        $db->setQuery("SELECT `line`, (
    SELECT `value` 
    FROM `#__lasercity_translations_{$lang_tag}`
    WHERE
      `object_name`='metro' AND 
      `object_column`='title' AND
      `object_id`=`t1`.`id`
  ) as `title`,
  `alias` 
FROM `#__lasercity_metro` as `t1`
WHERE 
  `t1`.`city`={$city_id} AND 
  (SELECT COUNT(*) FROM `#__lasercity_affiliates` WHERE `metro`=`t1`.`id` LIMIT 1)
");
        $metro = $db->loadObjectList();
        $result = array();
        foreach ($metro as $item) {
            $line = $item->line;
            unset($item->line);
            if (!isset($result[$line])) {
                $result[$line]['text'] = JText::_('COM_LASERCITY_TABLE_METRO_LINE_' . mb_strtoupper($line));
                $result[$line]['values'] = array();
            }
            $result[$line]['values'][] = $item;
        }
        return $result;
    }

    /**
     * Вибор списка услуг getFiltersList
     * @param $city_id
     * @param $lang_tag
     * @return array
     * @var $db \JDatabaseDriver
     * @since 1.0
     */
    function setPrice($prices) {
        $result = array();

        foreach ($prices as $price) {
            foreach ($price->data as $service) {
                foreach ($service->zones as $zone) {
                    $item['title'] = $service->service_text;
                    $item['duration'] = $service->duration;
                    $item['price'] = $service->price;
                    $item['service_id'] = $service->service;
                    $item['aparat_id'] = $price->aparat_id;
                    /*$item['percent'] = $service->percent;
                    $item['start_sale'] = $service->start_sale;
                    $item['end_sale'] = $service->end_sale;*/
                    $result[$price->apparatus][$zone][$price->sex][] = $item;
                }
            }
        }

        return $result;
    }
    private static function getFilterServices($db, $city_id, $lang_tag)
    {

        /*$db->setQuery("SELECT `prices` FROM `#__lasercity_affiliates` WHERE `published`='1' AND `city`='".$city_id."'");

        $affiliates = $db->loadObjectList();

        foreach ($affiliates as $affiliate) {
            if (!empty($affiliate->prices)) {
                $prices_ids = implode(',', $affiliate->prices);
                $db->setQuery("SELECT
                                IF(`sex`, 'men', 'women') as `sex`, apparat as aparat_id, (
                                    SELECT `title` FROM `#__lasercity_apparats` WHERE `id`=`main`.`apparat`
                                ) as `apparatus`, `data`
                                FROM `#__lasercity_prices` as `main`
                                WHERE `id` IN ({$prices_ids})
                            ");
                $services = array();
                $prices = $db->loadObjectList();
                foreach ($prices as $price) {
                    if (Search::isJSON($price->data)) {
                        $price->data = json_decode($price->data);
                        foreach ($price->data as $datum) {
                            if (!in_array($datum->service, $services)) {
                                $services[] = $datum->service;
                            }
                        }
                    } else {
                        $price->data = array();
                    }
                }

                if (!empty($services)) {
                    $services_ids = implode(',', $services);

                    // вибрати зони
                    $db->setQuery("SELECT `id`, `zones` FROM `#__lasercity_services` WHERE `id` IN({$services_ids})");

                    $zones = array();
                    $services_id_list = $db->loadObjectList('id');

                    foreach ($services_id_list as $service) {
                        if (Search::isJSON($service->zones)) {
                            $service->zones = json_decode($service->zones);
                            foreach ($service->zones as $zone) {
                                if (!in_array($zone, $zones)) {
                                    $zones[] = $zone;
                                }
                            }
                        } else {
                            $service->zones = array();
                        }
                    }

                    if (!empty($zones)) {
                        $zones_ids = implode(',', $zones);
                        $db->setQuery("SELECT `value`, `object_id` FROM `#__lasercity_translations_{$lang_tag}` WHERE `object_name`='zone' AND `object_column`='title' AND `object_id` IN({$zones_ids})");
                        $zones = $db->loadObjectList('object_id');
                        foreach ($services_id_list as $item) {
                            foreach ($item->zones as $key => $info_zone_ids) {
                                $item->zones[$key] = $zones[$item->zones[$key]]->value;
                            }
                        }
                    }

                    $db->setQuery("SELECT `value`, `object_id` FROM `#__lasercity_translations_{$lang_tag}` WHERE `object_name`='service' AND `object_column`='title' AND `object_id` IN({$services_ids})");
                    $services = $db->loadObjectList('object_id');
                    $count=0;
                    foreach ($prices as $price) {
                        foreach ($price->data as $datum) {
                            $datum->service_text = $services[$datum->service]->value;
                            $datum->zones = $services_id_list[$datum->service]->zones;
                            $count++;
                        }
                    }
                }

                $affiliate->prices[] = self::setPrice($prices);
                $affiliate->all_prices[] = $count;
            }
        }
        echo '<pre class="visually-hidden">';
        print_r($affiliate);
        echo '</pre>';*/
        //return $result;

        $db->setQuery("SELECT (
  SELECT `value` 
  FROM `#__lasercity_translations_{$lang_tag}`
  WHERE
    `object_name`='service' AND 
    `object_column`='title' AND
    `object_id`=`main`.`id`
) as `title`, `alias`, `id`, `zones`
FROM `#__lasercity_services` as `main` 
WHERE 
`id` IN(
  SELECT DISTINCT `service_id` 
  FROM `#__lasercity_to_filters` as `filters`
  WHERE (
    SELECT COUNT(*)
    FROM `#__lasercity_affiliates` as `affiliates`
    WHERE 
    `affiliates`.`city`={$city_id} AND
    `affiliates`.`prices` REGEXP CONCAT('[^0-9]', `filters`.`price_id` ,'[^0-9]')
    LIMIT 1
  )
)
");
        $zones_ids = array();
        $services = $db->loadObjectList();
        foreach ($services as $id => $service) {
            $services[$id]->zones = self::isJSON($service->zones) ? json_decode($service->zones) : array();
            foreach ($services[$id]->zones as $zone) {
                if (!in_array($zone, $zones_ids)) {
                    $zones_ids[] = $zone;
                }
            }
        }

        $result = array();

        if (!empty($zones_ids)) {
            $zones_ids = implode(',', $zones_ids);
            $db->setQuery("SELECT `id`, (
  SELECT `value` 
  FROM `#__lasercity_translations_{$lang_tag}`
  WHERE
    `object_name`='zone' AND 
    `object_column`='title' AND
    `object_id`=`main`.`id`
) as `title`
FROM `#__lasercity_zones` as `main`
WHERE `id` IN({$zones_ids})
");
            $zones = $db->loadObjectList('id');

            foreach ($zones as $zone) {
                $result[$zone->title] = array();
            }

            foreach ($services as $id => $service) {
                foreach ($service->zones as $key => $zone_id) {
                    foreach ($zones as $zone) {
                        if ($zone->id == $zone_id) {
                            $service->zones[$key] = $zone->title;
                        }
                    }
                }
                $services[$id] = $service;
            }

            $ids = array();

            foreach ($services as $id => $service) {
                $ids [] = $service->id;
            }

            if (!empty($ids)) {
                $ids_str = implode(',', $ids);
                $q = "SELECT `service_id`, `price_id`, (
    SELECT COUNT(*)
    FROM `#__lasercity_affiliates` as `affiliate`
    WHERE
        `affiliate`.`city` = {$city_id} AND
        `affiliate`.`prices` REGEXP CONCAT('[^0-9]', `main`.`price_id`,'[^0-9]')
) as `count`
FROM `#__lasercity_to_filters` as `main`
WHERE `service_id` IN ({$ids_str})";

                $db->setQuery($q);

                $services_price_count = $db->loadObjectList();

                foreach ($services as $service) {
                    $service->count = 0;
                    foreach ($services_price_count as $item) {
                        if ($item->service_id == $service->id) {
                            $service->count = $item->count;
                        }
                    }
                }

                foreach ($services as $id => $service) {
                    $ids [] = $service->id;
                    $service_info['title'] = $service->title;
                    $service_info['alias'] = $service->alias;
                    $service_info['count'] = $service->count;
                    for ($i = 0; $i < count($service->zones); $i++) {
                        $result[$service->zones[$i]][] = $service_info;
                    }
                }
            }
        }


        return $result;
    }

    private static $active_filters = null;

    public static function getActiveFiltersAndRemoveLinks()
    {
        if (self::$active_filters != null) {
            return self::$active_filters;
        }

        $currentActiveList = array();
        $filterList = self::getFiltersList();
        $search_block = self::getInfo('search_block');

        foreach ($search_block as $key => $items) {
            if (isset($filterList[$key]) || (isset($filterList['districts']) && $key == 'micro_districts')) {
                switch ($key) {
                    case 'services':
                        foreach ($filterList[$key] as $services) {
                            foreach ($services as $service) {
                                if (in_array($service['alias'], $items)) {
                                    $obj = new stdClass();
                                    $obj->title = $service['title'];
                                    $obj->alias = $service['alias'];
                                    $obj->type = $key;
                                    $currentActiveList[] = $obj;
                                }
                            }
                        }
                        break;
                    case 'metro':
                        foreach ($filterList[$key] as $lines) {
                            foreach ($lines['values'] as $metro) {
                                if (in_array($metro->alias, $items)) {
                                    $metro->type = $key;
                                    $currentActiveList[] = $metro;
                                }
                            }
                        }
                        break;
                    case 'districts':
                        foreach ($filterList[$key] as $district) {
                            if (in_array($district['alias'], $items)) {
                                $obj = new stdClass();
                                $obj->title = $district['title'];
                                $obj->alias = $district['alias'];
                                $obj->type = $key;
                                $currentActiveList[] = $obj;
                            }
                        }
                        break;
                    case 'micro_districts':
                        foreach ($filterList['districts'] as $district) {
                            foreach ($district['micro_districts'] as $micro_district) {
                                if (in_array($micro_district['alias'], $items)) {
                                    $obj = new stdClass();
                                    $obj->title = $micro_district['title'];
                                    $obj->alias = $micro_district['alias'];
                                    $obj->type = $key;
                                    $currentActiveList[] = $obj;
                                }
                            }
                        }
                        break;
                    case 'comforts':
                        foreach ($filterList[$key] as $comfort) {
                            if (in_array($comfort->alias, $items)) {
                                $comfort->type = $key;
                                $currentActiveList[] = $comfort;
                            }
                        }
                        break;
                    case 'locations':
                        foreach ($filterList[$key] as $location) {
                            if (in_array($location->alias, $items)) {
                                $location->type = $key;
                                $currentActiveList[] = $location;
                            }
                        }
                        break;
                    case 'apparatus':
                        foreach ($filterList[$key] as $types) {
                            foreach ($types as $apparatus) {
                                if (in_array($apparatus->alias, $items)) {
                                    $apparatus->type = $key;
                                    $currentActiveList[] = $apparatus;
                                }
                            }
                        }
                        break;
                }
            }
        }

        $sef = LangHelper::getCurrentSef();
        $city_alias = CityHelper::getCurrent()->alias;

        $work_link = "/{$sef}/clinics/{$city_alias}";

        foreach ($currentActiveList as $item) {
            $blocks = array();
            foreach ($currentActiveList as $elem) {
                if ($item->alias != $elem->alias) {
                    $blocks[$elem->type][] = $elem->alias;
                }
            }

            $to_implode = array();

            foreach ($blocks as $key => $elem) {
                $to_implode [] = $key . '=' . json_encode($elem);
            }

            $item->link = "$work_link" . (empty($to_implode) ? null : '?' . implode('&amp;', $to_implode));
        }

        self::$active_filters = $currentActiveList;

        return $currentActiveList;
    }
}