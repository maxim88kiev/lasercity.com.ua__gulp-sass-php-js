<?php defined('_JEXEC') or die;

class ContentLoader
{
    private static $data = array(
        'modals' => array(),
        'priorityScripts' => array(),
        'scripts' => array(),
    );

    private static function add($type, $fileName, $template)
    {
        $path = str_replace('{$:name:$}', $fileName, $template);
        $dir = JPATH_BASE . $path;
        if (file_exists($dir) && !in_array($path, self::$data[$type])) {
            self::$data[$type][] = $path;
        }
    }

    public static function addScript($fileName, $priority = false)
    {
        self::add($priority ? 'priorityScripts' : 'scripts', $fileName, '/templates/lasercity/js/{$:name:$}.js');
    }

    public static function addPopup($fileName)
    {
        self::add('modals', $fileName, '/templates/lasercity/crutch/{$:name:$}.php');
    }

    public static function drawScripts()
    {
        foreach (array_merge(self::$data['priorityScripts'], self::$data['scripts']) as $script) {
            echo "<script src='{$script}'></script>\r\n";
        }
    }

    public static function drawModals()
    {
        foreach (self::$data['modals'] as $modal) {
            require_once JPATH_SITE . $modal;
        }
    }

    public static function getStockTotal() {
        $db = JFactory::getDbo();

        $db->setQuery("SELECT COUNT(*) as all_count 
                      FROM `#__lasercity_stock` 
                      WHERE published='1'");

        $result = $db->loadObjectList();
        foreach ($result as $row) {
            return $row->all_count;
        }
        return 0;
    }

    public static function getReviewTotal() {
        $db = JFactory::getDbo();

        $db->setQuery("SELECT COUNT(*) as all_count 
                      FROM `#__lasercity_reviews` 
                      WHERE published='1'");

        $result = $db->loadObjectList();
        foreach ($result as $row) {
            return $row->all_count;
        }
        return 0;
    }

    public static function getAnswerTotal($format=null,$param=null) {
        $db = JFactory::getDbo();

        $sql = "SELECT COUNT(*) as all_count 
                      FROM `#__lasercity_answer` 
                      WHERE published='1' ";

        switch ($format){
            case 'question':
                $sql .= " AND question_id='".(int)$param."' ";
                break;
        }

        $db->setQuery($sql);

        $result = $db->loadObjectList();
        foreach ($result as $row) {
            return $row->all_count;
        }
        return 0;
    }

    public static function getSubscriptionTotal() {
        $db = JFactory::getDbo();

        $db->setQuery("SELECT COUNT(*) as all_count 
                      FROM `#__lasercity_subscription` 
                      WHERE published='1'");

        $result = $db->loadObjectList();
        foreach ($result as $row) {
            return $row->all_count;
        }
        return 0;
    }

    public static function getWriteServiceTotal($format=null,$param=null) {
        $db = JFactory::getDbo();

        $sql = "SELECT COUNT(*) as all_count 
                      FROM `#__lasercity_by_services` WHERE id>0 ";

        switch ($format){
            case 'stock':
                $sql .= " AND stock_id='".(int)$param."' ";
                break;
            case 'day':
                $sql .= " AND DATE(date_added)=DATE(NOW()) ";
                break;
        }

        $db->setQuery($sql);

        $result = $db->loadObjectList();
        foreach ($result as $row) {
            return $row->all_count;
        }
        return 0;
    }

    public static function getWriteStockTotal($format=null,$param=null) {
        $db = JFactory::getDbo();

        $sql = "SELECT COUNT(*) as all_count 
                      FROM `#__lasercity_stock` WHERE id>0 ";

        switch ($format){
            case 'affiliate':
                $sql .= " AND JSON_CONTAINS(affiliate_id, '".(int)$param."', '$') ";
                //$sql .= " AND JSON_SEARCH(affiliate_id, 'one', '".(int)$param."') IS NOT NULL ";
                //$sql .= " AND `affiliate_id`->>'$.\"".(int)$param."\"' IS NOT NULL ";
                break;
        }

        $db->setQuery($sql);

        $result = $db->loadObjectList();
        foreach ($result as $row) {
            return $row->all_count;
        }
        return 0;
    }

    public static function getApplications($status,$user_id) {
        $db = JFactory::getDbo();

        $sql = "SELECT COUNT(*) as all_count 
                      FROM `#__lasercity_by_services` 
                      WHERE status='$status' 
                      AND arhiv='0' 
                      AND user_id='$user_id' ";

        $db->setQuery($sql);

        $result = $db->loadObjectList();
        foreach ($result as $row) {
            return $row->all_count;
        }
        return 0;
    }

    public static function getApplicationByOrganization($status,$organization_id) {
        $db = JFactory::getDbo();

        $sql = "SELECT COUNT(*) as all_count 
                      FROM `#__lasercity_by_services` as s
                      LEFT JOIN `#__lasercity_affiliates` as a ON s.affiliate_id=a.id
                      LEFT JOIN `#__lasercity_organizations` as o ON a.organization=o.id 
                      WHERE s.status='$status' 
                      AND s.arhiv='0'
                      AND o.id='$organization_id' ";

        $db->setQuery($sql);

        $result = $db->loadObjectList();
        foreach ($result as $row) {
            return $row->all_count;
        }
        return 0;
    }

    public static function getNameOrganizationById($organization_id) {
        $db = JFactory::getDbo();
        $lang_tag = JFactory::getLanguage()->getTag();

        $sql = "SELECT t.value  
                    FROM `#__lasercity_organizations` as o 
                    LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON o.id=t.object_id 
                    WHERE o.id='$organization_id'
                    AND t.object_column='title' 
                    AND t.object_name='organization'";

        $db->setQuery($sql);

        $result = $db->loadObjectList();
        foreach ($result as $row) {
            return $row->value;
        }
        return 0;
    }

    public static function getTimeSalonNoreflex($organization_id) {
        $db = JFactory::getDbo();

        $sql = "SELECT TIME_FORMAT(TIMEDIFF(MAX(date_added),MIN(date_added)),'%H') as times
                      FROM `#__lasercity_by_services` as s
                      LEFT JOIN `#__lasercity_affiliates` as a ON s.affiliate_id=a.id
                      LEFT JOIN `#__lasercity_organizations` as o ON a.organization=o.id 
                      WHERE s.status='0' 
                      AND s.arhiv='0' 
                      AND o.id='$organization_id' ";

        $db->setQuery($sql);

        $result = $db->loadObjectList();
        foreach ($result as $row) {
            return $row->times;
        }
        return 0;
    }

    //affiliate or organization
    public static function getRaiting($id,$format=null) {

        $db = JFactory::getDbo();

        $column = !empty($format) ? $format : 'affiliate_id';

        $db->setQuery("SELECT 
                      COUNT(*) as all_count,
                      
                      AVG(place) as place_avg,
                      AVG(relationship) as relationship_avg,
                      AVG(purity) as purity_avg,
                      AVG(quality) as quality_avg,
                      AVG(price) as price_avg,
                      
                      SUM(place) as place_sum,
                      SUM(relationship) as relationship_sum,
                      SUM(purity) as purity_sum,
                      SUM(quality) as quality_sum,
                      SUM(price) as price_sum,
                      
                       (SELECT COUNT(*) 
                        FROM `#__lasercity_reviews`
                        WHERE $column = '".$id."' 
                        AND published='1'
                        AND (place='1' OR relationship='1' OR purity='1' OR quality='1' OR price='1')
                        ) as stars_1
                        ,
                        (SELECT COUNT(*) 
                        FROM `#__lasercity_reviews`
                        WHERE $column = '".$id."' 
                        AND published='1'
                        AND (place='2' OR relationship='2' OR purity='2' OR quality='2' OR price='2')
                        ) as stars_2
                        ,
                        (SELECT COUNT(*) 
                        FROM `#__lasercity_reviews`
                        WHERE $column = '".$id."' 
                        AND published='1'
                        AND (place='3' OR relationship='3' OR purity='3' OR quality='3' OR price='3')
                        ) as stars_3
                        ,
                        (SELECT COUNT(*) 
                        FROM `#__lasercity_reviews`
                        WHERE $column = '".$id."' 
                        AND published='1'
                        AND (place='4' OR relationship='4' OR purity='4' OR quality='4' OR price='4')
                        ) as stars_4
                        ,
                        (SELECT COUNT(*) 
                        FROM `#__lasercity_reviews`
                        WHERE $column = '".$id."' 
                        AND published='1'
                        AND (place='5' OR relationship='5' OR purity='5' OR quality='5' OR price='5')
                        ) as stars_5
                        
                      FROM `#__lasercity_reviews` 
                      WHERE $column = '".$id."' 
                      AND published='1'");

        $result = $db->loadObjectList();
        $ratings_all = 0;
        $ratings = array();
        $stars = array();
        $percent = array();
        $criterion = array();
        $avg = array();

        foreach ($result as $row) {
            $ratings['1'] = $row->stars_1;
            $ratings['2'] = $row->stars_2;
            $ratings['3'] = $row->stars_3;
            $ratings['4'] = $row->stars_4;
            $ratings['5'] = $row->stars_5;

            $ratings_all = $row->all_count;

            $stars['1'] = $row->stars_1;
            $stars['2'] = $row->stars_2;
            $stars['3'] = $row->stars_3;
            $stars['4'] = $row->stars_4;
            $stars['5'] = $row->stars_5;

            $percent['1'] = (array_sum($stars)>0) ? floor($row->stars_1*100/array_sum($stars)) : 0;
            $percent['2'] = (array_sum($stars)>0) ? floor($row->stars_2*100/array_sum($stars)) : 0;
            $percent['3'] = (array_sum($stars)>0) ? floor($row->stars_3*100/array_sum($stars)) : 0;
            $percent['4'] = (array_sum($stars)>0) ? floor($row->stars_4*100/array_sum($stars)) : 0;
            $percent['5'] = (array_sum($stars)>0) ? floor($row->stars_5*100/array_sum($stars)) : 0;

            $criterion['place'] = (!empty($row->place_sum)) ? $row->place_sum : 0;
            $criterion['relationship'] = (!empty($row->relationship_sum)) ? $row->relationship_sum : 0;
            $criterion['purity'] = (!empty($row->purity_sum)) ? $row->purity_sum : 0;
            $criterion['quality'] = (!empty($row->quality_sum)) ? $row->quality_sum : 0;
            $criterion['price'] = (!empty($row->price_sum)) ? $row->price_sum : 0;

            $avg['place'] = ($row->place_avg!='NAN') ? floor($row->place_avg) : 0;
            $avg['relationship'] = ($row->relationship_avg!='NAN') ? floor($row->relationship_avg) : 0;
            $avg['purity'] = ($row->purity_avg!='NAN') ? floor($row->purity_avg) : 0;
            $avg['quality'] =($row->quality_avg!='NAN') ?  floor($row->quality_avg) : 0;
            $avg['price'] = ($row->price_avg!='NAN') ? floor($row->price_avg) : 0;
        }

        $totalWeight = 0;
        $totalReviews = 0;

        foreach ($ratings as $weight => $numberofReviews) {
            $WeightMultipliedByNumber = $weight * $numberofReviews;
            $totalWeight += $WeightMultipliedByNumber;
            $totalReviews += $numberofReviews;
        }

        //divide the total weight by total number of reviews
        //$averageRating = ($totalReviews > 0) ? $totalWeight / $totalReviews : 0;
        $averageRating = ($totalReviews > 0) ? $totalWeight / $totalReviews : 0;

    //echo $totalWeight.'======'.$totalReviews;

        return ['raiting'=>number_format((float)$averageRating, 1, ',', ''),
                'count_raiting'=>$ratings_all,
                'stars'=>$stars,
                'percent'=>$percent,
                'criterion'=>$criterion,
                'avg'=>$avg];
    }

    public static function getAparatsByArray($array){
        $db = JFactory::getDbo();

        $query = "SELECT title 
                  FROM `#__lasercity_apparats`
                  WHERE id IN('" . implode("','", $array) . "')
                  AND published='1' ";
        $db->setQuery($query);
        $result = $db->loadObjectList();

        $apparat = array();
        foreach ($result as $row) {
            $apparat[] = $row->title;
        }

        return $apparat;
    }

    public static function getServisesByArray($array){
        $db = JFactory::getDbo();
        $lang_tag = JFactory::getLanguage()->getTag();

        $query = "SELECT t.value,s.id 
                    FROM `#__lasercity_services` as s 
                    LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON t.object_id = s.id 
                    WHERE s.id IN('" . implode("','", $array) . "')
                    AND t.object_column='title' 
                    AND t.object_name='service' 
                    AND s.published='1' ";

        $db->setQuery($query);
        $result = $db->loadObjectList();

        $service = array();
        foreach ($result as $row) {
            $service[] = $row;
        }

        return $service;
    }

    public static function getServisesByPrice($prices_id,$service_id){

        $json = null;

        $db = JFactory::getDbo();
        $lang_tag = JFactory::getLanguage()->getTag();

        $query = array();

        for ($i=0;$i<count($prices_id);$i++) {
            $query[] = "(SELECT p.id as prices_id, p.sex, p.apparat as apparat_id, p.data->>'$**.\"service\"' as service,p.data as prices,a.title as apparat_name 
                  FROM `#__lasercity_prices` as p 
                  LEFT JOIN `#__lasercity_apparats` as a ON p.apparat = a.id 
                  WHERE p.id='".(int)$prices_id[$i]."'
                  AND p.published='1') ";
        }
        $db->setQuery(implode(' UNION ALL ',$query));

        $result = $db->loadObjectList();
//echo $query;
        //узнаем услуги в прайсе
        $i=0;
        foreach ($result as $row) {
            //exit(var_dump(json_decode($row->prices, true)));

            //echo implode("','",array_unique($service_id));

            //ищем услугу по названию
            $query = "SELECT s.id,t.value FROM `#__lasercity_services` as s 
              LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON t.object_id = s.id 
              WHERE s.id='".(int)$service_id[$i]."'
              AND t.object_column='title' 
              AND t.object_name='service' 
              AND s.published='1' ";

            $db->setQuery($query);
            $res = $db->loadObjectList();

            foreach ($res as $row_s) {

                $price = 0;
                $duration = 0;
                $mas_data = json_decode($row->prices, true);
                if ($mas_data) {
                    foreach ($mas_data as $data) {
                        if ($data['service'] == $row_s->id) {
                            $price = $data['price'];
                            $duration = $data['duration'];
                            break;
                        }
                    }
                }

                $json[] = array(
                    'service_id' => $row_s->id,
                    'prices_id' => $row->prices_id,
                    'service' => $row_s->value,
                    'apparat' => $row->apparat_id,
                    'price_digit' => preg_replace('/[^0-9]/','',$price),
                    'duration' => $duration,
                    'price' => $price,
                    'apparat_name' => $row->apparat_name,
                    'persones' => $row->sex
                );
            }
            $i++;
        }

        return $json;
    }

    public static function getServisesByPriceAndSex($prices_id,$service_id){

        $json = null;

        $db = JFactory::getDbo();
        $lang_tag = JFactory::getLanguage()->getTag();

        $query = array();

        for ($i=0;$i<count($prices_id);$i++) {
            $query[] = "(SELECT p.id as prices_id, p.sex, p.apparat as apparat_id, p.data->>'$**.\"service\"' as service,p.data as prices,a.title as apparat_name 
                  FROM `#__lasercity_prices` as p 
                  LEFT JOIN `#__lasercity_apparats` as a ON p.apparat = a.id 
                  WHERE p.id='".(int)$prices_id[$i]."'
                  AND p.published='1') ";
        }
        $db->setQuery(implode(' UNION ALL ',$query));
        $result = $db->loadObjectList();
//echo $query;
        //узнаем услуги в прайсе
        $i=0;
        foreach ($result as $row) {
            //exit(var_dump(json_decode($row->prices, true)));

            //echo implode("','",array_unique($service_id));

            //ищем услугу по названию
            $query = "SELECT s.id,t.value FROM `#__lasercity_services` as s 
              LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON t.object_id = s.id 
              WHERE s.id='".(int)$service_id[$i]."'
              AND t.object_column='title' 
              AND t.object_name='service' 
              AND s.published='1' ";

            $db->setQuery($query);
            $res = $db->loadObjectList();

            foreach ($res as $row_s) {

                $price = 0;
                $duration = 0;
                $mas_data = json_decode($row->prices, true);
                if ($mas_data) {
                    foreach ($mas_data as $data) {
                        if ($data['service'] == $row_s->id) {
                            $price = $data['price'];
                            $duration = $data['duration'];
                            break;
                        }
                    }
                }

                $json[] = array(
                    'service_id' => $row_s->id,
                    'prices_id' => $row->prices_id,
                    'service' => $row_s->value,
                    'apparat' => $row->apparat_id,
                    'price_digit' => preg_replace('/[^0-9]/','',$price),
                    'duration' => $duration,
                    'price' => $price,
                    'apparat_name' => $row->apparat_name,
                    'persones' => $row->sex
                );
            }
            $i++;
        }

        return $json;
    }

    public static function replaceSuffix($digit,$param,$register=null)
    {

        $current_sef = LangHelper::getCurrentSef();

        $arr_param['hour'] =
            [0 => ['ru' => 'час', 'ua' => 'година'],
                1 => ['ru' => 'часов', 'ua' => 'годин'],
                2 => ['ru' => 'часа', 'ua' => 'години']
            ];

        $arr_param['application'] =
            [0 => ['ru' => 'заявка', 'ua' => 'заявка'],
                1 => ['ru' => 'заявок', 'ua' => 'заявок'],
                2 => ['ru' => 'заявки', 'ua' => 'заявки']
            ];

        $arr_param['apparat'] =
            [0 => ['ru' => 'аппарат', 'ua' => 'апарат'],
                1 => ['ru' => 'аппаратов', 'ua' => 'апаратів'],
                2 => ['ru' => 'аппарата', 'ua' => 'апарата']
            ];

        $arr_param['day'] =
            [0 => ['ru' => 'день', 'ua' => 'день'],
                1 => ['ru' => 'дней', 'ua' => 'днів'],
                2 => ['ru' => 'дня', 'ua' => 'дні']
            ];

        $arr_param['question'] =
            [0 => ['ru' => 'вопрос', 'ua' => 'запитання'],
                1 => ['ru' => 'вопросов', 'ua' => 'запитань'],
                2 => ['ru' => 'вопроса', 'ua' => 'запитання']
            ];

        $arr_param['answer'] =
            [0 => ['ru' => 'ответ', 'ua' => 'відповідь'],
                1 => ['ru' => 'ответов', 'ua' => 'відповідей'],
                2 => ['ru' => 'ответа', 'ua' => 'відповіді']
            ];

        $arr_param['subscriber'] =
            [0 => ['ru' => 'подписчик', 'ua' => 'передплатник'],
                1 => ['ru' => 'подписчиков', 'ua' => 'передплатників'],
                2 => ['ru' => 'подписчика', 'ua' => 'передплатника']
            ];

        $arr_param['salon'] =
            [0 => ['ru' => 'салон', 'ua' => 'салон'],
                1 => ['ru' => 'салонов', 'ua' => 'салонів'],
                2 => ['ru' => 'салона', 'ua' => 'салона']
            ];

        $arr_param['user'] =
            [0 => ['ru' => 'посетитель', 'ua' => 'відвідувач'],
                1 => ['ru' => 'посетителей', 'ua' => 'відвідувачів'],
                2 => ['ru' => 'посетителя', 'ua' => 'відвідувача']
            ];

        $arr_param['see'] =
            [0 => ['ru' => 'просмотр', 'ua' => 'перегляд'],
                1 => ['ru' => 'просмотров', 'ua' => 'переглядів'],
                2 => ['ru' => 'просмотра', 'ua' => 'перегляда']
            ];
        $arr_param['like'] =
            [0 => ['ru' => 'лайк', 'ua' => 'лайк'],
                1 => ['ru' => 'лайков', 'ua' => 'лайків'],
                2 => ['ru' => 'лайка', 'ua' => 'лайка']
            ];
        $arr_param['write'] =
            [0 => ['ru' => 'запись', 'ua' => 'запис'],
                1 => ['ru' => 'записей', 'ua' => 'записів'],
                2 => ['ru' => 'записи', 'ua' => 'записа']
            ];
        $arr_param['people'] =
            [0 => ['ru' => 'человек', 'ua' => 'людина'],
                1 => ['ru' => 'человек', 'ua' => 'людей'],
                2 => ['ru' => 'человека', 'ua' => 'людини']
            ];
        $arr_param['review'] =
            [0 => ['ru' => 'отзыв', 'ua' => 'відгук'],
                1 => ['ru' => 'отзывов', 'ua' => 'відгуків'],
                2 => ['ru' => 'отзыва', 'ua' => 'відгука']
            ];
        $arr_param['adress'] =
            [0 => ['ru' => 'адрес', 'ua' => 'адреса'],
                1 => ['ru' => 'адресов', 'ua' => 'адрес'],
                2 => ['ru' => 'адреса', 'ua' => 'адреси']
            ];
        $arr_param['star'] =
            [0 => ['ru' => 'звезда', 'ua' => 'зірка'],
                1 => ['ru' => 'звезд', 'ua' => 'зірок'],
                2 => ['ru' => 'звезды', 'ua' => 'зірки']
            ];

        $str = '';
        $num = $digit > 100 ? substr($digit, -2) : $digit;
        if ($num >= 5 && $num <= 14) $str = $arr_param[$param][1][$current_sef];
        else {
            $num = substr($digit, -1);
            if ($num == 0 || ($num >= 5 && $num <= 9)) $str = $arr_param[$param][1][$current_sef];
            if ($num == 1) $str = $arr_param[$param][0][$current_sef];
            if ($num >= 2 && $num <= 4) $str = $arr_param[$param][2][$current_sef];
        }
        return ($register) ? mb_convert_case($str, MB_CASE_TITLE, 'UTF-8') : $str;
    }

}