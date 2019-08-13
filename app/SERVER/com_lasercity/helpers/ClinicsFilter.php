<?php defined('_JEXEC') or die;

class AffiliatesFilter
{
    /**
     * @var JDatabaseDriver
     * @since 1.0
     */
    private $_db;

    /**
     * @var JInput
     * @since 1.0
     */
    private $_input;

    /**
     * @var JSession
     * @since 1.0
     */
    private $_session;

    private $config;

    private $info;

    private $activeFilters = [];

    private $query;

    private $instructions;

    private $container;

    function __construct($options = [])
    {
        $this->_db = JFactory::$database;
        $this->_input = JFactory::$application->input;
        $this->_session = JFactory::$session;
        $this->info = [
            'page' => 1,
            'pages' => 1,
            'count' => 1,
            'time' => 0
        ];
        $this->config = [
            'limit' => 10,
            'langTag' => JFactory::$language->getTag()
        ];
        if (!empty($options)) {
            $this->updateOptions($this->info, $options);
            $this->updateOptions($this->config, $options);
        }
        $functions = [
            'getWhereIdInAliasCallback' => function ($column, $table) {
                return function ($aliasList) use ($column, $table) {
                    $this->query['where'][] = "{$column} IN(SELECT `id` FROM {$table} WHERE `alias` IN(" . implode(',', $this->_db->quote($aliasList)) . '))';
                };
            },
            'getWhereIdRegexpAliasCallback' => function ($column, $table) {
                return function ($aliasList) use ($column, $table) {
                    $this->query['where'][] = "{$column} REGEXP CONCAT('[^0-9](', (SELECT GROUP_CONCAT(id SEPARATOR '|') FROM {$table} WHERE `published` AND `alias` IN(" . implode(',', $this->_db->quote($aliasList)) . ')),\')[^0-9]\')';
                };
            },
            'getSubColumn' => function ($column, $table, $whereSubColumn, $equalsMainColumn, $as) {
                return "(SELECT $column FROM {$table} as `sub_table` WHERE `sub_table`.{$whereSubColumn}=`main_table`.$equalsMainColumn) as {$as}";
            }
        ];
        $this->query = [
            'columns' => [
                '`id`', '`alias`', '`metro`', '`location`', '`apparats`', '`comforts`', '`main_image`',
                '`phones`', '`schedule`', '`coordinate`', '`premium`', '`prices`', '`organization`',
                '(SELECT FORMAT(AVG((`place`+`relationship`+`purity`+`quality`+`price`)/5), 2) FROM `#__lasercity_reviews` as `rating_table` WHERE `rating_table`.`affiliate_id`=`main_table`.`id`) as `rating`',
                '(SELECT COUNT(*) FROM `#__lasercity_reviews` as `review_table` WHERE `review_table`.`affiliate_id`=`main_table`.`id`) as `reviews`',
                $functions['getSubColumn']('`type`', '`#__lasercity_locations`', '`id`', '`location`', '`location_type`'),
                $functions['getSubColumn']('`line`', '`#__lasercity_metro`', '`id`', '`metro`', '`metro_line`')
            ],
            'table' => '`#__lasercity_affiliates` as `main_table`',
            'where' => [
                '`published`',
                '`city`=' . $this->_session->get('current_city_id', 0)
            ],
            'order' => [
                'rating' => 'desc'
            ]
        ];
        $this->instructions = [
            'page' => [
                'callbacks' => [
                    'build-where' => function ($value) {
                        if (is_numeric($value) && $value > 0) {
                            $this->updateOptions($this->info, ['page' => $value]);
                        }
                    }
                ],
                'json' => false,
                'filter' => false
            ],
            'order-by' => [
                'callbacks' => [
                    'build-where' => function($text) {
                        $orderComps = explode('.', $text);
                        if (count($orderComps) == 2 && in_array($orderComps[0], ['rating', 'reviews']) && in_array($orderComps[1], ['desc', 'asc'])) {
                            $this->query['order'][$orderComps[0]] = $orderComps[1];
                        }
                    }
                ],
                'json' => false,
                'filter' => false
            ],
            'comforts' => [
                'callbacks' => [
                    'build-where' => $functions['getWhereIdRegexpAliasCallback']('`comforts`', '`#__lasercity_comforts`')
                ],
                'json' => true,
                'filter' => true
            ],
            'apparatus' => [
                'callbacks' => [
                    'build-where' => function ($apparatus) {
                        $this->query['where'][] = "`prices` REGEXP CONCAT('[^0-9](', (SELECT GROUP_CONCAT(id SEPARATOR '|') FROM (SELECT `id` FROM `#__lasercity_prices` WHERE `apparat` IN(SELECT `id` FROM `#__lasercity_apparats` WHERE `alias` IN(" . implode(',', $this->_db->quote($apparatus)) . "))) as `table`),')[^0-9]')";
                    }
                ],
                'json' => true,
                'filter' => true
            ],
            'districts' => [
                'callbacks' => [
                    'build-where' => $functions['getWhereIdInAliasCallback']('`district`', '`#__lasercity_districts`')
                ],
                'json' => true,
                'filter' => true
            ],
            'locations' => [
                'callbacks' => [
                    'build-where' => $functions['getWhereIdInAliasCallback']('`location`', '`#__lasercity_locations`')
                ],
                'json' => true,
                'filter' => true
            ],
            'metro' => [
                'callbacks' => [
                    'build-where' => $functions['getWhereIdInAliasCallback']('`metro`', '`#__lasercity_metro`')
                ],
                'json' => true,
                'filter' => true
            ],
            'micro_districts' => [
                'callbacks' => [
                    'build-where' => $functions['getWhereIdInAliasCallback']('`micro_district`', '`#__lasercity_micro_districts`')
                ],
                'json' => true,
                'filter' => true
            ],
            'services' => [
                'callbacks' => [
                    'build-where' => function ($aliasList) {
                        $validAliasList = ['male' => [], 'female' => []];
                        foreach ($aliasList as $alias) {
                            $tmp = explode('.', $alias);
                            if (count($tmp) == 2) {
                                $service = array_pop($tmp);
                                $sex = array_pop($tmp);
                                if (mb_strlen($sex) && mb_strlen($service) && isset($validAliasList[$sex])) {
                                    $validAliasList[$sex][] = $service;
                                }
                            }
                        }

                        if (!empty($validAliasList)) {
                            $setQuery = function ($sex, $alias) {
                                return "`prices` REGEXP CONCAT('[^0-9](', (SELECT GROUP_CONCAT(`price_id` SEPARATOR '|') FROM (SELECT `price_id` FROM `#__lasercity_to_filters` WHERE `price_id` IN(SELECT `id` FROM `#__lasercity_prices` WHERE `sex`={$sex}) AND `service_id` IN(SELECT `id` FROM `#__lasercity_services` WHERE `alias` IN(" . implode(',', $this->_db->quote($alias)) . "))) as `table`),')[^0-9]')";
                            };

                            $sex = 1;

                            foreach ($validAliasList as $values) {
                                if (!empty($values)) {
                                    $this->query['where'][] = $setQuery($sex, $values);
                                }
                                $sex--;
                            }
                        }
                    }
                ],
                'json' => true,
                'filter' => true
            ]
        ];
        $this->container = [
            'translate' => [
                'data' => [],
                'callback' => function (&$data, $column, $name) {
                    foreach ((array)$column['object_column'] as $object_column) {
                        $data[] = "(`object_name`='{$column['object_name']}' AND `object_column`='{$object_column}' AND `object_id` IN(" . implode(',', $column['values']) . '))';
                    }
                },
                'callback_after' => function ($data, &$affiliates, $columns) {
                    $type_column = 'NULL';

                    foreach ($columns as $name => $info) {
                        foreach ((array)$info['object_column'] as $column) {
                            $type_column = "IF(`object_name`='{$info['object_name']}' AND `object_column`='{$column}' AND `object_id` IN(" . implode(',', $info['values']) . "), '{$name}', " . $type_column . ')';
                        }
                    }

                    $translates = $this->_db->setQuery("SELECT {$type_column} as `type`, `object_column`, `object_id`, `value` FROM `#__lasercity_translations_{$this->config['langTag']}` WHERE " . implode(' OR ', $data))->loadAssocList();

                    $setArray = [];
                    foreach ($translates as $translate) {
                        $setArray[$translate['type']][$translate['object_id']][$translate['object_column']] = $translate['value'];
                    }

                    foreach ($affiliates as $affiliate) {
                        foreach ($setArray as $column => $values) {
                            foreach ($columns[$column]['set_column'] as $object_column => $set_column) {
                                if (isset($affiliate->$set_column) && is_array($affiliate->$set_column)) {
                                    $new_value = [];
                                    foreach ($affiliate->$set_column as $id => $value) {
                                        if (isset($setArray[$column][$value])) {
                                            $new_value[$value] = $setArray[$column][$value][$object_column];
                                        }
                                    }
                                    $affiliate->$set_column = $new_value;
                                } else {
                                    $affiliate->$set_column = isset($setArray[$column][$affiliate->$column]) ? $setArray[$column][$affiliate->$column][$object_column] : null;
                                }
                            }
                        }
                    }
                },
                'columns' => [
                    'id' => [
                        'values' => [],
                        'object_name' => 'affiliate',
                        'object_column' => ['title', 'description'],
                        'set_column' => [
                            'title' => 'title',
                            'description' => 'description'
                        ]
                    ], 'organization' => [
                        'values' => [],
                        'object_name' => 'organization',
                        'object_column' => 'type',
                        'set_column' => ['type' => 'type']
                    ], 'location' => [
                        'values' => [],
                        'object_name' => 'location',
                        'object_column' => 'title',
                        'set_column' => ['title' => 'location']
                    ], 'metro' => [
                        'values' => [],
                        'object_name' => 'metro',
                        'object_column' => 'title',
                        'set_column' => ['title' => 'metro']
                    ],
                    'comforts' => [
                        'values' => [],
                        'object_name' => 'comfort',
                        'object_column' => 'title',
                        'set_column' => ['title' => 'comforts']
                    ]
                ]
            ],
            'sub_query' => [
                'data' => [],
                'callback' => function (&$data, $column, $name) {
                    $data[] = "(SELECT `id`, '{$name}' as `type`, {$column['column']} FROM `{$column['table']}` WHERE `id` IN(" . implode(',', $column['values']) . '))';
                },
                'callback_after' => function ($data, &$affiliates, $columns) {
                    $values = $this->_db->setQuery(implode(' UNION ALL ', $data))->loadObjectList();

                    $setArray = [];
                    foreach ($values as $value) {
                        $setArray[$value->type][$value->id] = $value->{$columns[$value->type]['set_column']};
                    }

                    foreach ($affiliates as $affiliate) {
                        foreach ($setArray as $field => $items) {
                            $set_value = [];
                            foreach ($affiliate->$field as $key => $item) {
                                if (isset($items[$key])) {
                                    $set_value[] = [
                                        $columns[$field]['set_column'] => $items[$key],
                                        $columns[$field]['value_column'] => $item
                                    ];
                                }
                            }
                            $affiliate->$field = $set_value;
                        }
                    }
                },
                'columns' => [
                    'comforts' => [
                        'values' => [],
                        'column' => '`icon`',
                        'set_column' => 'icon',
                        'value_column' => 'title',
                        'table' => '#__lasercity_comforts'
                    ]
                ]
            ],
            'column-array' => [
                'data' => [],
                'callback' => function (&$data, $column, $name) {
                    $data[] = "(SELECT `id`, {$column['column']} as `value`, '{$name}' as `type` FROM {$column['table']} WHERE {$column['where_column']} IN(" . implode(',', $column['values']) . '))';
                },
                'callback_after' => function ($data, &$affiliates, $columns) {
                    $result = $this->_db->setQuery(implode(' UNION ALL ', $data))->loadAssocList();
                    $setArray = [];
                    foreach ($result as $item) {
                        $setArray[$item['type']][$item['id']] = $item['value'];
                    }
                    foreach ($setArray as $column => $data) {
                        foreach ($affiliates as $affiliate) {
                            foreach ($affiliate->$column as $id => $item) {
                                $affiliate->$column[$id] = $setArray[$column][$affiliate->$column[$id]];
                            }
                        }
                    }
                },
                'columns' => [
                    'apparats' => [
                        'values' => [],
                        'column' => '`title`',
                        'table' => '`#__lasercity_apparats`',
                        'where_column' => '`id`'
                    ]
                ]
            ]
        ];
    }

