<?php defined('_JEXEC') or die;

class LasercityModelClinics extends JModelList
{
    private function checkJson(&$obj, $keys)
    {
        foreach ($keys as $key) {
            if (Search::isJSON($obj->$key)) {
                $obj->$key = json_decode($obj->$key);
            }
        }
    }

    private function setActions($instruction, &$obj, &$ids)
    {
        foreach ($instruction as $key => $actions) {
            if (in_array('decode', $actions)) {
                $obj->$key = json_decode($obj->$key);
            }
            if (in_array('unique', $actions)) {
                if (!isset($ids[$key])) {
                    $ids[$key] = array();
                }
                $obj->$key = (array)$obj->$key;
                foreach ($obj->$key as $item) {
                    if (!in_array($item, $ids[$key])) {
                        $ids[$key][] = $item;
                    }
                }
            }
            if (in_array('sort', $actions)) {
                asort($ids[$key]);
            }
            if (in_array('first', $actions)) {
                $obj->$key = reset($obj->$key);
            }
            if (in_array('schedule', $actions)) {
                Search::setSchedule($obj->$key);
            }
        }
    }

    public function getItems($ajax_load=null)
    {
        $time_start = microtime(true);
        $input = JFactory::$application->input;
        $db = JFactory::getDbo();

        if ($input->get('to_filter_data') === null && empty($_GET) || (count($_GET) == 1 && isset($_GET['page'])) || !empty($ajax_load)) {
            $city_id = $input->getInt('current_city_id', '0');
            $language = JFactory::$language->getTag();
            $affiliates = $db->setQuery("
SELECT
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$language}`
        WHERE
            `object_name`='affiliate' AND
            `object_column`='title' AND
            `object_id`=`affiliate`.`id`
    ) as `title`,
    (
        SELECT `line` FROM `#__lasercity_metro` WHERE `id`=`affiliate`.`metro`
    ) as `metro_line`,  
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$language}`
        WHERE
            `object_name`='metro' AND
            `object_column`='title' AND
            `object_id`=`affiliate`.`metro`
    ) as `metro`,
    (
        SELECT `type` FROM `#__lasercity_locations` WHERE `id`=`affiliate`.`location`
    ) as `location_type`,  
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$language}`
        WHERE
            `object_name`='location' AND
            `object_column`='title' AND
            `object_id`=`affiliate`.`location`
    ) as `location`,
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$language}`
        WHERE
            `object_name`='district' AND
            `object_column`='title' AND
            `object_id`=`affiliate`.`district`
    ) as `district`,
    `id`, `main_image`, `alias`, `organization`, `phones`, `comforts`, `apparats`, `schedule`, `coordinate`, `number_home`
