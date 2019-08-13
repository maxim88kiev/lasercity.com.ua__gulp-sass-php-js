<?php defined('_JEXEC') or die;

class lasercityModelAffiliate extends JModelItem
{
    public function getItem($id = null)
    {
        $id = (!empty($id)) ? $id : (int)$this->getState('item.id');
        $lang_tag = JFactory::$language->getTag();

        $db = JFactory::getDbo();

        $db->setQuery("SELECT `id`,
`logo`, `main_image`, `alias`, `organization`, `phones`,
`prices`, `comforts`, `comforts`, `schedule`, `images`,
`number_home`, `coordinate`, `site`, `social_networks`, 
`premium`, `apparats` as `apparatus`,
(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='affiliate' AND 
    `t`.`object_column`='title' AND 
    `t`.`object_id`=`main`.`id`
) as `title`,
(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='affiliate' AND 
    `t`.`object_column`='description' AND 
    `t`.`object_id`=`main`.`id`
) as `description`,
(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='affiliate' AND 
    `t`.`object_column`='type' AND 
    `t`.`object_id`=`main`.`id`
) as `type`,
(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='organization' AND 
    `t`.`object_column`='type' AND 
    `t`.`object_id`=`main`.`organization`
) as `type_organization`,
(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='district' AND 
    `t`.`object_column`='title' AND 
    `t`.`object_id`=`main`.`district`
) as `district`,
(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='micro_district' AND 
    `t`.`object_column`='title' AND 
    `t`.`object_id`=`main`.`micro_district`
) as `micro_district`,
(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='metro' AND 
    `t`.`object_column`='title' AND 
    `t`.`object_id`=`main`.`metro`
) as `metro`,
(SELECT `line` FROM `#__lasercity_metro` as `t` WHERE 
    `t`.`id`=`main`.`metro`
) as `metro_line`,
(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='location' AND 
    `t`.`object_column`='title' AND 
    `t`.`object_id`=`main`.`location`
) as `location`,
(SELECT `type` FROM `#__lasercity_locations` as `t` WHERE `t`.`id`=`main`.`location`
) as `location_type`
FROM `#__lasercity_affiliates` as `main`
WHERE `id` IN({$id})
");
        $affiliate_obj = $db->loadObject();

        $json_ch = array(
            'phones', 'prices', 'comforts', 'schedule', 'images', 'social_networks', 'apparatus'
        );

        foreach ($json_ch as $ch) {
            if (Search::isJSON($affiliate_obj->$ch)) {
                $affiliate_obj->$ch = json_decode($affiliate_obj->$ch);
            } else {
                $affiliate_obj->$ch = array();
            }
        }

        $affiliate_obj->social_networks = SocialNetworkDetector::detect($affiliate_obj->social_networks);
        if ($affiliate_obj->location != null) {
            $affiliate_obj->location_type = Search::keyTranslates('location', $affiliate_obj->location_type);
            $affiliate_obj->location = $affiliate_obj->location_type . ' ' . $affiliate_obj->location;
            unset($affiliate_obj->location_type);
        }

        if (!empty($affiliate_obj->apparatus)) {
            $apparatus_ids = implode(',', $affiliate_obj->apparatus);
            $db->setQuery("SELECT `title` FROM `#__lasercity_apparats` WHERE `id` IN({$apparatus_ids})");
            $obj = $db->loadObjectList();
            $apparatus = array();
            foreach ($obj as $item) {
                $apparatus[] = $item->title;
            }
            $affiliate_obj->apparatus = $apparatus;
        }

        if (!empty($affiliate_obj->prices)) {
            $prices_ids = implode(',', $affiliate_obj->prices);
            $db->setQuery("SELECT
    IF(`sex`, 'men', 'women') as `sex`, (
        SELECT `title` FROM `#__lasercity_apparats` WHERE `id`=`main`.`apparat`
    ) as `apparatus`, `data`
    FROM `#__lasercity_prices` as `main`
    WHERE `id` IN ({$prices_ids})
");
            $services = array();
            $prices = $this->_db->loadObjectList();
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
                $this->_db->setQuery("SELECT `id`, `zones` FROM `#__lasercity_services` WHERE `id` IN({$services_ids})");

                $zones = array();
                $services_id_list = $this->_db->loadObjectList('id');

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
                    $this->_db->setQuery("SELECT `value`, `object_id` FROM `#__lasercity_translations_{$lang_tag}` WHERE `object_name`='zone' AND `object_column`='title' AND `object_id` IN({$zones_ids})");
                    $zones = $this->_db->loadObjectList('object_id');
                    foreach ($services_id_list as $item) {
                        foreach ($item->zones as $key => $info_zone_ids) {
                            $item->zones[$key] = $zones[$item->zones[$key]]->value;
                        }
                    }
                }

                $this->_db->setQuery("SELECT `value`, `object_id` FROM `#__lasercity_translations_{$lang_tag}` WHERE `object_name`='service' AND `object_column`='title' AND `object_id` IN({$services_ids})");
                $services = $this->_db->loadObjectList('object_id');
                foreach ($prices as $price) {
                    foreach ($price->data as $datum) {
                        $datum->service_text = $services[$datum->service]->value;
                        $datum->zones = $services_id_list[$datum->service]->zones;
                    }
                }
            }
            
            $affiliate_obj->prices = $this->setPrice($prices);
        }

        if (!empty($affiliate_obj->comforts)) {
            $comfort_ids = implode(',', $affiliate_obj->comforts);
            $this->_db->setQuery("SELECT
    `icon`,
    (SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
        `t`.`object_name`='comfort' AND 
        `t`.`object_column`='title' AND 
        `t`.`object_id`=`main`.`id`
    ) as `title`
FROM `#__lasercity_comforts` as `main`
WHERE `id` IN ({$comfort_ids}) 
");
            $affiliate_obj->comforts = $this->_db->loadObjectList();
        }

        if (!empty($affiliate_obj->schedule)) {
            $translates = array(
                'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'
            );
            $schedules = array();
            foreach ($affiliate_obj->schedule as $item) {
                $first = -1;
                $last = -1;
                for ($i = 0; $i < 7; $i++) {
                    if ($item[$i] == 1) {
                        $first = $first == -1 ? $i : $first;
                        $last = $i;
                    }
                }
                if ($first != $last) {
                    $days = $translates[$first] . '-' . $translates[$last];
                } else {
                    $days = $translates[$first];
                }
                $schedule['days'] = $days;
                $schedule['times'] = $item[7];
                $schedules[] = $schedule;
            }
            $affiliate_obj->schedule = $schedules;
        }

        return $affiliate_obj;
    }

    function setPrice($prices) {
        $result = array();

        foreach ($prices as $price) {
            foreach ($price->data as $service) {
                foreach ($service->zones as $zone) {
                    $item['title'] = $service->service_text;
                    $item['duration'] = $service->duration;
                    $item['price'] = $service->price;
                    /*$item['percent'] = $service->percent;
                    $item['start_sale'] = $service->start_sale;
                    $item['end_sale'] = $service->end_sale;*/
                    $result[$price->apparatus][$zone][$price->sex][] = $item;
                }
            }
        }
        
        return $result;
    }

    function populateState()
    {
        $app = JFactory::$application;
        $id = $app->input->getInt('id', 0);
        $this->setState('item.id', $id);
        parent::populateState();
    }
}