    function getActiveFilters()
    {
        return $this->activeFilters;
    }

    function getResult()
    {
        $time_start = microtime(true);

        //todo Нада кеш система на идиншники

        $this->provokeCallback('build-where');

        $affiliates = $this->_db->setQuery($this->getQuery())->loadObjectList();

        $count = count($affiliates);
        $pages = ceil($count / $this->config['limit']);
        $page = $this->info['page'] > $pages ? $pages : $this->info['page'];

        $this->updateOptions($this->info, [
            'count' => $count,
            'page' => $page,
            'pages' => $pages
        ]);

        $affiliates = array_slice($affiliates, ($page - 1) * $this->config['limit'], $this->config['limit']);

        $this->setFormat($affiliates);

        $this->updateOptions($this->info, ['time' => microtime(true) - $time_start . ' sec']);
        return [
            'info' => $this->info,
            'config' => $this->config,
            'query' => $this->query,
            'affiliates' => $affiliates
        ];
    }

    private function isJSON($string)
    {
        return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string)))));
    }

    private function setSchedule(&$schedule)
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
            $new_schedule['days'] = $first == $last ? $this->keyTranslates('schedule', $first) : $this->keyTranslates('schedule', $first) . '-' . $this->keyTranslates('schedule', $last);
            $new_schedule['time'] = $info[7];
            $schedule[$key] = $new_schedule;
        }
    }

    private function setFormat(&$affiliates)
    {
        foreach ($affiliates as $affiliate) {
            if (mb_strlen($affiliate->coordinate)) {
                $affiliate->coordinate = "http://maps.google.com/?q={$affiliate->coordinate}";
            }
            foreach (['apparats', 'comforts', 'phones', 'schedule', 'prices'] as $field) {
                $affiliate->$field = json_decode($affiliate->$field);
            }
            foreach (array_keys($this->container) as $type) {
                foreach (array_keys($this->container[$type]['columns']) as $key) {
                    if (is_array($affiliate->$key)) {
                        foreach ($affiliate->$key as $id) {
                            if (!in_array($id, $this->container[$type]['columns'][$key]['values'])) {
                                $this->container[$type]['columns'][$key]['values'][] = $id;
                            }
                        }
                    } else {
                        if (!in_array($affiliate->$key, $this->container[$type]['columns'][$key]['values'])) {
                            $this->container[$type]['columns'][$key]['values'][] = $affiliate->$key;
                        }
                    }
                }
            }
            if (!is_numeric($affiliate->rating)) {
                $affiliate->rating = 0;
            }
            $this->setSchedule($affiliate->schedule);
        }
        if (count($affiliates)) {
            foreach ($this->container as $type => $instruction) {
                $columns = [];
                foreach ($instruction['columns'] as $name => $info) {
                    if (!empty($info['values'])) {
                        $this->container[$type]['callback']($this->container[$type]['data'], $info, $name);
                        $columns[$name] = $info;
                    }
                }
                if (!empty($columns)) {
                    $instruction['callback_after']($this->container[$type]['data'], $affiliates, $columns);
                }
            }
        }
    }

    private function getQuery()
    {
        $orderBy = [];
        foreach ($this->query['order'] as $column => $duration) {
            $orderBy[] = $column . ' ' . $duration;
        }
        return 'SELECT ' . implode(',', $this->query['columns']) .
            ' FROM ' . $this->query['table'] . (
            !empty($this->query['where']) ?
                ' WHERE ' . implode(' AND ', $this->query['where']) :
                ''
            ) . ' ORDER BY ' . implode(',', $orderBy);
    }

    private function provokeCallback($name)
    {
        foreach ($this->instructions as $field => $instruction) {
            $value = $this->_input->getString($field, false);
            if ($value) {
                if (
                    isset($instruction['callbacks'][$name]) &&
                    is_object($instruction['callbacks'][$name])) {
                    if (isset($instruction['json']) && $instruction['json'] === true) {
                        if ($this->isJSON($value)) {
                            $value = json_decode($value);
                            if (empty($value)) {
                                continue;
                            }
                        } else {
                            continue;
                        }
                    }
                    if (isset($instruction['filter']) && $instruction['filter'] === true) {
                        $this->activeFilters[$field] = $value;
                    }
                    $instruction['callbacks'][$name]($value);
                }
            }
        }
    }

    private function updateOptions(&$array, $options)
    {
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                if (isset($array[$key])) {
                    $array[$key] = $value;
                }
            }
        }
    }

    private function keyTranslates($type, $key)
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
}