FROM `#__lasercity_affiliates` as `affiliate`
WHERE `city`={$city_id} AND `organization`
            ")->loadObjectList();

            $ids = array();
            foreach ($affiliates as $affiliate) {
                $this->setActions(array(
                    'organization' => ['unique', 'sort', 'first'],
                    'phones' => ['decode'],
                    'comforts' => ['decode', 'unique', 'sort'],
                    'apparats' => ['decode', 'unique', 'sort'],
                    'schedule' => ['decode', 'schedule'],
                ), $affiliate, $ids);
                $affiliate->coordinate = 'https://www.google.com/maps?q=' . $affiliate->coordinate;
                $fullLocation = array();
                $affiliate->location_type = JText::_('COM_LASERCITY_TABLE_' . mb_strtoupper($affiliate->location_type) . '_S');
                if ($affiliate->location != null) {
                    $fullLocation[] = $affiliate->location_type . ' ' . $affiliate->location;
                }
                if ($affiliate->number_home != null) {
                    $fullLocation[] = $affiliate->number_home;
                }
                if ($affiliate->district != null) {
                    $fullLocation[] = $affiliate->district;
                }
                unset($affiliate->location_type);
                unset($affiliate->location);
                unset($affiliate->number_home);
                unset($affiliate->district);
                $affiliate->location = implode(', ', $fullLocation);
            }

            $apparatIds = implode(',', $ids['apparats']);
            $apparatus = $db->setQuery("SELECT `id`, `title` FROM `#__lasercity_apparats` WHERE `id` IN ({$apparatIds})")->loadObjectList('id');

            $comfortIds = implode(',', $ids['comforts']);
            $comforts = $db->setQuery("
SELECT `id`, `icon`,
(
        SELECT `value`
        FROM `#__lasercity_translations_{$language}`
        WHERE
            `object_name`='comfort' AND
            `object_column`='title' AND
            `object_id`=`comfort`.`id`
) as `title`
FROM `#__lasercity_comforts` as `comfort`
WHERE `id` IN({$comfortIds})
            ")->loadAssocList('id');

            foreach ($affiliates as $affiliate) {
                foreach ($affiliate->apparats as $id => $c) {
                    $affiliate->apparats[$id] = $apparatus[$c]->title;
                }
                foreach ($affiliate->comforts as $id => $comfort) {
                    $affiliate->comforts[$id] = $comforts[$comfort];
                }
            }

            $organizationIds = implode(',', $ids['organization']);
            $organizations = $db->setQuery("
SELECT 
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$language}`
        WHERE
            `object_name`='organization' AND
            `object_column`='title' AND
            `object_id`=`organization`.`id`
    ) as `title`,
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$language}`
        WHERE
            `object_name`='organization' AND
            `object_column`='type' AND
            `object_id`=`organization`.`id`
    ) as `type`,
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$language}`
        WHERE
            `object_name`='organization' AND
            `object_column`='description' AND
            `object_id`=`organization`.`id`
    ) as `description`, `id`, `logo`, `alias`
FROM `#__lasercity_organizations` as `organization`
WHERE `id` IN({$organizationIds})
            ")->loadObjectList();

            foreach ($organizations as $organization) {
                $organization->affiliates = array();
                $organization->apparatus = array();
                $organization->comforts = array();
                foreach ($affiliates as $id => $affiliate) {
                    if ($affiliate->organization == $organization->id) {
                        $organization->affiliates[] = $affiliate;
                        foreach ($affiliate->apparats as $apparatus) {
                            if (!in_array($apparatus, $organization->apparatus)) {
                                $organization->apparatus[] = $apparatus;
                            }
                        }
                        unset($affiliate->apparats);
                        foreach ($affiliate->comforts as $comfort) {
                            if (!isset($organization->comforts[$comfort['id']])) {
                                $organization->comforts[$comfort['id']] = $comfort;
                            }
                        }
                        unset($affiliate->comforts); //
                        unset($affiliates[$id]);
                    }
                }
            }

            //todo: разбитие на страници
            $page = $input->getInt('page', 1);
            $count = count($organizations);
            $limit = 7;
            $pages = ceil($count / $limit);
            $organizations = array_slice($organizations, ($page - 1) * $limit, $limit);

            $result['affiliates'] = $organizations;
            $result['search_info']['count'] = $count;
            $result['search_info']['page'] = $page;
            $result['search_info']['pages'] = $pages;
            $result['search_info']['config']['limit'] = $limit;
            $result['get_setter_string'] = '';
            $result['organizations'] = true;

            /*echo '<pre>';
            print_r($organizations);
            echo '</pre>';*/
        } else {
            $result['affiliates'] = Search::getResult(array(
                'limit' => 7
            ));

            $result['search_info'] = Search::getInfo(); //

            $result['active_filters'] = Search::getActiveFiltersAndRemoveLinks();

            if (isset($result['search_info']['search_block']['services'])) {
                $prices = [];

                foreach ($result['affiliates'] as $affiliate) {
                    $affiliate->priceData = array();
                    foreach ($affiliate->prices as $price) {
                        if (!in_array($price, $prices)) {
                            $prices[] = $price;
                        }
                    }
                }

                $prices = implode(',', $prices);

                $services = [];
                foreach ($result['search_info']['search_block']['services'] as $service) {
                    $services[] = "'{$service}'";
                }

                $services = implode(',', $services);

                $services = $db->setQuery("
SELECT `id`, `alias` FROM `#__lasercity_services` WHERE `alias` IN ({$services});
                ")->loadObjectList('id');

                $servicesIds = [];

                foreach ($services as $service) {
                    $servicesIds[] = $service->id;
                }

                $servicesIdsString = implode(',', $servicesIds);

                $prices = $db->setQuery("SELECT `price_id` FROM `#__lasercity_to_filters` WHERE `service_id` IN ({$servicesIdsString}) AND `price_id` IN ({$prices})")->loadObjectList();

                foreach ($prices as $i => $price) {
                    $prices[$i] = $price->price_id;
                }

                $prices = implode(',', $prices);

                $prices = $db->setQuery("
SELECT `id`, `sex`, (SELECT `title` FROM `#__lasercity_apparats` as `apparat` WHERE `apparat`.`id`=`main`.apparat) as `apparatus`, `data` FROM `#__lasercity_prices` as `main` WHERE `id` IN({$prices})
                ")->loadObjectList();

                $pricesData = [];

                foreach ($prices as $price) {
                    $price->data = json_decode($price->data);
                    foreach ($price->data as $i => $datum) {
                        if (!in_array($datum->service, $servicesIds)) {
                            unset($price->data[$i]);
                        }
                    }
                    foreach ($price->data as $datum) {
                        $data['price_id'] = $price->id;
                        $data['sex'] = $price->sex;
                        $data['apparatus'] = $price->apparatus;
                        $data['service'] = null;
                        foreach ($result['active_filters'] as $active_filter) {
                            if ($services[$datum->service]->alias == $active_filter->alias) {
                                $data['service'] = $active_filter->title;
                                break;
                            }
                        }

                        $data['price'] = $datum->price;
                        $pricesData[] = $data;
                    }
                }

                foreach ($result['affiliates'] as $affiliate) {
                    foreach ($pricesData as $datum) {
                        if (in_array($datum['price_id'], $affiliate->prices)) {
                            $affiliate->priceData[]= $datum;
                        }
                    }
                }
            }

            $get_setter = array();

            foreach ($result['active_filters'] as $active_filter) {
                $get_setter[$active_filter->type][] = $active_filter->alias;
            }

            $get_setter_string = null;

            if (!empty($get_setter)) {
                $get_setter_string = array();

                foreach ($get_setter as $key => $items) {
                    $get_setter_string[] = $key . '=' . json_encode($items);
                }

                $get_setter_string = implode('&amp;', $get_setter_string);
            }

            $result['get_setter_string'] = $get_setter_string;
        }

        $result['all_time'] = microtime(true) - $time_start . ' sec';

        return $result;
    }
}

/*function file_get_contents() {
    echo 'wef';
}

file_get_contents();*/
