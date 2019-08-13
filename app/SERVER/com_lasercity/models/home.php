<?php defined('_JEXEC') or die;

class lasercityModelHome extends JModelItem
{
    function setPrice($prices) {
        $result = array();

        foreach ($prices as $price) {
            foreach ($price->data as $service) {
                if(!empty($service->zones)) {
                    foreach ($service->zones as $zone) {
                        $item['title'] = $service->service_text;
                        $item['alias'] = $service->alias;
                        /*$item['duration'] = $service->duration;
                        $item['price'] = $service->price;
                        $item['service_id'] = $service->service;
                        $item['aparat_id'] = $price->aparat_id;
                        $item['percent'] = $service->percent;
                        $item['start_sale'] = $service->start_sale;
                        $item['end_sale'] = $service->end_sale;*/
                        $result[$price->sex][$zone][] = $item;
                    }
                }
            }
        }

        return $result;
    }

    function getFilterServices()
    {

        $obj_city = CityHelper::getCurrent();
        $city_id = $obj_city->id;

        $db = JFactory::getDbo();

        $lang_tag = JFactory::getLanguage()->getTag();

        /*$db->setQuery("SELECT prices FROM `#__lasercity_affiliates` WHERE published='1' AND city='$city_id'");

        $affiliates = $db->loadObjectList();

        $affiliate_prices = array();

        foreach ($affiliates as $affiliate) {
            $affiliate->prices = trim(str_replace(array("[","]"),'',$affiliate->prices));
            if (!empty($affiliate->prices)) {
                //echo "<br>"."'".$$affiliate->prices."'";
                $prices_ids = '"'.implode('","', explode(",",$affiliate->prices)).'"';
//exit($prices_ids);
                $db->setQuery("SELECT
                                IF(`sex`, 'men', 'women') as `sex`, `data`
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
                    $services_ids = '"' . implode('","', $services) . '"';

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
                        $zones_ids = '"' . implode('","', $zones) . '"';
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
                    $count = 0;
                    foreach ($prices as $price) {
                        foreach ($price->data as $datum) {
                            $datum->service_text = @$services[$datum->service]->value;
                            $datum->zones = @$services_id_list[$datum->service]->zones;
                            $datum->alias = @$services_id_list[$datum->service]->alias;
                            $count++;
                        }
                    }
                }

                $affiliate_prices[] = self::setPrice($prices);
                $affiliate_prices['all'] = $count;

            }
        }
        /*echo '<pre class="visually-hidden">';
        print_r($affiliate_prices);
        echo '</pre>';*/
        //return $affiliate_prices;

    }
}