<?php defined('_JEXEC') or die;

class lasercityController extends JControllerLegacy
{
    protected $default_view = 'home';

    function getSalonNames()
    {
        $city_id = JFactory::$session->get('current_city_id');
        $language = JFactory::$language->getTag();
        $sef = LangHelper::getCurrentSef();

        $db = JFactory::getDbo();
        $query = "SELECT (
    SELECT `value`
    FROM `#__lasercity_translations_{$language}`
    WHERE
        `object_name`='affiliate' AND
        `object_column`='title' AND
        `object_id`=`affiliate`.`id`
) as `title`, `alias` as `href`
FROM `#__lasercity_affiliates` as `affiliate`
WHERE `city`={$city_id} AND `published`";

        $db->setQuery($query);

        $affiliates = $db->loadObjectList();

        foreach ($affiliates as $affiliate) {
            $affiliate->href = "/{$sef}/clinics/{$affiliate->href}";
        }

        die(json_encode($affiliates));
    }

    function getPlaceNames()
    {
        $db = JFactory::getDbo();
        $language = JFactory::$language->getTag();
        $sef = LangHelper::getCurrentSef();
        $city_id = JFactory::$session->get('current_city_id');
        $city = JFactory::$session->get('current_city_alias');

        if ($city == null) {
            $city = CityHelper::getCurrent();
        }

        $query = "
(
    SELECT (
        SELECT `value`
        FROM `#__lasercity_translations_{$language}`
        WHERE
            `object_name`='location' AND
            `object_column`='title' AND
            `object_id`=`location`.`id`
        ) as `title`, `type`,`alias` as `href`
        FROM `#__lasercity_locations` as `location`
    WHERE `city`={$city_id} AND `published`
)
UNION ALL 
(
    SELECT (
        SELECT `value`
        FROM `#__lasercity_translations_{$language}`
        WHERE
            `object_name`='metro' AND
            `object_column`='title' AND
            `object_id`=`metro`.`id`
        ) as `title`, 'metro', `alias`
    FROM `#__lasercity_metro` as `metro`
    WHERE `city`={$city_id} AND `published`
)
UNION ALL 
(
    SELECT (
        SELECT `value`
        FROM `#__lasercity_translations_{$language}`
        WHERE
            `object_name`='district' AND
            `object_column`='title' AND
            `object_id`=`district`.`id`
        ) as `title`, 'district', `alias`
    FROM `#__lasercity_districts` as `district`
    WHERE `city`={$city_id} AND `published`
)
";

        $db->setQuery($query);
        $locations = $db->loadObjectList();

        /*echo '<pre>';
        print_r($locations);
        echo '</pre>';*/

        foreach ($locations as $location) {
            $type = $location->type;
            unset($location->type);
            $location->title = JText::_('COM_LASERCITY_TABLE_' . mb_strtoupper($type) . '_S') . ' ' . $location->title;
            $location->href = "/{$sef}/clinics/{$city}/{$location->href}";
        }

        die(json_encode($locations));
    }

    function getMainInfo()
    {
        $result = array();
        $city_id = JFactory::$session->get('current_city_id');
        $city = JFactory::$session->get('current_city_alias');
        $language = JFactory::$language->getTag();
        $sef = LangHelper::getCurrentSef();

        if ($city == null) {
            $city = CityHelper::getCurrent();
        }

        $db = JFactory::getDbo();
        $db->setQuery("(
    SELECT `value` as `text`, 'district' as `type`, (
        SELECT `alias` FROM `#__lasercity_districts` WHERE `id`=`object_id`
    ) as `alias`, NULL as `dop_info`
    FROM `#__lasercity_translations_{$language}`
    WHERE
        `object_name`='district' AND
        `object_column`='title' AND
        `object_id` IN (SELECT DISTINCT `district` FROM `#__lasercity_affiliates` WHERE `city`={$city_id})
) UNION ALL (
    SELECT `value`, 'metro', (
        SELECT `alias` FROM `#__lasercity_metro` WHERE `id`=`object_id`
    ), NULL
    FROM `#__lasercity_translations_{$language}`
    WHERE
        `object_name`='metro' AND
        `object_column`='title' AND
        `object_id` IN (SELECT DISTINCT `metro` FROM `#__lasercity_affiliates` WHERE `city`={$city_id})
) UNION ALL (
    SELECT `value`, 'location', (
        SELECT `alias` FROM `#__lasercity_locations` WHERE `id`=`object_id`
    ), (
        SELECT `type` FROM `#__lasercity_locations` WHERE `id`=`object_id`
    )
    FROM `#__lasercity_translations_{$language}`
    WHERE
        `object_name`='location' AND
        `object_column`='title' AND
        `object_id` IN (SELECT DISTINCT `location` FROM `#__lasercity_affiliates` WHERE `city`={$city_id})
) UNION ALL (
    SELECT `value`, 'affiliate', (
        SELECT `alias` FROM `#__lasercity_affiliates` WHERE `id`=`object_id`
    ), NULL
    FROM `#__lasercity_translations_{$language}`
    WHERE
        `object_name`='affiliate' AND
        `object_column`='title' AND
        `object_id` IN (SELECT `id` FROM `#__lasercity_affiliates` WHERE `city`={$city_id})
) UNION ALL (
    SELECT `value`, 'service', (
        SELECT `alias` FROM `#__lasercity_services` WHERE `id`=`object_id`
    ), NULL
    FROM `#__lasercity_translations_{$language}`
    WHERE
        `object_name`='service' AND
        `object_column`='title' AND
        `object_id` IN (SELECT `id` FROM `#__lasercity_services`)
) UNION ALL (
    SELECT `title`, 'apparatus', `alias`, null FROM `#__lasercity_apparats`
) UNION ALL (
    SELECT `value`, 'comfort', (
        SELECT `alias` FROM `#__lasercity_comforts` WHERE `id`=`object_id`
    ), NULL
    FROM `#__lasercity_translations_{$language}`
    WHERE
        `object_name`='comfort' AND
        `object_column`='title' AND
        `object_id` IN (SELECT `id` FROM `#__lasercity_comforts`)
)
");
        $data = $db->loadObjectList();

        $translate = array(
            'district' => 'Район',
            'metro' => 'Метро',
            'location' => 'Локация',
            'affiliate' => 'Филиал',
            'service' => 'Услуга',
            'apparatus' => 'Аппарат',
            'comfort' => 'Комфорт'
        );

        foreach ($data as $datum) {
            $datum->link = "/{$sef}/clinics/" . ($datum->type == 'affiliate' ? null : "{$city}/") . $datum->alias;
            if ($datum->type == 'location') {
                $datum->text = JText::_('COM_LASERCITY_TABLE_' . mb_strtoupper($datum->dop_info) . '_S') . ' ' . $datum->text;
            }
            $obj['title'] = $datum->text;
            $obj['href'] = $datum->link;
            $obj['type'] = $translate[$datum->type];
            $result[] = $obj;
        }

        die(json_encode($result));
    }

    function getAffiliates()
    {
        jimport('components.com_lasercity.models.clinics', JPATH_BASE);

        $model = new LasercityModelClinics(array());

        $list = $model->getItems();
        $affiliates =$list['affiliates'];

        $sef = LangHelper::getCurrentSef();
        ob_start();
        require JPATH_BASE . '/templates/lasercity/crutch/clinics_draw_affiliates.php';
        $output = ob_get_contents();
        ob_end_clean();

        $result['html'] = $output;
        $result['search_info'] = $list['search_info'];//Search::getInfo();
        die(json_encode($result));
    }

    function getOrganizations()
    {
        $sef = LangHelper::getCurrentSef();

        require JPATH_BASE . "/components/com_lasercity/models/clinics.php";
        $clinicsModel = new LasercityModelClinics;

        $_GET['page'] = JFactory::getApplication()->input->getString('page');

        $result = $clinicsModel->getItems('yes');
        $organizations = $result['affiliates'];
        $result['affiliates'] = $organizations;
        //exit(var_dump($result['search_info']));

        ob_start();
        require JPATH_BASE . '/templates/lasercity/crutch/clinics_draw_organizations.php';
        $output = ob_get_contents();
        ob_end_clean();

        $result['html'] = $output;

        die(json_encode($result));
    }

    function getMainMapInfo()
    {
        $db = JFactory::getDbo();
        $city_id = JFactory::getSession()->get('current_city_id');
        $lang_tag = JFactory::getLanguage()->getTag();

        $db->setQuery("
SELECT
    IF (`organization`=0,`logo`,`main_image`) as `logo`, `alias`, `number_home`, `comforts`, `coordinate`,
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$lang_tag}`
        WHERE
            `object_name`='affiliate' AND
            `object_column`='title' AND 
            `object_id`=`main`.`id`
    ) as `title`,
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$lang_tag}`
        WHERE
            `object_name`='metro' AND
            `object_column`='title' AND 
            `object_id`=`main`.`metro`
    ) as `metro`,
    (
        SELECT `line`
        FROM `#__lasercity_metro`
        WHERE `id`=`main`.`metro`
    ) as `metro_line`,
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$lang_tag}`
        WHERE
            `object_name`='location' AND
            `object_column`='title' AND 
            `object_id`=`main`.`location`
    ) as `location`,
    (
        SELECT `type`
        FROM `#__lasercity_locations`
        WHERE `id`=`main`.`location`
    ) as `location_type`,
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$lang_tag}`
        WHERE
            `object_name`='district' AND
            `object_column`='title' AND 
            `object_id`=`main`.`district`
    ) as `district`,
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$lang_tag}`
        WHERE
            `object_name`='micro_district' AND
            `object_column`='title' AND 
            `object_id`=`main`.`micro_district`
    ) as `micro_district`,
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$lang_tag}`
        WHERE
            `object_name`=IF(`main`.`organization`=0,'affiliate','organization') AND
            `object_column`='type' AND 
            `object_id`=IF(`main`.`organization`=0,`main`.`id`,`main`.`organization`)
    ) as `type`
FROM `#__lasercity_affiliates` as `main`
WHERE `city`={$city_id} AND `coordinate`<>''
        ");
        $affiliates = $db->loadObjectList();
        $comforts = array();
        $sef = LangHelper::getCurrentSef();
        foreach ($affiliates as $affiliate) {
            $affiliate->href = "/{$sef}/clinics/{$affiliate->alias}";
            $affiliate->comforts = Search::isJSON($affiliate->comforts) ? json_decode($affiliate->comforts) : array();
            foreach ($affiliate->comforts as $comfort) {
                if (!in_array($comfort, $comforts)) {
                    $comforts[] = $comfort;
                }
            }
        }
        if (count($comforts)) {
            $db->setQuery("
SELECT `id`, `icon`,
(
    SELECT `value`
    FROM `#__lasercity_translations_{$lang_tag}`
    WHERE
        `object_name`='comfort' AND
        `object_column`='title' AND 
        `object_id`=`main`.`id`
) as `title`
FROM `#__lasercity_comforts` as `main`
WHERE `id` IN(" . implode(',', $comforts) . ")
            ");
            $comforts = $db->loadObjectList('id');
            foreach ($comforts as $comfort) {
                unset($comfort->id);
            }
            foreach ($affiliates as $affiliate) {
                foreach ($affiliate->comforts as $key => $comfort) {
                    $affiliate->comforts[$key] = $comforts[$comfort];
                }
            }
        }
        $to_array = array(
            'location',
            'number_home',
            'district',
            'micro_district'
        );
        foreach ($affiliates as $id => $affiliate) {
            $coordinates = explode(',', str_replace(' ', '', $affiliate->coordinate));
            $affiliate->location = Search::keyTranslates('location', $affiliate->location_type) . ' ' . $affiliate->location;
            unset($affiliate->location_type);
            $location_data = array();
            foreach ($to_array as $key) {
                if ($affiliate->$key != null) {
                    $location_data[] = $affiliate->$key;
                    unset($affiliate->$key);
                }
            }
            $affiliate->location_data = implode(', ', $location_data);
            if (count($coordinates) != 2) {
                unset($affiliates[$id]);
            } else {
                $affiliate->logo = $affiliate->logo == null ? 'https://placehold.it/101x78/000' : '/' . $affiliate->logo;
                unset($affiliate->coordinate);
                $affiliate->coordinateLat = (float)$coordinates[0];
                $affiliate->coordinateLng = (float)$coordinates[1];
            }
        }
        if (isset($_GET['show'])) {
            echo '<pre>';
            print_r($affiliates);
            echo '</pre>';
        } else {
            die(json_encode($affiliates));
        }
    }

    function getArticles()
    {
        $json = array();
        $tag = JFactory::$language->getTag();
        $db = JFactory::getDbo();
        $db->setQuery("
SELECT * FROM (
    SELECT `alias`,
        (
            SELECT `value`
            FROM `#__lasercity_translations_{$tag}`
            WHERE 
                `object_id`=`main`.`id` AND
                `object_column`='title' AND
                `object_name`='article'
        ) as `title`
    FROM `#__lasercity_articles` as `main`
    WHERE `published`
    ORDER BY `ordering` ASC, `date` DESC
) as `t`
        ");
        $articles = $db->loadObjectList();
        foreach ($articles as $article) {
            $obj = new stdClass;
            $obj->title = $article->title;
            $obj->href = "/articles/{$article->alias}";
            $json[] = $obj;
        }

        die(json_encode($json));
    }

    function getFaq()
    {
        $json = array();
        $tag = JFactory::$language->getTag();
        $db = JFactory::getDbo();
        $db->setQuery("
SELECT * FROM (
    SELECT `alias`,
        (
            SELECT `value`
            FROM `#__lasercity_translations_{$tag}`
            WHERE 
                `object_id`=`main`.`id` AND
                `object_column`='title' AND
                `object_name`='faq'
        ) as `title`
    FROM `#__lasercity_faq` as `main`
    WHERE `published`
    ORDER BY `ordering` ASC, `date` DESC
) as `t`
        ");
        $faq = $db->loadObjectList();
        foreach ($faq as $item) {
            $obj = new stdClass;
            $obj->title = $item->title;
            $obj->href = "/faq/{$item->alias}";
            $json[] = $obj;
        }

        die(json_encode($json));
    }

    function getCountUsers()
    {
        $db = JFactory::getDbo();
        $db->setQuery("SELECT COUNT(*) FROM `#__lasercity_last_visit` WHERE `object_type`='home'");
        die($db->loadResult());
    }

    function getOrganization()
    {

        $json = null;

        $lang_tag = JFactory::getLanguage()->getTag();
        $db = JFactory::getDbo();

        $post_find = JFactory::getApplication()->input->getString('organization');

        $query = "SELECT o.id,t.value FROM `#__lasercity_organizations` as o 
              LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON t.object_id = o.id 
              WHERE t.object_column='title' AND t.object_name='organization' AND o.published='1' AND t.value LIKE '" . $post_find . "%' ";

        $db->setQuery($query);
        $result = $db->loadObjectList();

        foreach ($result as $row) {
            $json[] = array(
                'id' => $row->id,
                'value' => $row->value,
                'option' => JText::_('COM_LASERCITY_ALL_SALON_SELECT_ORGANIZ')
            );
        }
        if(!is_array($json)){
            $json[] = array(
                'id' => 0,
                'value' => $post_find,
                'option' => JText::_('COM_LASERCITY_ALL_SALON_SELECT_ORGANIZ_SET')
            );
        }

        die(json_encode($json));
    }

    function setAuth($user)
    {
        JFactory::getSession()->set('my_user_id', $user['my_user_id']);
        JFactory::getSession()->set('my_user_name', $user['my_user_name']);
        JFactory::getSession()->set('my_user_avatar', $user['my_user_avatar']);
        JFactory::getSession()->set('my_user_rank', $user['my_user_rank']);
        JFactory::getSession()->set('my_user_phone', $user['my_user_phone']);
        JFactory::getSession()->set('tip_user', $user['tip_user']);
        JFactory::getSession()->set('organization_id', $user['organization_id']);
        $post_save_me = JFactory::getSession()->get('remember_me');
//exit($post_save_me);
        if (!empty($post_save_me)) {
            JFactory::getApplication()->input->cookie->set(
                'my_user_id',
                $user['my_user_id'],
                time() + 365 * 86400,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true
            );
            JFactory::getApplication()->input->cookie->set(
                'my_user_name',
                $user['my_user_name'],
                time() + 365 * 86400,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true
            );
            JFactory::getApplication()->input->cookie->set(
                'my_user_avatar',
                $user['my_user_avatar'],
                time() + 365 * 86400,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true
            );
            JFactory::getApplication()->input->cookie->set(
                'my_user_rank',
                $user['my_user_rank'],
                time() + 365 * 86400,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true
            );
            JFactory::getApplication()->input->cookie->set(
                'my_user_phone',
                $user['my_user_phone'],
                time() + 365 * 86400,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true
            );
            JFactory::getApplication()->input->cookie->set(
                'tip_user',
                $user['tip_user'],
                time() + 365 * 86400,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true
            );
            JFactory::getApplication()->input->cookie->set(
                'organization_id',
                $user['organization_id'],
                time() + 365 * 86400,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true
            );
        } else {
            JFactory::getApplication()->input->cookie->set('my_user_id', null, time() - 1,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true);
            JFactory::getApplication()->input->cookie->set('my_user_name', null, time() - 1,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true);
            JFactory::getApplication()->input->cookie->set('my_user_avatar', null, time() - 1,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true);
            JFactory::getApplication()->input->cookie->set('my_user_rank', null, time() - 1,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true);
            JFactory::getApplication()->input->cookie->set('my_user_phone', null, time() - 1,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true);
            JFactory::getApplication()->input->cookie->set('tip_user', null, time() - 1,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true);
            JFactory::getApplication()->input->cookie->set('organization_id', null, time() - 1,
                JFactory::getApplication()->get('cookie_path', '/'),
                JFactory::getApplication()->get('cookie_domain', ''),
                JFactory::getApplication()->isHttpsForced(),
                true);
        }
    }

    function setRemember()
    {
        $json = null;
        $post_remember = JFactory::getApplication()->input->getString('save_me');
        if (!empty($post_remember)) {
            JFactory::getSession()->set('remember_me', 'save');
            $json['success'] = JFactory::getSession()->get('remember_me');
        } else {
            JFactory::getSession()->clear('remember_me');
            $json['success'] = JFactory::getSession()->get('remember_me');
        }
        die(json_encode($json));
    }


    function getLanguageUri($slesh=null){
        $lang_tag = JFactory::getLanguage()->getTag();
        $db = JFactory::getDbo();
        //узнаем ссылку на язык
        $db->setQuery("SELECT sef FROM `#__languages`  
                      WHERE lang_code = '" . $lang_tag . "' ");
        $result = $db->loadObjectList();

        foreach ($result as $row) {
            if (!empty($row->sef)) {
                $language_uri = $row->sef.$slesh;
                break;
            }
        }
        return $language_uri;
    }

    function setEnter()
    {

        $json = null;

        $db = JFactory::getDbo();
        //узнаем ссылку на язык
        $language_uri = self::getLanguageUri("/");

        $post_enter_email = JFactory::getApplication()->input->getString('enter_email');
        $post_enter_password = JApplicationHelper::getHash(JFactory::getApplication()->input->getString('enter_password'));

        $query = "SELECT u.email,u.name,u.id as user_id,d.tip_user,d.organization_id,d.avatar,d.rank,d.phone 
                  FROM `#__users` as u 
                  LEFT JOIN `#__users_description` as d ON d.user_id = u.id 
                  WHERE u.email='$post_enter_email' AND u.password='$post_enter_password' ";

        $db->setQuery($query);
        $result = $db->loadObjectList();

        foreach ($result as $row) {
            if (!empty($row->user_id)) {
                $json['success'] = 'ok';

                self::setAuth(['my_user_id' => $row->user_id, 'my_user_name' => $row->name, 'my_user_avatar' => $row->avatar,  'my_user_phone' => $row->phone, 'my_user_rank' => $row->rank, 'tip_user' => $row->tip_user, 'organization_id' => $row->organization_id]);

                if(!empty($row->organization_id)){
                    $json['uri'] = '/' . $language_uri . 'records-salon/';
                }else {
                    $json['uri'] = '/' . $language_uri . 'records-client/';
                }

                break;
            }
        }

        if (empty($json['success'])) {
            $json['error']['enter_email'] = 'no';
            $json['error']['enter_password'] = JText::_('COM_LASERCITY_ERROR_EMAIL_OR_PASSWORD');
        }


        die(json_encode($json));
    }

    function sendLetterForRegister($array)
    {

        $post_password = '';
        if(!empty($array['password_temp'])) {
            $post_password = 'Ваш временный пароль: <b>' . $array['password_temp'] . '</b>';
        }

        $message = "
			<!DOCTYPE html>
			<head>
			<meta charset='utf-8'>
			<title>Регистрация на " . $_SERVER['SERVER_NAME'] . "</title>
			</head>
				<body>
					<div title='" . $_SERVER['SERVER_NAME'] . "' style='background:#F0F0F0;width:700px;height:auto;margin:auto;padding:20px 50px 40px 50px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;'>
					<a href='https://" . $_SERVER['SERVER_NAME'] . "/" . $array['language_uri'] . "/'>" . strtoupper($_SERVER['SERVER_NAME']) . "<!--<img src='https://" . $_SERVER['SERVER_NAME'] . "/img/logo.svg' alt=''>--></a>
						<div style='text-align:justify;margin-top:20px;padding:20px;background:white;font-size:15px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;-moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);-webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);'>
							<h3>Здравствуйте!</h3>
							".$post_password."
							Чтобы подтвердить регистрацию на " . $_SERVER['SERVER_NAME'] . ", нажмите на кнопку «Зарегистрироваться».
							<a href='" . $array['href_reg'] . "' style='text-decoration:none;'><div style='text-align:center;margin:auto;padding:10px;font-size:16px;font-weight:bold;color:white;background:#06c;margin-top:20px;white-space:nowrap;width:170px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;'>Зарегистрироваться</div></a>
						</div>
						<div style='text-align:justify;margin-top:20px;padding:20px;background:white;font-size:15px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;-moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);-webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);'>
							<h3>Важно знать</h3>
							Регистрация позволит Вам убрать рекламу с сайта, а также пользоваться всеми привелегиями сайта.
						</div>							
						<div style='font-size:12px;margin-top:20px;width:100%;text-align:center;color:#989898;'>Пожалуйста, не отвечайте на это сообщение оно было отправлено автоматически.</div>
					</div>
				</body>
			</html>";

        return $message;
    }
    

    function setRegistration()
    {
        $json = null;
        $db = JFactory::getDbo();

        $post_name = JFactory::getApplication()->input->getString('name');
        $post_register_for = JFactory::getApplication()->input->getString('register_for');
        $post_phone = JFactory::getApplication()->input->getString('phone');
        $post_password = JFactory::getApplication()->input->getString('password');
        $post_email = JFactory::getApplication()->input->getString('email');
        $post_ruls = JFactory::getApplication()->input->getString('ruls');
        $post_organization = 0;
        //exit($post_phone);
        //$post_repeat_password = JFactory::getApplication()->input->getString('repeat_password');
        //$post_organization = JFactory::getApplication()->input->getString('organization');
        //$post_organization_sel = JFactory::getApplication()->input->getString('organization_sel');


        if (utf8_strlen($post_name) < 3) {
            $json['error']['name'] = sprintf(JText::_('COM_LASERCITY_ERROR_NAME'), 3);
        }

        $lang_tag = JFactory::getLanguage()->getTag();

        //узнаем ссылку на язык
        $language_uri = self::getLanguageUri();

        /*if ($post_register_for == 'organization') {
            if (empty($post_organization_sel) || (!empty($post_organization_sel) && utf8_strlen($post_organization_sel) < 3)) {
                $json['error']['organization_sel'] = sprintf(JText::_('COM_LASERCITY_ERROR_ORGANIZACION_REGISTER'), 3);
            }

            if (empty($post_organization)) {

                $query = "SELECT t.value FROM `#__lasercity_organizations` as o 
                          LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON t.object_id = o.id 
                          WHERE t.object_column='title' AND t.value='" . $post_organization_sel . "' ";

                $db->setQuery($query);
                $result = $db->loadObjectList();
                foreach ($result as $row) {
                    if (!empty($row->value)) {
                        $json['error']['organization_sel'] = JText::_('COM_LASERCITY_ERROR_ORGANIZACION_IS_SET');
                        break;
                    }
                }
            }

            if (utf8_strlen($post_phone) != 19) {
                $json['error']['phone'] = JText::_('COM_LASERCITY_ERROR_PHONE');
            }
        }*/

        if (!preg_match('(\+38\s\(\d{3}\)\s\d{3}\s\d{2}\s\d{2})',$post_phone)) {
            $json['error']['phone'] = JText::_('COM_LASERCITY_ERROR_PHONE');
        }

        if (!filter_var($post_email, FILTER_VALIDATE_EMAIL)) {
            $json['error']['email'] = JText::_('COM_LASERCITY_ERROR_EMAIL');
        }

        if(!empty(self::getUserByEmail($post_email)))
        {
            $json['error']['email'] = JText::_('COM_LASERCITY_ERROR_EMAIL_IS_SET');
        }

        if (utf8_strlen($post_password) < 6) {
            $json['error']['password'] = sprintf(JText::_('COM_LASERCITY_ERROR_PASSWORD'), 6);
        }
        if (empty($post_ruls)) {
            $json['error']['ruls'] = JText::_('COM_LASERCITY_ERROR_FORM_REGISTER_WHO_GIVE');
        }
        /*if (utf8_strlen($post_repeat_password) < 6) {
            $json['error']['repeat_password'] = sprintf(JText::_('COM_LASERCITY_ERROR_PASSWORD_REPEAT'), 6);
        }*/
        /*if ($post_password != $post_repeat_password) {
            $json['error']['password'] = JText::_('COM_LASERCITY_ERROR_PASSWORD_EQUALLY');
            $json['error']['repeat_password'] = JText::_('COM_LASERCITY_ERROR_PASSWORD_EQUALLY');
        }*/

        if (empty($json['error'])) {
            $json['success'] = null;

            $post_password = JApplicationHelper::getHash($post_password);

            if($post_register_for == 'organization'){

                JFactory::getSession()->set('post_name', $post_name);
                JFactory::getSession()->set('post_register_for', $post_register_for);
                JFactory::getSession()->set('post_phone', $post_phone);
                JFactory::getSession()->set('post_password', $post_password);
                JFactory::getSession()->set('post_email', $post_email);

                $json['success'] = 'success';
                $json['uri'] = "/".$language_uri."/register-step/";
                die(json_encode($json));
            }

            $username = explode("@", $post_email)[0];

            //если введена новая организация
            /*if (empty($post_organization) && !empty($post_organization_sel)) {

                //добавлям новую запись оранизации - не опубликованная
                $query = "INSERT INTO `#__lasercity_organizations` SET 
                  published='0' ";

                $db->setQuery($query);
                $db->query();
                $post_organization = $db->insertid();

                //добавляем название огранизации в таблицу языков
                $query = "SELECT lang_code FROM `#__languages`  
                      WHERE lang_id > 0 ";

                $db->setQuery($query);
                $result = $db->loadObjectList();

                foreach ($result as $row) {
                    if (!empty($row->lang_code)) {
                        $db->setQuery("INSERT INTO `#__lasercity_translations_" . $row->lang_code . "` SET 
                                  object_name='organization',
                                  object_column='title',
                                  value = '" . $post_organization_sel . "', 
                                  object_id = '" . (int)$post_organization . "'
                                  ");
                        $db->query();
                    }
                }

            }*/

            //добавляем пользователя и данные организации в базу - пользователь пока еще не подтвердил регистрацию по почте activation = '0'
            $query = "INSERT INTO `#__users` SET 
                  email='" . $post_email . "',
                  password='" . $post_password . "',
                  username = '$username', 
                  name = '" . $post_name . "', 
                  activation = '0', 
                  registerDate = NOW() ";

            $db->setQuery($query);
            $db->query();
            $last_user_id = $db->insertid();

            $query = "INSERT INTO `#__users_description` SET 
                  tip_user='" . $post_register_for . "',
                  phone = '" . $post_phone . "', 
                  organization_id = '" . (int)$post_organization . "', 
                  user_id = '" . (int)$last_user_id . "' ";

            $db->setQuery($query);
            $db->query();

            //отправляем письмо с подтверждением регистрации
            $reg_code = mt_rand(1000, 9000);
            $href_reg = "https://" . $_SERVER['SERVER_NAME'] . "/?option=com_lasercity&task=statusRegistration&language=" . $language_uri . "&regcode=" . $reg_code . "&posts=" . $last_user_id;

            $message = self::sendLetterForRegister(array('language_uri' => $language_uri,'href_reg' => $href_reg));
            
            if (mail($post_email, "Регистрация на " . $_SERVER['SERVER_NAME'], $message, "From: " . $_SERVER['SERVER_NAME'] . " <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n" . "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8")) {


                $message = "
                <!DOCTYPE html>
                <head>
                <meta charset='utf-8'>
                <title>Регистрация " . (($post_register_for == 'organization') ? 'Партнера' : 'Пользователя') . "</title>
                </head>
                    <body>
                        <div title='" . $_SERVER['SERVER_NAME'] . "' style='background:#F0F0F0;width:700px;height:auto;margin:auto;padding:20px 50px 40px 50px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;'>
                        <a href='https://" . $_SERVER['SERVER_NAME'] . "/" . $language_uri . "/'>" . strtoupper($_SERVER['SERVER_NAME']) . "<!--<img src='https://" . $_SERVER['SERVER_NAME'] . "/img/logo.svg' alt=''>--></a>
                            <div style='text-align:justify;margin-top:20px;padding:20px;background:white;font-size:15px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;-moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);-webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);'>
                               Тип: <b>" . (($post_register_for == 'organization') ? 'Партнер' : 'Пользователь') . "</b><br>
                               " . (!empty($post_organization_sel) ? 'Название организации: <b>' . $post_organization_sel . '</b><br>' : '') . "
                               ФИО: <b>" . $post_name . "</b><br>
                               " . (!empty($post_phone) ? 'Телефон: <b>' . $post_phone . '</b><br>' : '') . "
                               " . (!empty($post_email) ? 'E-mail: <b>' . $post_email . '</b><br>' : '') . "
                            </div>
                            <div style='font-size:12px;margin-top:20px;width:100%;text-align:center;color:#989898;'>Пожалуйста, не отвечайте на это сообщение оно было отправлено автоматически.</div>
                        </div>
                    </body>
                </html>";

                $db->setQuery("SELECT email FROM `#__lasercity_settings`  
                      WHERE published = '1' AND who='register_info' ");
                $result = $db->loadObjectList();

                foreach ($result as $row) {
                    if (!empty($row->email)) {
                        mail($row->email, "Регистрация " . (($post_register_for == 'organization') ? 'ОРГАНИЗАЦИИ' : 'Пользователя'), $message, "From: " . $_SERVER['SERVER_NAME'] . " <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n" . "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8");
                    }
                }

                if (!empty($post_organization_sel)) {
                    $json['success'] = 'На ваш e-mail ' . explode("@", $post_email)[0] . '@<a href="https://' . explode("@", $post_email)[1] . '">' . explode("@", $post_email)[1] . '</a> было отправлено письмо с подтверждение регистрации, пожалуйста подтвердите регистрацию! Наш менеджер свяжется с Вами в ближайшее время.';
                } else {
                    $json['success'] = 'На ваш e-mail ' . explode("@", $post_email)[0] . '@<a href="https://' . explode("@", $post_email)[1] . '">' . explode("@", $post_email)[1] . '</a> было отправлено письмо с подтверждение регистрации, пожалуйста подтвердите регистрацию!';
                }
            }

        }

        die(json_encode($json));
    }

    function statusRegistration()
    {
        $post_language = JFactory::getApplication()->input->getString('language');
        $post_regcode = JFactory::getApplication()->input->getString('regcode');
        $post_posts = JFactory::getApplication()->input->getString('posts');

        if (!empty($post_language) && !empty($post_regcode) && !empty($post_posts)) {
            $db = JFactory::getDbo();

            $db->setQuery("UPDATE `#__users` SET activation = '1' WHERE id='" . $post_posts . "'");
            $db->query();

            header("Location: /register-success/", TRUE, 301);
            exit();
        } else {
            header("Location: /register-error/", TRUE, 301);
            exit();
        }
    }

    function initGoogle($secret = null)
    {
        $db = JFactory::getDbo();
        //узнаем ссылку на язык
        $language_uri = self::getLanguageUri("/");

        $client_id = '13471528563-lccfijl8v97ofbelgu8k2189nbdievnd.apps.googleusercontent.com'; // Client ID
        $client_secret = '9p8UjCurL8hsg6UKomV0hoJ5'; // Client secret
        $redirect_uri = 'https://' . $_SERVER["SERVER_NAME"] . '/' . $language_uri . "action-auth-google/"; // Redirect URIs

        if (!empty($secret)) {
            $json['secret'] = $client_secret;
        }

        $json['params'] = array(
            'redirect_uri' => $redirect_uri,
            'response_type' => 'code',
            'client_id' => $client_id,
            'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
        );

        $json['link_button'] = "https://accounts.google.com/o/oauth2/auth?" . urldecode(http_build_query($json['params']));

        if (!empty($secret)) {
            $json['secret'] = $client_secret;
            return json_encode($json);
        } else {
            die(json_encode($json));
        }
    }

    function initFacebook($secret = null)
    {
        $db = JFactory::getDbo();
        //узнаем ссылку на язык
        $language_uri = self::getLanguageUri("/");


        $client_id = '478933249332333'; // Client ID
        $client_secret = 'eddb179f0f8cf5d1bfda6e6f42d4ec8d'; // Client secret
        $redirect_uri = 'https://' . $_SERVER["SERVER_NAME"] . '/' . $language_uri . "action-auth-facebook/"; // Redirect URIs

        if (!empty($secret)) {
            $json['secret'] = $client_secret;
        }

        $json['params'] = array(
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'code',
            'client_id'     => $client_id,
            'scope'         => 'public_profile,email'
        );

        $json['link_button'] = "https://www.facebook.com/dialog/oauth?" . urldecode(http_build_query($json['params']));

        if (!empty($secret)) {
            $json['secret'] = $client_secret;
            return json_encode($json);
        } else {
            die(json_encode($json));
        }
    }

    function generatePassword($length = 6)
    {
        $chars = 'abdefhikn*rstyzABDEFGHKNQR_STYZ23456789';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }

    //$id or email
    function getUserById($id, $otherResource = null)
    {
        if ($id) {
            $db = JFactory::getDbo();

            $sql = "SELECT u.id as my_user_id, u.name, d.tip_user, d.organization_id, d.avatar, d.rank, d.phone FROM #__users u 
                        LEFT JOIN #__users_description d ON u.id=d.user_id ";
            if (!empty($otherResource) && $otherResource == 'facebook') {
                $sql .= " WHERE u.email = '" . $id . "'";
            } else if (!empty($otherResource) && $otherResource == 'google') {
                $sql .= "WHERE u.email = '" . $id . "'";
            } else {
                $sql .= "WHERE u.id = '" . $id . "'";
            }

            //exit($sql);

            $db->setQuery($sql);
            $result = $db->loadObjectList();

            $array['my_user_id'] = '';
            $array['my_user_name'] = '';
            $array['my_user_avatar'] = '';
            $array['my_user_rank'] = '';
            $array['my_user_phone'] = '';
            $array['tip_user'] = '';
            $array['organization_id'] = '';

            if ($result) {
                foreach ($result as $row) {
                    $array['my_user_id'] = $row->my_user_id;
                    $array['my_user_name'] = $row->name;
                    $array['my_user_avatar'] = $row->avatar;
                    $array['my_user_rank'] = $row->rank;
                    $array['my_user_phone'] = $row->phone;
                    $array['tip_user'] = $row->tip_user;
                    $array['organization_id'] = $row->organization_id;
                }
            }

            return $array;
        }
        return false;
    }

    /**
     * Авторизация/Регистрация спомощью Google
     */
    function actionAuthGoogle()
    {
        $json = null;
        $post_code = JFactory::getApplication()->input->getString('code');

        // опеределяем язык страницы
        $db = JFactory::getDbo();
        $lang_tag = JFactory::getLanguage()->getTag();
        //узнаем ссылку на язык
        $language_uri = self::getLanguageUri();

        $client_id = null;
        $client_secret = null;
        $redirect_uri = null;
        $int_array = json_decode(self::initGoogle('yes'), true);
//exit(var_dump($int_array));

        if (!empty($post_code)) {

            //exit($pos_code);
            $result = false;

            $params = array(
                'client_id' => $int_array['params']['client_id'],
                'client_secret' => $int_array['secret'],
                'redirect_uri' => $int_array['params']['redirect_uri'],
                'grant_type' => 'authorization_code',
                'code' => $post_code
            );

            $url = 'https://accounts.google.com/o/oauth2/token';

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($curl);
            curl_close($curl);
            $tokenInfo = json_decode($result, true);

            if (isset($tokenInfo['access_token'])) {
                $params['access_token'] = $tokenInfo['access_token'];

                $info = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo' . '?' . urldecode(http_build_query($params))), true);
                if (isset($info['id'])) {
                    $result = true;
                }
            }

            if ($result) {
//exit(var_dump($info));
                /*echo "Социальный ID пользователя: " . $userInfo['id'] . '<br />';
                echo "Имя пользователя: " . $userInfo['name'] . '<br />';
                echo "Email: " . $userInfo['email'] . '<br />';
                echo "Ссылка на профиль пользователя: " . $userInfo['link'] . '<br />';
                echo "Пол пользователя: " . $userInfo['gender'] . '<br />';
                echo '<img src="' . $userInfo['picture'] . '" />'; echo "<br />";*/

                if (!empty($info['id']) && !empty($info['email'])) {

                    //Авторизация/Регистрация пользователя через Google
                    $password_google = self::generatePassword(6);
                    $email = $info['email'];
                    $name = $info['name'];
                    $google_id = $info['id'];
//exit($google_id);
                    $password = JApplicationHelper::getHash($password_google);

                    $user = self::getUserById($email, 'google');
                    //exit(var_dump($user));
                    if (!empty($user['my_user_id'])) {

                        self::setAuth($user);
                        //сохраняем запись на услугу если есть данные
                        self::saveServiceAvtorisetSocseti($email,$user['my_user_id']);

                        header("Location: /", TRUE, 301);
                        exit();
                    } else {

                        $query = "INSERT INTO `#__users` SET 
                                  email='" . $email . "',
                                  password='" . $password . "',
                                  username = '" . explode('@', $email)[0] . "', 
                                  name = '" . $name . "', 
                                  activation = '0', 
                                  google_id = '$google_id', 
                                  registerDate = NOW() ";

                        $db->setQuery($query);
                        $db->query();
                        $last_user_id = $db->insertid();

                        $reg_code = mt_rand(1000, 9000);
                        $href_reg = "https://" . $_SERVER['SERVER_NAME'] . "/?option=com_lasercity&task=statusRegistration&language=" . $language_uri . "&regcode=" . $reg_code . "&posts=" . $last_user_id;

                        $message = self::sendLetterForRegister(array('language_uri' => $language_uri,'href_reg' => $href_reg));

                        if (mail($email, "Регистрация на " . $_SERVER['SERVER_NAME'], $message, "From: " . $_SERVER['SERVER_NAME'] . " <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n" . "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8")) {
                            $json['success'] = 'На ваш e-mail ' . explode("@", $email)[0] . '@<a href="https://' . explode("@", $email)[1] . '">' . explode("@", $email)[1] . '</a> было отправлено письмо с подтверждение регистрации, пожалуйста подтвердите регистрацию!';
                        }

                        self::setAuth(['my_user_id' => $last_user_id, 'my_user_name' => '', 'my_user_avatar' => '', 'my_user_phone' => '', 'my_user_rank' => '', 'tip_user' => 'user', 'organization_id' => '']);

                    }

                    $json['success'] = 'Вы успешно вошли как ' . $info['name'];
                    header("Location: /", TRUE, 301);
                    exit();

                } else {
                    $json['error'] = 'Ошибка авторизации, пожалуйста, попробуйте войти на сайт, через альтернативную форму';
                    header("Location: /register-error/", TRUE, 301);
                    exit();
                }
            }

        }

        die(json_encode($json));
    }
    

    /**
     * Авторизация/Регистрация спомощью Facebook
     */
    public function actionAuthFacebook()
    {
        $post_code = JFactory::getApplication()->input->getString('code');
        $json = null;

        // опеределяем язык страницы
        $db = JFactory::getDbo();
        $lang_tag = JFactory::getLanguage()->getTag();
        //узнаем ссылку на язык
        $language_uri = self::getLanguageUri();

        $client_id = null;
        $client_secret = null;
        $redirect_uri = null;
        $int_array = json_decode(self::initFacebook('yes'), true);
//exit(var_dump($params));

        if (!empty($post_code)) {

            //exit($post_code);
            $result = false;

            $params = array(
                'client_id' => $int_array['params']['client_id'],
                'client_secret' => $int_array['secret'],
                'redirect_uri' => $int_array['params']['redirect_uri'],
                'code' => $post_code
            );

            $url = 'https://graph.facebook.com/oauth/access_token';

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($curl);
            curl_close($curl);
            $tokenInfo = json_decode($result, true);
//exit(var_dump($tokenInfo));
            if (isset($tokenInfo['access_token'])) {
                $params['access_token'] = $tokenInfo['access_token'];

                $info = json_decode(file_get_contents('https://graph.facebook.com/v3.3/me?fields=id,name,email&' . urldecode(http_build_query($params))), true);
                if (isset($info['id'])) {
                    $result = true;
                }
            }

            if ($result) {
                //exit(var_dump($info));
                /*echo "Социальный ID пользователя: " . $userInfo['id'] . '<br />';
                echo "Имя пользователя: " . $userInfo['name'] . '<br />';
                echo "Email: " . $userInfo['email'] . '<br />';
                echo "Ссылка на профиль пользователя: " . $userInfo['link'] . '<br />';
                echo "Пол пользователя: " . $userInfo['gender'] . '<br />';
                echo '<img src="' . $userInfo['picture'] . '" />'; echo "<br />";*/

                if (!empty($info['id']) && !empty($info['email'])) {

                    //Авторизация/Регистрация пользователя через Facebook
                    $password_facebook = self::generatePassword(6);
                    $email = $info['email'];
                    $name = $info['name'];
                    $facebook_id = $info['id'];

                    $password = JApplicationHelper::getHash($password_facebook);

                    $user = self::getUserById($email, 'facebook');
                    //exit(var_dump($user));
                    if (!empty($user['my_user_id'])) {

                        self::setAuth($user);
                        //сохраняем запись на услугу если есть данные
                        self::saveServiceAvtorisetSocseti($email,$user['my_user_id']);

                        header("Location: /", TRUE, 301);
                        exit();

                    } else {
                        $query = "INSERT INTO `#__users` SET 
                                  email='" . $email . "',
                                  password='" . $password . "',
                                  username = '" . explode('@', $email)[0] . "', 
                                  name = '" . $name . "', 
                                  activation = '0', 
                                  facebook_id = '$facebook_id', 
                                  registerDate = NOW() ";

                        $db->setQuery($query);
                        $db->query();
                        $last_user_id = $db->insertid();

                        $reg_code = mt_rand(1000, 9000);
                        $href_reg = "https://" . $_SERVER['SERVER_NAME'] . "/?option=com_lasercity&task=statusRegistration&language=" . $language_uri . "&regcode=" . $reg_code . "&posts=" . $last_user_id;

                        $message = self::sendLetterForRegister(array('language_uri' => $language_uri,'href_reg' => $href_reg));

                        if (mail($email, "Регистрация на " . $_SERVER['SERVER_NAME'], $message, "From: " . $_SERVER['SERVER_NAME'] . " <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n" . "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8")) {
                            $json['success'] = 'На ваш e-mail ' . explode("@", $email)[0] . '@<a href="https://' . explode("@", $email)[1] . '">' . explode("@", $email)[1] . '</a> было отправлено письмо с подтверждение регистрации, пожалуйста подтвердите регистрацию!';
                        }

                        self::setAuth(['my_user_id' => $last_user_id, 'my_user_name' => '', 'my_user_avatar' => '', 'my_user_phone' => '', 'my_user_rank' => '', 'tip_user' => 'user', 'organization_id' => '']);

                        //$json['success'] = 'Вы успешно вошли как ' . $info['name'];
                        header("Location: /", TRUE, 301);
                        exit();
                    }

                } else {
                    $json['error'] = 'Ошибка авторизации, пожалуйста, попробуйте войти на сайт, через альтернативную форму';
                    header("Location: /register-error/", TRUE, 301);
                    exit();
                }
            }

        }

        die(json_encode($json));
    }

    /*
     * ресайз изображения
     * */
    function resize($file_input, $file_output, $w_o, $h_o, $percent = false)
    {
        list($w_i, $h_i, $type) = getimagesize($file_input);
        if (!$w_i || !$h_i) {
            //echo 'Невозможно получить длину и ширину изображения';
            return;
        }
        $types = array('jpg', 'gif', 'jpeg', 'png');
        $ext = $types[$type];
        if ($ext) {
            $func = 'imagecreatefrom' . $ext;
            $img = $func($file_input);
        } else {
            //echo 'Некорректный формат файла';
            return;
        }
        if ($percent) {
            $w_o *= $w_i / 100;
            $h_o *= $h_i / 100;
        }
        if (!$h_o) $h_o = $w_o / ($w_i / $h_i);
        if (!$w_o) $w_o = $h_o / ($h_i / $w_i);
        $img_o = imagecreatetruecolor($w_o, $h_o);
        imagecopyresampled($img_o, $img, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
        if ($type == 2) {
            return imagejpeg($img_o, $file_output, 100);
        } else {
            $func = 'image' . $ext;
            return $func($img_o, $file_output);
        }
    }

    /*
     * транслитерация символов
     * */
    function sms_translit($string)
    {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'і' => 'i', 'ї' => 'i', 'є' => 'e',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'І' => 'I', 'Ї' => 'I', 'Є' => 'E',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );

        return preg_replace("/-+/", "-", trim(preg_replace('~[^-a-z0-9_]+~u', '-', strtolower(strtr($string, $converter))), "-"));
    }

    /*
     * загрузка аватара
     * */
    function loadAvatar()
    {

        // опеределяем язык страницы
        $db = JFactory::getDbo();
        $lang_tag = JFactory::getLanguage()->getTag();
        //узнаем ссылку на язык
        $language_uri = self::getLanguageUri();

        $json['error'] = null;
        $json['src'] = null;
        $fileElementName = 'uploadfile';

        if (!empty($_FILES[$fileElementName]['error'])) {
            switch ($_FILES[$fileElementName]['error']) {

                case '1':
                    //$json['error'] = 'размер загруженного файла превышает размер установленный параметром upload_max_filesize  в php.ini ';
                    break;
                case '2':
                    //$json['error'] = 'размер загруженного файла превышает размер установленный параметром MAX_FILE_SIZE в HTML форме. ';
                    break;
                case '3':
                    $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_PARTS');//загружена только часть файла
                    break;
                case '4':
                    $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_PATH');//файл не был загружен (Вы указали неверный путь к файлу).
                    break;
                case '6':
                    //$infoFoto['error'] = 'неверная временная дирректория';
                    break;
                case '7':
                    $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_WRITE');//ошибка записи файла на диск
                    break;
                case '8':
                    $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_NO');//загрузка файла прервана
                    break;
                case '999':
                default:
                    //$infoFoto['txt'] = = 'No error code avaiable';
            }
        } else {

            // узнаем расширение файла
            $expansion = new SplFileInfo($_FILES[$fileElementName]['name']);

            $nazva_foto = str_shuffle(str_replace(':', '', date("H:i:s")) . trim(str_replace('-', '', date("Y-m-d"))) . str_replace($expansion->getExtension(), "", trim(@substr(self::sms_translit($_FILES[$fileElementName]['name']), 0, 10)))) . "." . $expansion->getExtension();
            $file_exists = $_SERVER['DOCUMENT_ROOT'] . "/images/reviews/tmp/" . $nazva_foto;

            $output = @getimagesize($_FILES[$fileElementName]['tmp_name']);
            $w_i = $output[0];
            $h_i = $output[1];

            if (file_exists($file_exists)) {
                $json['error'] = "Изображение - " . basename($_FILES[$fileElementName]['name']) . " уже существует";
            } else if ($w_i < 105 || $h_i < 105) {
                $json['error'] = sprintf(JText::_('COM_LASERCITY_ERROR_UPLOAD_MIN'), 105, 105);
            } else if (@filesize($_FILES[$fileElementName]['tmp_name']) > 5000000) {
                $json['error'] = sprintf(JText::_('COM_LASERCITY_ERROR_UPLOAD_MAX'), 5);
            } else {
                if (move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $file_exists)) {
                    $imageinfo = @getimagesize($file_exists);
                    switch ($imageinfo['mime']) {
                        //Создать изображение в соответствии с содержанием
                        case 'image/jpg':
                        case 'image/jpeg':
                        case 'image/pjpeg': //for IE
                            $src_mime = 'jpeg';
                            break;
                        case 'image/gif':
                            $src_mime = 'gif';
                            break;
                        case 'image/png':
                        case 'image/x-png': //for IE
                            $src_mime = 'png';
                            break;
                        default :
                            $src_mime = null;
                            break;
                    }

                    self::resize($file_exists, $file_exists, 180, '', $percent = false);


                    if (!empty($src_mime)) {
                        $json['error'] = "";
                        $json['src'] = "//" . $_SERVER['SERVER_NAME'] . "/images/reviews/tmp/" . $nazva_foto;
                        $json['avatar'] = $nazva_foto;
                    } else {
                        $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_FORMAT');
                    }
                } else {
                    $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_DONT_KNOW');
                }
            }
        }
        die(json_encode($json));

    }

    function loadFiles()
    {
        $count = JFactory::getApplication()->input->getString('count');

        $json = null;
        // количество фото
        $json['count'] = $count;

        if ($count < 6) {
            $fileElementName = 'uploadfiles';

            if (!empty($_FILES[$fileElementName]['error'])) {
                switch ($_FILES[$fileElementName]['error']) {

                    case '1':
                        //$error = 'размер загруженного файла превышает размер установленный параметром upload_max_filesize  в php.ini ';
                        break;
                    case '2':
                        //$error = 'размер загруженного файла превышает размер установленный параметром MAX_FILE_SIZE в HTML форме. ';
                        break;
                    case '3':
                        $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_PARTS');
                        break;
                    case '4':
                        $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_PATH');
                        break;
                    case '6':
                        //$json['error'] = 'неверная временная дирректория';
                        break;
                    case '7':
                        $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_WRITE');
                        break;
                    case '8':
                        $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_NO');
                        break;
                    case '999':
                    default:
                        //$json['txt'] = = 'No error code avaiable';
                }
            } else {

                // узнаем расширение файла
                $expansion = new SplFileInfo($_FILES[$fileElementName]['name']);

                $nazva_foto = str_shuffle(@str_replace(':', '', date("H:i:s")) . trim(str_replace('-', '', date("Y-m-d"))) . str_replace($expansion->getExtension(), "", trim(@substr(self::sms_translit($_FILES[$fileElementName]['name']), 0, 10)))) . "." . $expansion->getExtension();
                $file_exists = $_SERVER['DOCUMENT_ROOT'] . "/images/reviews/tmp/" . $nazva_foto;

                $output = @getimagesize($_FILES[$fileElementName]['tmp_name']);
                $w_i = $output[0];
                $h_i = $output[1];

                if ($w_i < 100 || $h_i < 100) {
                    $json['error'] = sprintf(JText::_('COM_LASERCITY_ERROR_UPLOAD_MIN'), 100, 100);
                } else if (@filesize($_FILES[$fileElementName]['tmp_name']) > 5000000) {
                    $json['error'] = sprintf(JText::_('COM_LASERCITY_ERROR_UPLOAD_MAX'), 5);
                } else if (move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $file_exists)) {
                    $imageinfo = @getimagesize($file_exists);
                    switch ($imageinfo['mime']) {
                        //Создать изображение в соответствии с содержанием
                        case 'image/jpg':
                        case 'image/jpeg':
                        case 'image/pjpeg': //for IE
                            $src_mime = 'jpeg';
                            break;
                        case 'image/gif':
                            $src_mime = 'gif';
                            break;
                        case 'image/png':
                        case 'image/x-png': //for IE
                            $src_mime = 'png';
                            break;
                        default :
                            $src_mime = null;
                            break;
                    }

                    if (!empty($src_mime)) {
                        $json['error'] = "";
                        $json['rel'] = $nazva_foto;
                    } else {
                        $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_FORMAT');
                    }
                } else {
                    $json['error'] = JText::_('COM_LASERCITY_ERROR_UPLOAD_DONT_KNOW');
                }

            }
            die(json_encode($json));
        }
    }

    function deleteFile()
    {

        $count = JFactory::getApplication()->input->getString('count');

        $json = null;
        // количество фото
        $json['count'] = $count;

        $patch = JFactory::getApplication()->input->getString('patch');
        @unlink($_SERVER['DOCUMENT_ROOT'] . "/images/reviews/tmp/" . $patch);
        die(json_encode($json));
    }

    function addReview()
    {
        $json = null;
        $db = JFactory::getDbo();

        $post_name = JFactory::getApplication()->input->getString('name');
        $post_phone = JFactory::getApplication()->input->getString('phone');
        $post_email = JFactory::getApplication()->input->getString('email');
        $post_file_avatar = JFactory::getApplication()->input->getString('file_avatar');
        $post_date_visit = JFactory::getApplication()->input->getString('date_visit');
        $post_comment = JFactory::getApplication()->input->getString('send_comment');
//exit($post_phone);

        if(!empty(JFactory::getApplication()->input->getString('load_file'))){
            foreach (JFactory::getApplication()->input->getString('load_file') as $file){
                $post_load_file[] = "images/reviews/".$file;
            }
            $post_load_file = json_encode($post_load_file);
        }
        else{
            $post_load_file = json_encode(array(''));
        }

        //$post_load_file = !empty(JFactory::getApplication()->input->getString('load_file')) ? json_encode(JFactory::getApplication()->input->getString('load_file')) : json_encode(array(''));

        $post_place = JFactory::getApplication()->input->getString('place');
        $post_relationship = JFactory::getApplication()->input->getString('relationship');
        $post_purity = JFactory::getApplication()->input->getString('purity');
        $post_quality = JFactory::getApplication()->input->getString('quality');
        $post_price = JFactory::getApplication()->input->getString('price');

        $user_id = JFactory::getSession()->get('my_user_id');

        $organization_id = null;
        $affiliate = JFactory::getApplication()->input->getString('affiliate');
        $db->setQuery("SELECT organization FROM `#__lasercity_affiliates`  
                      WHERE id = '" . (int)$affiliate . "' ");
        $result = $db->loadObjectList();
        foreach ($result as $row) {
            if (!empty($row->organization)) {
                $organization_id = $row->organization;
                break;
            }
        }


        $lang_tag = JFactory::getLanguage()->getTag();

        //узнаем ссылку на язык
        $language_uri = self::getLanguageUri();

        $curlData = include($_SERVER['DOCUMENT_ROOT'] . '/templates/lasercity/recaptcha/init.php');

        if (utf8_strlen($post_name) < 3) {
            $json['error']['name'] = sprintf(JText::_('COM_LASERCITY_ERROR_NAME'), 3);
        }
        if ($post_phone!="+38 (0__) ___ __ __" && !preg_match('(\+38\s\(\d{3}\)\s\d{3}\s\d{2}\s\d{2})',$post_phone)) {
            $json['error']['phone'] = JText::_('COM_LASERCITY_ERROR_PHONE');
        }
        if (!empty($post_email) && !filter_var($post_email, FILTER_VALIDATE_EMAIL)) {
            $json['error']['email'] = JText::_('COM_LASERCITY_ERROR_EMAIL');
        }
        if (!$curlData['success']) {
            $json['error']['captcha'] = JText::_('COM_LASERCITY_ERROR_REVIEW_NO_BOT');
        }

        if (empty($json['error'])) {

            @rename(JPATH_BASE . "/images/reviews/tmp/" . $post_file_avatar, JPATH_BASE . "/images/reviews/" . $post_file_avatar);
            $imageConversion = new ImageConversion(JPATH_BASE . "/images/reviews/" . $post_file_avatar);
            $dimensions_avtor = array( // Розміри
                array(
                    'width' => 45,
                    'height' => 45,
                    'quality' => 55
                ),
                array(
                    'width' => 90,
                    'height' => 90,
                    'quality' => 55
                ),
                array(
                    'width' => 180,
                    'height' => 180,
                    'quality' => 55
                )
            );
            $imageConversion->convert($dimensions_avtor, ['jpg', 'webp'], true);

            $query = "INSERT INTO `#__lasercity_reviews` SET 
                                  user_id='" . (int)$user_id . "',
                                  organization_id='" . (int)$organization_id . "',
                                  affiliate_id = '" . (int)$affiliate . "',
                                  name = '" . $post_name . "', 
                                  email = '" . $post_email . "', 
                                  phone = '" . $post_phone . "', 
                                  koment = '" . $post_comment . "', 
                                  avatar = 'images/reviews/" . $post_file_avatar . "', 
                                  foto = '" . $post_load_file . "', 
                                  place = '" . (int)$post_place . "', 
                                  relationship = '" . (int)$post_relationship . "', 
                                  purity = '" . (int)$post_purity . "', 
                                  quality = '" . (int)$post_quality . "', 
                                  price = '" . (int)$post_price . "', 
                                  date_visit = '$post_date_visit', 
                                  city_id = '".(int)JFactory::$session->get('current_city_id')."', 
                                  date_added = NOW() ";

            $db->setQuery($query);
            $db->query();
            $last_id = $db->insertid();

            if (!empty(JFactory::getApplication()->input->getString('load_file'))) {
                $images = JFactory::getApplication()->input->getString('load_file');
                foreach ($images as $image) {
                    @rename(JPATH_BASE . "/images/reviews/tmp/" . $image, JPATH_BASE . "/images/reviews/" . $image);
                    $imageConversion = new ImageConversion(JPATH_BASE . "/images/reviews/" . $image);
                    $dimensions = array( // Розміри
                        array(
                            'width' => 82,
                            'height' => 63,
                            'quality' => 55
                        ),
                        array(
                            'width' => 105,
                            'height' => 80,
                            'quality' => 55
                        ),
                        array(
                            'width' => 164,
                            'height' => 126,
                            'quality' => 55
                        ),
                        array(
                            'width' => 210,
                            'height' => 160,
                            'quality' => 55
                        )
                    );
                    $imageConversion->convert($dimensions, ['jpg', 'webp'], true);
                }
            }

            $json['success'] = JText::_('COM_LASERCITY_SUCCESS_REVIEW');
        }


        die(json_encode($json));
    }

    function loadMoreResult()
    {
        //узнаем ссылку на язык
        $default_sef = "";
        $current_sef = LangHelper::getCurrentSef();
        $language_default = JComponentHelper::getParams('com_languages')->get('site');
        $db = JFactory::getDbo();
        $db->setQuery("SELECT sef FROM `#__languages` WHERE lang_code = '".$language_default."' ");
        $result = $db->loadObjectList();
        foreach ($result as $row) {
            if (!empty($row->sef)) {$default_sef = $row->sef; break;}
        }
        $current_sef = str_replace($default_sef,"",$current_sef);
        $current_sef = empty($current_sef) ? "/" : "/".$current_sef."/";

        $db = JFactory::getDbo();

        $tag = JFactory::$language->getTag();
        $query = "
                    SELECT * FROM (
                        SELECT *,
                            (
                                SELECT `value`
                                FROM `#__lasercity_translations_{$tag}`
                                WHERE 
                                    `object_id`=`main`.`id` AND
                                    `object_column`='title' AND
                                    `object_name`='stock'
                            ) as `title`,
                            (
                                SELECT `value`
                                FROM `#__lasercity_translations_{$tag}`
                                WHERE 
                                    `object_id`=`main`.`id` AND
                                    `object_column`='description' AND
                                    `object_name`='stock'
                            ) as `description`
                        FROM `#__lasercity_stock` as `main`
                        WHERE `published` 
                        AND `city_id`='" . (int)JFactory::$session->get('current_city_id') . "'
                        ORDER BY `ordering` ASC
                    ) as `t`
                            ";
        $search = addslashes(JFactory::$application->input->getString('search'));
        if ($search != null) {
            $query .= "WHERE LOWER(`title`) LIKE LOWER('%{$search}%')";
        }
        $db->setQuery($query);
        $stocks = $db->loadObjectList();
        foreach ($stocks as $stock) {
            $stock->image = deleteFormat($stock->image);
            $stock->description = mb_substr(strip_tags($stock->description), 0, 20) . '...';
            $db->setQuery("SELECT f.city FROM `#__lasercity_affiliates` as f 
                          WHERE f.published='1' 
                          AND f.organization!=0 
                          AND f.organization='" . $stock->organization . "' 
                          AND f.city='" . (int)JFactory::$session->get('current_city_id') . "'");
            $count_addres = $db->loadObjectList();
            $stock->count_addres = !empty($count_addres) ? count($count_addres) : 0;
            $stock->count_string = ContentLoader::replaceSuffix($stock->count_addres, 'adress');
        }



        $limit = 8;
        $count = count($stocks);
        $pages = ceil($count / $limit);

        $page = JFactory::getApplication()->input->getString('page');
        $page = is_numeric($page) ? $page : 1;
        $page = $page > $pages ? $pages : $page;

        $stocks = array_slice($stocks, ($page - 1) * $limit, $limit);

        $obj_city = CityHelper::getCurrent();
        $output = "";
        foreach ($stocks as $stock){

            $output .= '<li class="slider__item">
                           <div class="slider__item-wrapper">
                               <output class="stock-slider__item-discount">-'.$stock->discount.'%</output>
                               <picture>
                                  <source data-srcset="/'.$stock->image.'_262x198.webp 1x, /'.$stock->image.'_524x396.webp 2x" type="image/webp">
                                  <img class="stock-slider__item-img lazyload" data-src="/'.$stock->image.'_262x198.jpg" data-srcset="/'.$stock->image.'_524x396.jpg 2x" loading="lazy" title="'.$stock->title.'" alt="'.$stock->title.'">
                               </picture>
                               <div class="stock-slider__item-wrapper">
                                    <h4 class="stock-slider__item-title">'.$stock->title.'</h4>
                                    <p class="stock-slider__item-address">'.$stock->count_addres.' '.$stock->count_string.' в '.$obj_city->title.'</p>
                                    <p class="stock-slider__item-footer">
                                       <output class="stock-slider__item-purchased">('.ContentLoader::getWriteServiceTotal('stock',$stock->id).')</output>
                                       <a class="stock-slider__item-look button" href="'.$current_sef.'stocks/'.$stock->alias.'/">'.JText::_('COM_LASERCITY_ALL_A_SEE').'</a>
                                    </p>
                               </div>
                           </div>
                       </li>';
        }
//exit($output);
        $result['html'] = $output;
        $result['search_info']['page'] = $page;
        $result['search_info']['pages'] = $pages;
        $result['search_info']['count'] = $count;
        $result['search_info']['config']['limit'] = $limit;
        //$result['search_info'] = Search::getInfo();
        die(json_encode($result));
    }


    function loadMoreArticles()
    {
        //узнаем ссылку на язык
        $default_sef = "";
        $current_sef = LangHelper::getCurrentSef();
        $language_default = JComponentHelper::getParams('com_languages')->get('site');
        $db = JFactory::getDbo();
        $db->setQuery("SELECT sef FROM `#__languages` WHERE lang_code = '".$language_default."' ");
        $result = $db->loadObjectList();
        foreach ($result as $row) {
            if (!empty($row->sef)) {$default_sef = $row->sef; break;}
        }
        $current_sef = str_replace($default_sef,"",$current_sef);
        $current_sef = empty($current_sef) ? "/" : "/".$current_sef."/";

        $db = JFactory::getDbo();

        $tag = JFactory::$language->getTag();
        $query = "
                    SELECT * FROM (
                        SELECT *,
                            (
                                SELECT `value`
                                FROM `#__lasercity_translations_{$tag}`
                                WHERE 
                                    `object_id`=`main`.`id` AND
                                    `object_column`='title' AND
                                    `object_name`='article'
                            ) as `title`,
                            (
                                SELECT `value`
                                FROM `#__lasercity_translations_{$tag}`
                                WHERE 
                                    `object_id`=`main`.`id` AND
                                    `object_column`='description' AND
                                    `object_name`='article'
                            ) as `description`
                        FROM `#__lasercity_articles` as `main`
                        WHERE `published` 
                        ORDER BY `ordering` ASC
                    ) as `t`
                            ";
        $search = addslashes(JFactory::$application->input->getString('search'));
        if ($search != null) {
            $query .= "WHERE LOWER(`title`) LIKE LOWER('%{$search}%')";
        }
        $db->setQuery($query);
        $articles = $db->loadObjectList();
        foreach ($articles as $article) {
            $article->main_image = @deleteFormat($article->main_image);
        }


        $limit = 8;
        $count = count($articles);
        $pages = ceil($count / $limit);

        $page = JFactory::getApplication()->input->getString('page');
        $page = is_numeric($page) ? $page : 1;
        $page = $page > $pages ? $pages : $page;

        $articles = array_slice($articles, ($page - 1) * $limit, $limit);

        $obj_city = CityHelper::getCurrent();
        $output = "";
        foreach ($articles as $article){

            $output .= '<li class="articles__article-item">
            <article class="articles__article">
              <picture>
                <source data-srcset="/'.$article->main_image.'_262x198.webp 1x, /'.$article->main_image.'_524x396.webp 2x" media="(min-width: 1200px)" type="image/webp">
                <img class="articles-index__image lazyload" data-src="/'.$article->main_image.'_262x198.jpg" data-srcset="/'.$article->main_image.'_524x396.jpg 2x" loading="lazy" title="'.JText::_('COM_LASERCITY_ALL_IMG_TITLE').': '.$article->title.'" alt="'.JText::_('COM_LASERCITY_ALL_IMG_TITLE').': '.$article->title.'">
              </picture>
              <h3 class="articles__article-title article__text">'.$article->title.'</h3>
              <div class="articles__article-wrapper">
                <p class="articles__article-text">'.$article->description.'</p>
                <div class="articles__article-links-wrapper">
                  <a class="articles__article-button button" href="'.$current_sef.'articles/'.$article->alias.'">'.JText::_('COM_LASERCITY_ALL_SHOW_GO').'</a>
                  <ul class="articles__article-links">
                    <li class="articles__article-links-item articles__article-links-item--facebook">
                      <output>'.$article->likes.'</output>
                    </li>
                    <li class="articles__article-links-item articles__article-links-item--view">
                      <output>'.$article->see.'</output>
                    </li>
                  </ul>
                </div>
              </div>
            </article>
          </li>';
        }
//exit($output);
        $result['html'] = $output;
        $result['search_info']['page'] = $page;
        $result['search_info']['pages'] = $pages;
        $result['search_info']['count'] = $count;
        $result['search_info']['config']['limit'] = $limit;
        //$result['search_info'] = Search::getInfo();
        die(json_encode($result));
    }


    function setSubscription()
    {
        $json = null;
        $db = JFactory::getDbo();

        $post_mailing = JFactory::getApplication()->input->getString('mailing');
        $session_organization_id = JFactory::getSession()->get('organization_id');

        $lang_tag = JFactory::getLanguage()->getTag();

        //узнаем язык
        $db->setQuery("SELECT sef FROM `#__languages`  
                      WHERE lang_code = '" . $lang_tag . "' ");
        $result = $db->loadObjectList();

        foreach ($result as $row) {
            if (!empty($row->sef)) {
                $language = $row->sef;
                break;
            }
        }

        if (empty($post_mailing) || !filter_var($post_mailing, FILTER_VALIDATE_EMAIL)) {
            $json['error']['mailing'] = JText::_('COM_LASERCITY_ERROR_EMAIL');
        }

        if (empty($json['error'])) {

            $flag = '';

            $query = "SELECT email FROM `#__lasercity_subscription`  
                      WHERE email='" . $post_mailing . "' ";

            $db->setQuery($query);
            $result = $db->loadObjectList();
            foreach ($result as $row) {
                if (!empty($row->email)) {
                    $flag = 'yes';
                    break;
                }
            }

            if (empty($flag)) {
                $query = "INSERT INTO `#__lasercity_subscription` SET 
                                  organization_id='" . (int)$session_organization_id . "',
                                  email = '" . $post_mailing . "',
                                  published	 = '1', 
                                  date_added = NOW() ";

                $db->setQuery($query);
                $db->query();
                $last_id = $db->insertid();
            }
            $json['success'] = JText::_('COM_LASERCITY_SUCCESS_SUBSCRIPTION');
        }


        die(json_encode($json));
    }


    function setQuestions()
    {
        $json = null;
        $db = JFactory::getDbo();

        $post_name = JFactory::getApplication()->input->getString('name');
        $post_phone = JFactory::getApplication()->input->getString('phone');
        $post_email = JFactory::getApplication()->input->getString('email');
        $post_comment = JFactory::getApplication()->input->getString('koment');
        $post_affiliate = JFactory::getApplication()->input->getString('affiliate');

        $user_id = JFactory::getSession()->get('my_user_id');


        $lang_tag = JFactory::getLanguage()->getTag();

        //узнаем ссылку на язык
        $language_uri = self::getLanguageUri();

        if (utf8_strlen($post_name) < 3) {
            $json['error']['name'] = sprintf(JText::_('COM_LASERCITY_ERROR_NAME'), 3);
        }
        if (empty($post_phone) || !preg_match('(\+38\s\(\d{3}\)\s\d{3}\s\d{2}\s\d{2})',$post_phone)) {
            $json['error']['phone'] = JText::_('COM_LASERCITY_ERROR_PHONE');
        }
        if (empty($post_email) || !filter_var($post_email, FILTER_VALIDATE_EMAIL)) {
            $json['error']['email'] = JText::_('COM_LASERCITY_ERROR_EMAIL');
        }
        if (utf8_strlen($post_comment) < 10) {
            $json['error']['koment'] = sprintf(JText::_('COM_LASERCITY_ERROR_QUESTION_KOMENT'), 10);
        }

        if (empty($json['error'])) {

            $query = "INSERT INTO `#__lasercity_question` SET 
                                  user_id='" . (int)$user_id . "',
                                  affiliate_id = '" . (int)$post_affiliate . "',
                                  name = '" . $post_name . "', 
                                  email = '" . $post_email . "', 
                                  phone = '" . $post_phone . "', 
                                  koment = '" . $post_comment . "',  
                                  date_added = NOW() ";

            $db->setQuery($query);
            $db->query();
            $last_id = $db->insertid();

            $json['success'] = JText::_('COM_LASERCITY_SUCCESS_QUESTION');
        }
        die(json_encode($json));
    }

    function changePass()
    {
        $json = null;
        $post_email_forgot = JFactory::getApplication()->input->getString('email_forgot');

        if (empty($post_email_forgot) || !filter_var($post_email_forgot, FILTER_VALIDATE_EMAIL)) {
            $json['error']['email_forgot'] = JText::_('COM_LASERCITY_ERROR_EMAIL');
        }
        if (filter_var($post_email_forgot, FILTER_VALIDATE_EMAIL) && empty(self::getUserByEmail($post_email_forgot))) {
            $json['error']['email_forgot'] = JText::_('COM_LASERCITY_ERROR_EMAIL_NO_SET');
        }

        if (empty($json['error'])) {

            $lang_tag = JFactory::getLanguage()->getTag();

            //узнаем ссылку на язык
            $db = JFactory::getDbo();
            $language_uri = self::getLanguageUri();

            $code=JApplicationHelper::getHash(mt_rand(1000,9000));
            $fora = date("Y-m-d H:i:s", strtotime("+1 hour"));
            $db->setQuery("UPDATE `#__users` SET lastResetTime = '$fora' WHERE email='" . $post_email_forgot . "' AND activation='1'");
            $db->query();

            $href = "https://" . $_SERVER['SERVER_NAME'] . "/".$language_uri."/password-change/?data=".self::getUserByEmail($post_email_forgot).":".$code;

            $message = "
				<!DOCTYPE html>
					<head>
					<meta charset='utf-8'>
					<title>Изменение пароля на " . $_SERVER['SERVER_NAME'] . "</title>
					</head>
					<body>
						<div style='background:#F0F0F0;width:700px;height:auto;margin:auto;padding:20px 50px 40px 50px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;'>
							<a href='https://" . $_SERVER['SERVER_NAME'] . "/" . $language_uri . "/'>" . strtoupper($_SERVER['SERVER_NAME']) . "<!--<img src='https://" . $_SERVER['SERVER_NAME'] . "/img/logo.svg' alt=''>--></a>
							<div style='text-align:justify;margin-top:20px;padding:20px;background:white;font-size:15px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;-moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);-webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);'>
								<h3>Здравствуйте!</h3>
								Чтобы изменить Ваш пароль, нажмите на кнопку «Изменить пароль».
								<a href='" . $href . "' style='text-decoration:none;'><div style='text-align:center;margin:auto;padding:10px;font-size:16px;font-weight:bold;color:white;background:#06c;margin-top:20px;white-space:nowrap;width:155px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;'>Изменить пароль</div></a>
							</div>
							<div style='text-align:justify;margin-top:20px;padding:20px;background:white;font-size:15px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;-moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);-webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);'>
								<h3>Важно знать</h3>
								Ваш пароль обеспечивает мгновенный доступ в личный кабинет, возможность отслеживания заказа и многое другое.
							</div>							
							<div style='font-size:12px;margin-top:20px;width:100%;text-align:center;color:#989898;'>Пожалуйста, не отвечайте на это сообщение оно было отправлено автоматически.</div>
						</div>
					</body>
				</html>";

            if (mail($post_email_forgot, "Изменить пароль на LaserCity", $message, "From: " . $_SERVER['SERVER_NAME'] . " <info@" . $_SERVER['SERVER_NAME'] . ">\r\n" . "Reply-To: <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n" . "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8")) {

                JFactory::getSession()->set('email_forgot_change', 'yes');
                $json['success'] = JText::_('COM_LASERCITY_DONT_KNOW_ALERT_SUCCESS');

            }
        }

        die(json_encode($json));
    }

    function setRegisterMe()
    {
        $json = null;
        $post_remember = JFactory::getApplication()->input->getString('register_me');
        if (!empty($post_remember)) {
            JFactory::getSession()->set('register_me', 'register_me');
            $json['success'] = JFactory::getSession()->get('register_me');
        } else {
            JFactory::getSession()->clear('register_me');
            $json['success'] = JFactory::getSession()->get('register_me');
        }
        die(json_encode($json));
    }

    function deleteRecords()
    {
        $json = null;
        $post_record = JFactory::getApplication()->input->getString('record');
        if (!empty($post_record)) {
            $db = JFactory::getDbo();

            //$db->setQuery("DELETE FROM `#__lasercity_by_services` WHERE id='" . (int)$post_record . "'");
            $db->setQuery("UPDATE `#__lasercity_by_services` SET arhiv='1' WHERE id='" . (int)$post_record . "'");
            $db->query();

            $json['success'] = JText::_('COM_LASERCITY_RECORD_DEL_SUCCESS');
        }
        die(json_encode($json));
    }

    function saveNewPass(){
        $json = null;

        $user_id = JFactory::getApplication()->input->getString('user_id');
        $post_new_password = JFactory::getApplication()->input->getString('new_password');
        $post_new_password1 = JFactory::getApplication()->input->getString('new_password1');

        if (utf8_strlen($post_new_password) < 6) {
            $json['error']['new_password'] = sprintf(JText::_('COM_LASERCITY_ERROR_PASSWORD'), 6);
        }
        if (utf8_strlen($post_new_password1) < 6) {
            $json['error']['new_password1'] = sprintf(JText::_('COM_LASERCITY_ERROR_PASSWORD'), 6);
        }
        if ($post_new_password!=$post_new_password1) {
            $json['error']['new_password1'] = JText::_('COM_LASERCITY_ERROR_PASSWORD_EQUALLY');
        }


        if (empty($json['error'])) {
            $db = JFactory::getDbo();
            $db->setQuery("UPDATE `#__users` SET password='".JApplicationHelper::getHash($post_new_password)."' WHERE id='" . (int)$user_id . "' AND activation='1'");
            $db->query();

            $json['success'] = JText::_('COM_LASERCITY_ERROR_PASSWORD_REPLACE');
        }

        die(json_encode($json));
    }

    function getAparatById($id){
        $db = JFactory::getDbo();

        $query = "SELECT title 
                  FROM `#__lasercity_apparats`
                  WHERE id='$id'
                  AND published='1' ";
        $db->setQuery($query);
        $result = $db->loadObjectList();

        foreach ($result as $row) {
            $apparat = $row->title;
        }

        return $apparat;
    }

    function getServiceById($id){
        $db = JFactory::getDbo();

        $lang_tag = JFactory::getLanguage()->getTag();

        $query = "SELECT t.value FROM `#__lasercity_services` as s 
              LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON t.object_id = s.id 
              WHERE s.id='$id'
              AND t.object_column='title' 
              AND t.object_name='service' 
              AND s.published='1' ";

        $db->setQuery($query);
        $result = $db->loadObjectList();

        foreach ($result as $row) {
            $service = $row->value;
        }

        return $service;
    }

    function saveServiceAvtorisetSocseti($email,$user_id){
        $json = null;

        $mas_service = json_decode(JFactory::getSession()->get('post_mas_service'),true);
        $mas_service_dopol = json_decode(JFactory::getSession()->get('post_mas_service_dopol'),true);

        if(!empty($mas_service) && !empty($mas_service_dopol)) {

            $post_price = array();
            $post_service_id = array();
            $post_prices_id = array();
            $post_apparat_id = array();
            foreach ($mas_service as $service) {
                $post_service_id[] = $service['service_id'];
                $post_prices_id[] = $service['prices_id'];
                $post_apparat_id[] = $service['apparat_id'];
                $post_price[] = $service['price'];
            }

            $db = JFactory::getDbo();
            $query = "INSERT INTO `#__lasercity_by_services` SET 
                                  user_id='" . (int)$user_id . "',
                                  affiliate_id = '" . (int)$mas_service_dopol['post_affiliate_id'] . "',
                                  service_id = '" . json_encode($post_service_id) . "',
                                  prices_id = '" . json_encode($post_prices_id) . "',
                                  stock_id = '" . (int)$mas_service_dopol['post_stock_id'] . "',
                                  apparat_id = '" . json_encode($post_apparat_id) . "',
                                  name = '" . $mas_service_dopol['post_name'] . "', 
                                  email = '" . $email . "', 
                                  phone = '" . $mas_service_dopol['post_phone'] . "', 
                                  koment = '" . $mas_service_dopol['post_koment'] . "',  
                                  price = '" . array_sum($post_price) . "',  
                                  date_visit = '" . $mas_service_dopol['post_date_visit'] . "',  
                                  date_end = '" . $mas_service_dopol['post_date_end'] . "',  
                                  date_added = NOW() ";

            $db->setQuery($query);
            $db->query();

            JFactory::getSession()->clear('post_mas_service');
            JFactory::getSession()->clear('post_mas_service_dopol');
            JFactory::getSession()->set('autoriz_save_servises', 'yes');
            $json['success'] = 'success';
        }

        echo json_encode($json);

    }

    function completedProcedure(){
        $json = null;
        $db = JFactory::getDbo();
        $post_record_id = JFactory::getApplication()->input->getString('record_id');
        $post_affiliate_id = JFactory::getApplication()->input->getString('affiliate');

        $user_id = JFactory::getSession()->get('my_user_id');
        $organization_id = JFactory::getSession()->get('organization_id');

        if(!empty($post_record_id) && !empty($user_id) && !empty($organization_id)) {
            $query = "UPDATE `#__lasercity_by_services` 
                      SET confirmed = '1' 
                      WHERE id='".(int)$post_record_id ."' AND affiliate_id='".$post_affiliate_id."' ";
            $db->setQuery($query);
            //echo $query;
            $db->query();
            $json['success'] = 'success';
        }
        die(json_encode($json));
    }

    function abolishedProcedure(){
        $json = null;
        $db = JFactory::getDbo();
        $post_record_id = JFactory::getApplication()->input->getString('record_id');
        $post_affiliate_id = JFactory::getApplication()->input->getString('affiliate');

        $user_id = JFactory::getSession()->get('my_user_id');
        $organization_id = JFactory::getSession()->get('organization_id');

        if(!empty($post_record_id) && !empty($user_id) && !empty($organization_id)) {
            $query = "UPDATE `#__lasercity_by_services` 
                      SET status = '3' 
                      WHERE id='".(int)$post_record_id ."' AND affiliate_id='".$post_affiliate_id."' ";
            $db->setQuery($query);
            $db->query();
            $json['success'] = 'success';
        }
        die(json_encode($json));
    }


    function loadNewHeld(){
        $json = null;
        $db = JFactory::getDbo();
        $user_id = JFactory::getSession()->get('my_user_id');
        $organization_id = JFactory::getSession()->get('organization_id');
        $post_records_id = JFactory::getApplication()->input->getString('records_id');

        //echo var_dump($post_records_id);

        if(!empty($user_id)) {

            $query = "UPDATE `#__lasercity_by_services` 
                      SET status = '2' 
                      WHERE id IN('" . implode("','", $post_records_id) ."') 
                      AND UNIX_TIMESTAMP('".date('Y-m-d H:i:s')."') > UNIX_TIMESTAMP(date_end) ";
            $db->setQuery($query);
            $db->query();

            if(!empty($organization_id)) {
                $json['new'] = ContentLoader::getApplicationByOrganization(0,$organization_id);
                $json['agreed'] = ContentLoader::getApplicationByOrganization(1,$organization_id);
            }
            else{
                $json['new'] = ContentLoader::getApplications(0,$user_id);
                $json['agreed'] = ContentLoader::getApplications(1,$user_id);
            }

            $json['success'] = 'success';
        }

        die(json_encode($json));
    }


    function servisesRecordBySalon(){
        $json = null;
        $post_date_hour1 = !empty(JFactory::getApplication()->input->getString('hour1')) ? JFactory::getApplication()->input->getString('hour1') : "00";
        $post_date_hour2 = !empty(JFactory::getApplication()->input->getString('hour2')) ? JFactory::getApplication()->input->getString('hour2') : "00";
        $post_date_minut1 = !empty(JFactory::getApplication()->input->getString('minut1')) ? JFactory::getApplication()->input->getString('minut1') : "00";
        $post_date_minut2 = !empty(JFactory::getApplication()->input->getString('minut2')) ? JFactory::getApplication()->input->getString('minut2') : "00";
        $post_date_agreed = date('Y-m-d',strtotime(JFactory::getApplication()->input->getString('date_visit')))." ".$post_date_hour1.":".$post_date_minut1;
        $post_date_end = date('Y-m-d',strtotime(JFactory::getApplication()->input->getString('date_visit')))." ".$post_date_hour2.":".$post_date_minut2;
        $post_sex_id = JFactory::getApplication()->input->getString('sex_id');
        $post_service_id = JFactory::getApplication()->input->getString('service_id');
        $post_prices_id = JFactory::getApplication()->input->getString('prices_id');
        $post_apparat_id = JFactory::getApplication()->input->getString('apparat_id');
        $post_price = JFactory::getApplication()->input->getString('price');
        $post_record_id = JFactory::getApplication()->input->getString('record_id');

        //узнаем ссылку на язык
        $db = JFactory::getDbo();
        $language_uri = self::getLanguageUri("/");

        if (empty($post_service_id) || $post_service_id=='null') {
            $json['error']['service_txt'] = JText::_('COM_LASERCITY_ERROR_SERVICE');
        }
        if (strtotime($post_date_agreed) < strtotime(date('Y-m-d H:i'))) {
            $json['error']['date_visit'] = JText::_('COM_LASERCITY_ERROR_DATE_VISIT');
        }
        if ($post_date_hour1=='00' || $post_date_hour2=='00' || strtotime($post_date_agreed) > strtotime($post_date_end)) {
            $json['error']['hour1'] = JText::_('COM_LASERCITY_ERROR_TIME_VISIT');
        }

        if (empty($json['error'])) {

            $json['success'] = 'success';

            $query = "UPDATE `#__lasercity_by_services` SET 
                                  service_id = '" . json_encode($post_service_id) . "',
                                  prices_id = '" . json_encode($post_prices_id) . "',
                                  apparat_id = '" . json_encode($post_apparat_id) . "', 
                                  price = '" . array_sum($post_price) . "',  
                                  date_agreed = '" . $post_date_agreed . "',  
                                  date_end = '" . $post_date_end . "', 
                                  status = '1' 
                                  WHERE id='".(int)$post_record_id."'";

            $db->setQuery($query);
            $db->query();
            $last_id = $db->insertid();

            $json['uri'] = '/' . $language_uri . 'records-salon/?status=1';
        }
        die(json_encode($json));
    }

    function servisesRecord()
    {
        $json = null;
        $post_date_hour1 = !empty(JFactory::getApplication()->input->getString('hour1')) ? JFactory::getApplication()->input->getString('hour1') : "00";
        $post_date_hour2 = !empty(JFactory::getApplication()->input->getString('hour2')) ? JFactory::getApplication()->input->getString('hour2') : "00";
        $post_date_minut1 = !empty(JFactory::getApplication()->input->getString('minut1')) ? JFactory::getApplication()->input->getString('minut1') : "00";
        $post_date_minut2 = !empty(JFactory::getApplication()->input->getString('minut2')) ? JFactory::getApplication()->input->getString('minut2') : "00";
        $post_date_visit = date('Y-m-d',strtotime(JFactory::getApplication()->input->getString('date_visit')))." ".$post_date_hour1.":".$post_date_minut1;
        $post_date_end = date('Y-m-d',strtotime(JFactory::getApplication()->input->getString('date_visit')))." ".$post_date_hour2.":".$post_date_minut2;
        $post_sex_id = JFactory::getApplication()->input->getString('sex_id');
        $post_service_id = JFactory::getApplication()->input->getString('service_id');
        $post_prices_id = JFactory::getApplication()->input->getString('prices_id');
        $post_duration = JFactory::getApplication()->input->getString('duration');
        $post_apparat_id = JFactory::getApplication()->input->getString('apparat_id');
        $post_price = JFactory::getApplication()->input->getString('price');
        $post_name = JFactory::getApplication()->input->getString('name');
        $post_phone = JFactory::getApplication()->input->getString('phone');
        $post_koment = JFactory::getApplication()->input->getString('koment');
        $post_affiliate_id = JFactory::getApplication()->input->getString('affiliate');
        $post_stock_id = JFactory::getApplication()->input->getString('stock');

        $user_id = JFactory::getSession()->get('my_user_id');

        //узнаем ссылку на язык
        $db = JFactory::getDbo();
        $language_uri = self::getLanguageUri("/");

        /*echo $post_date_hour1."<br>";
        echo $post_date_hour2."<br>";
        echo $post_date_minut1."<br>";
        echo $post_date_minut2."<br>";
        echo $post_date_visit."<br>";
        echo $post_date_end."<br>";
        echo $post_service_id."<br>";
        echo $post_apparat_id."<br>";
        exit();*/

        if (empty($post_service_id) || $post_service_id=='null') {
            $json['error']['service_txt'] = JText::_('COM_LASERCITY_ERROR_SERVICE');
        }
        if (strtotime($post_date_visit) < strtotime(date('Y-m-d H:i'))) {
            $json['error']['date_visit'] = JText::_('COM_LASERCITY_ERROR_DATE_VISIT');
        }
        if ($post_date_hour1=='00' || $post_date_hour2=='00' || strtotime($post_date_visit) > strtotime($post_date_end)) {
            $json['error']['hour1'] = JText::_('COM_LASERCITY_ERROR_TIME_VISIT');
        }

        if (empty($json['error'])) {

            $post_name = empty($post_name) ? JFactory::getSession()->get('my_user_name') : $post_name;
            $post_phone = empty($post_phone) ? JFactory::getSession()->get('my_user_phone') : $post_phone;
            $post_email = "";

            $json['success'] = 'success';

            if(!empty($user_id)){

                $query = "INSERT INTO `#__lasercity_by_services` SET 
                                  user_id='" . (int)$user_id . "',
                                  affiliate_id = '" . (int)$post_affiliate_id . "',
                                  service_id = '" . json_encode($post_service_id) . "',
                                  prices_id = '" . json_encode($post_prices_id) . "',
                                  stock_id = '" . (int)$post_stock_id . "',
                                  apparat_id = '" . json_encode($post_apparat_id) . "',
                                  name = '" . $post_name . "', 
                                  email = '" . $post_email . "', 
                                  phone = '" . $post_phone . "', 
                                  koment = '" . $post_koment . "',  
                                  price = '" . array_sum($post_price) . "',  
                                  date_visit = '" . $post_date_visit . "',  
                                  date_end = '" . $post_date_end . "',  
                                  date_added = NOW() ";

                $db->setQuery($query);
                $db->query();
                $last_id = $db->insertid();

                $json['success'] = JText::_('COM_LASERCITY_ERROR_RECORDING_BY_SERVICES');
                $json['uri'] = '/' . $language_uri . 'records-client/';
            }
            else {
                $mas_service = array();
                for($i=0; $i<count($post_sex_id); $i++){
                    $mas_service[] = array(
                        'sex' => $post_sex_id[$i],
                        'service_id' => $post_service_id[$i],
                        'prices_id' => $post_prices_id[$i],
                        'duration' => $post_duration[$i],
                        'service_name' => self::getServiceById($post_service_id[$i]),
                        'apparat_id' => $post_apparat_id[$i],
                        'apparat_name' => self::getAparatById($post_apparat_id[$i]),
                        'price' => $post_price[$i]
                    );
                }


                JFactory::getSession()->set('post_mas_service', json_encode($mas_service));
                $mas_service_dopol = array(
                    'post_affiliate_id' => $post_affiliate_id,
                    'post_stock_id' => $post_stock_id,
                    'post_date_end' => $post_date_end,
                    'post_date_visit' => $post_date_visit,
                    'post_name' => $post_name,
                    'post_phone' => $post_phone,
                    'post_koment' => $post_koment
                );
                JFactory::getSession()->set('post_mas_service_dopol', json_encode($mas_service_dopol));

                $json['uri'] = '/' . $language_uri . 'servises-record/';
            }
        }

        die(json_encode($json));
    }

    function setRecordServisUser(){
        $json = null;

        $post_date_hour1 = !empty(JFactory::getApplication()->input->getString('hour1')) ? JFactory::getApplication()->input->getString('hour1') : "00";
        $post_date_hour2 = !empty(JFactory::getApplication()->input->getString('hour2')) ? JFactory::getApplication()->input->getString('hour2') : "00";
        $post_date_minut1 = !empty(JFactory::getApplication()->input->getString('minut1')) ? JFactory::getApplication()->input->getString('minut1') : "00";
        $post_date_minut2 = !empty(JFactory::getApplication()->input->getString('minut2')) ? JFactory::getApplication()->input->getString('minut2') : "00";
        $post_date_visit = date('Y-m-d',strtotime(JFactory::getApplication()->input->getString('date_visit')))." ".$post_date_hour1.":".$post_date_minut1;
        $post_date_end = date('Y-m-d',strtotime(JFactory::getApplication()->input->getString('date_visit')))." ".$post_date_hour2.":".$post_date_minut2;
        $post_sex_id = JFactory::getApplication()->input->getString('sex_id');
        $post_service_id = JFactory::getApplication()->input->getString('service_id');
        $post_prices_id = JFactory::getApplication()->input->getString('prices_id');
        $post_apparat_id = JFactory::getApplication()->input->getString('apparat_id');
        $post_price = JFactory::getApplication()->input->getString('price');
        $post_name = JFactory::getApplication()->input->getString('name');
        $post_email = JFactory::getApplication()->input->getString('email');
        $post_password = JFactory::getApplication()->input->getString('password');
        $post_phone = JFactory::getApplication()->input->getString('phone');
        $post_koment = JFactory::getApplication()->input->getString('koment');
        $post_affiliate_id = JFactory::getApplication()->input->getString('affiliate');
        $post_stock_id = JFactory::getApplication()->input->getString('stock');
        $post_ruls = JFactory::getApplication()->input->getString('ruls');
        $post_register_for = JFactory::getApplication()->input->getString('register_for');

        $user_id = '';

        //узнаем ссылку на язык
        $db = JFactory::getDbo();
        $language_uri = self::getLanguageUri("/");

        if (utf8_strlen($post_name) < 3 && $post_register_for=='no_accaut') {
            $json['error']['name'] = sprintf(JText::_('COM_LASERCITY_ERROR_NAME'), 3);
        }
        if ((empty($post_phone) || $post_phone=='+38 (0__) ___ __ __' || !preg_match('(\+38\s\(\d{3}\)\s\d{3}\s\d{2}\s\d{2})',$post_phone)) && $post_register_for=='no_accaut') {
            $json['error']['phone'] = JText::_('COM_LASERCITY_ERROR_PHONE');
        }
        if (empty($post_email) || !filter_var($post_email, FILTER_VALIDATE_EMAIL)) {
            $json['error']['email'] = JText::_('COM_LASERCITY_ERROR_EMAIL');
        }
        if(!empty(self::getUserByEmail($post_email)) && $post_register_for=='no_accaut')
        {
            $json['error']['email'] = JText::_('COM_LASERCITY_ERROR_EMAIL_IS_SET');
        }
        if (utf8_strlen($post_password) < 6) {
            $json['error']['password'] = sprintf(JText::_('COM_LASERCITY_ERROR_PASSWORD'), 6);
        }

        if($post_register_for=='yes_accaut') {
            $query = "SELECT id  
                  FROM `#__users` 
                  WHERE email='$post_email' AND password='".JApplicationHelper::getHash($post_password)."' ";

            $db->setQuery($query);
            $result = $db->loadObjectList();

            foreach ($result as $row) {
                if (!empty($row->id)) {
                    $json['success_user'] = 'ok';
                    break;
                }
            }

            if (empty($json['success_user'])) {
                $json['error']['password'] = JText::_('COM_LASERCITY_ERROR_EMAIL_OR_PASSWORD');
            }
        }

        if (empty($post_service_id) || $post_service_id=='null') {
            $json['error']['service_txt'] = JText::_('COM_LASERCITY_ERROR_SERVICE');
        }
        if (strtotime($post_date_visit) < strtotime(date('Y-m-d H:i'))) {
            $json['error']['date_visit'] = JText::_('COM_LASERCITY_ERROR_DATE_VISIT');
        }
        if ($post_date_hour1=='00' || $post_date_hour2=='00' || strtotime($post_date_visit) > strtotime($post_date_end)) {
            $json['error']['hour1'] = JText::_('COM_LASERCITY_ERROR_TIME_VISIT');
        }
        if (empty($post_ruls)) {
            $json['error']['ruls'] = JText::_('COM_LASERCITY_ERROR_FORM_REGISTER_WHO_GIVE');
        }

        if (empty($json['error'])) {

            $json['success'] = 'success';

            if ($post_register_for=='yes_accaut') {
                $arrayU = self::getUserById(self::getUserByEmail($post_email));
                self::setAuth(['my_user_id' => $arrayU['my_user_id'],
                    'my_user_name' => $arrayU['my_user_name'],
                    'my_user_avatar' => $arrayU['my_user_avatar'],
                    'my_user_phone' => $arrayU['my_user_phone'],
                    'my_user_rank' => $arrayU['my_user_rank'],
                    'tip_user' => $arrayU['tip_user'],
                    'organization_id' => $arrayU['organization_id']]);

                $user_id = JFactory::getSession()->get('my_user_id');

                $json['uri'] = '/' . $language_uri . 'records-client/';
            }
            else if ($post_register_for=='no_accaut') {
                //Авторизация/Регистрация пользователя
                //$post_password = self::generatePassword(6);
                $post_password_temp = JApplicationHelper::getHash($post_password);

                //добавляем пользователя и данные организации в базу - пользователь пока еще не подтвердил регистрацию по почте activation = '0'
                $query = "INSERT INTO `#__users` SET 
                  email='" . $post_email . "',
                  password='" . $post_password_temp . "',
                  username = '$post_name', 
                  name = '" . $post_name . "', 
                  activation = '0', 
                  registerDate = NOW() ";

                $db->setQuery($query);
                $db->query();
                $last_user_id = $db->insertid();

                $user_id = $last_user_id;

                $query = "INSERT INTO `#__users_description` SET 
                  tip_user='user',
                  phone = '" . $post_phone . "', 
                  organization_id = '0', 
                  user_id = '" . (int)$last_user_id . "' ";

                $db->setQuery($query);
                $db->query();

                //отправляем письмо с подтверждением регистрации
                $reg_code = mt_rand(1000, 9000);
                $href_reg = "https://" . $_SERVER['SERVER_NAME'] . "/?option=com_lasercity&task=statusRegistration&language=" . $language_uri . "&regcode=" . $reg_code . "&posts=" . $last_user_id;

                $message = self::sendLetterForRegister(array('language_uri' => $language_uri,'href_reg' => $href_reg, 'password_temp' => $post_password));

                mail($post_email, "Регистрация на " . $_SERVER['SERVER_NAME'], $message, "From: " . $_SERVER['SERVER_NAME'] . " <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n" . "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8");
            }

                $query = "INSERT INTO `#__lasercity_by_services` SET 
                                  user_id='" . (int)$user_id . "',
                                  affiliate_id = '" . (int)$post_affiliate_id . "',
                                  service_id = '" . json_encode($post_service_id) . "',
                                  prices_id = '" . json_encode($post_prices_id) . "',
                                  stock_id = '" . (int)$post_stock_id . "',
                                  apparat_id = '" . json_encode($post_apparat_id) . "',
                                  name = '" . $post_name . "', 
                                  email = '" . $post_email . "', 
                                  phone = '" . $post_phone . "', 
                                  koment = '" . $post_koment . "',  
                                  price = '" . array_sum($post_price) . "',  
                                  date_visit = '" . $post_date_visit . "',  
                                  date_end = '" . $post_date_end . "',  
                                  date_added = NOW() ";

                $db->setQuery($query);
                $db->query();
                $last_id = $db->insertid();

                $json['success'] = JText::_('COM_LASERCITY_ERROR_RECORDING_BY_SERVICES');

        }

        die(json_encode($json));
    }

    function getUserByEmail($email){

        $db = JFactory::getDbo();

        $query = "SELECT id FROM `#__users`  
              WHERE email='" . $email . "' AND activation='1' ";

        $db->setQuery($query);
        $result = $db->loadObjectList();
        foreach ($result as $row) {
            if (!empty($row->id)) {
                return $row->id;
                break;
            }
        }

        return 0;
    }

    //делаем запись на услуги
    /*function addUserServices()
    {
        $json = null;
        $db = JFactory::getDbo();

        $post_name = JFactory::getApplication()->input->getString('name');
        $post_phone = JFactory::getApplication()->input->getString('phone');
        $post_email = JFactory::getApplication()->input->getString('email');
        $post_comment = JFactory::getApplication()->input->getString('koment');
        $post_affiliate = JFactory::getApplication()->input->getString('affiliate');
        $post_date_visit = JFactory::getApplication()->input->getString('date_visit');
        $post_service = JFactory::getApplication()->input->getString('service');
        $post_service_txt = JFactory::getApplication()->input->getString('service_txt');
        $post_apparat = JFactory::getApplication()->input->getString('apparat');
        $post_apparat_txt = JFactory::getApplication()->input->getString('apparat_txt');
        $post_price = JFactory::getApplication()->input->getString('price');
        $post_stock = JFactory::getApplication()->input->getString('stock');

        $user_id = JFactory::getSession()->get('my_user_id');
        $is_register = JFactory::getSession()->get('register_me');
//exit($is_register);

        $lang_tag = JFactory::getLanguage()->getTag();

        //узнаем ссылку на язык
        $db->setQuery("SELECT sef FROM `#__languages`  
                      WHERE lang_code = '" . $lang_tag . "' ");
        $result = $db->loadObjectList();

        $language_uri = "";
        foreach ($result as $row) {
            if (!empty($row->sef)) {
                $language_uri = $row->sef;
                break;
            }
        }

        if (empty($post_service) || empty($post_service_txt)) {
            $json['error']['service_txt'] = JText::_('COM_LASERCITY_ERROR_SERVICE');
        }
        if (empty($post_apparat) || empty($post_apparat_txt)) {
            $json['error']['apparat_txt'] = "";//JText::_('COM_LASERCITY_ERROR_APPARAT');
        }
        if (utf8_strlen($post_name) < 3) {
            $json['error']['name'] = sprintf(JText::_('COM_LASERCITY_ERROR_NAME'), 3);
        }
        if (empty($post_phone) || !preg_match('(\+38\s\(\d{3}\)\s\d{3}\s\d{2}\s\d{2})',$post_phone)) {
            $json['error']['phone'] = JText::_('COM_LASERCITY_ERROR_PHONE');
        }
        if (empty($post_email) || !filter_var($post_email, FILTER_VALIDATE_EMAIL)) {
            $json['error']['email'] = JText::_('COM_LASERCITY_ERROR_EMAIL');
        }
        if (!empty($is_register)) {
            if(!empty(self::getUserByEmail($post_email)))
            {
                $json['error']['email'] = JText::_('COM_LASERCITY_ERROR_EMAIL_IS_SET_BY_SERVICES');
            }
        }

        if (strtotime($post_date_visit) < strtotime(date('Y-m-d H:i'))) {
            $json['error']['date_visit'] = JText::_('COM_LASERCITY_ERROR_DATE_VISIT');
        }

        if (empty($json['error'])) {

            $query = "INSERT INTO `#__lasercity_by_services` SET 
                                  user_id='" . (int)$user_id . "',
                                  affiliate_id = '" . (int)$post_affiliate . "',
                                  service_id = '" . (int)$post_service . "',
                                  stock_id = '" . (int)$post_stock . "',
                                  apparat_id = '" . (int)$post_apparat . "',
                                  name = '" . $post_name . "', 
                                  email = '" . $post_email . "', 
                                  phone = '" . $post_phone . "', 
                                  koment = '" . $post_comment . "',  
                                  price = '" . $post_price . "',  
                                  date_visit = '" . $post_date_visit . "',  
                                  date_added = NOW() ";

            $db->setQuery($query);
            $db->query();
            $last_id = $db->insertid();

            if (!empty($is_register)) {
                //Авторизация/Регистрация пользователя
                $post_password = self::generatePassword(6);
                $post_password = JApplicationHelper::getHash($post_password);
                $username = explode("@", $post_email)[0];
                //добавляем пользователя и данные организации в базу - пользователь пока еще не подтвердил регистрацию по почте activation = '0'
                $query = "INSERT INTO `#__users` SET 
                  email='" . $post_email . "',
                  password='" . $post_password . "',
                  username = '$username', 
                  name = '" . $post_name . "', 
                  activation = '0', 
                  registerDate = NOW() ";

                $db->setQuery($query);
                $db->query();
                $last_user_id = $db->insertid();

                $query = "INSERT INTO `#__users_description` SET 
                  tip_user='user',
                  phone = '" . $post_phone . "', 
                  organization_id = '0', 
                  user_id = '" . (int)$last_user_id . "' ";

                $db->setQuery($query);
                $db->query();

                //отправляем письмо с подтверждением регистрации
                $reg_code = mt_rand(1000, 9000);
                $href_reg = "https://" . $_SERVER['SERVER_NAME'] . "/?option=com_lasercity&task=statusRegistration&language=" . $language_uri . "&regcode=" . $reg_code . "&posts=" . $last_user_id;

                $message = self::sendLetterForRegister(array('language_uri' => $language_uri,'href_reg' => $href_reg, 'password_temp' => $post_password));

                mail($post_email, "Регистрация на " . $_SERVER['SERVER_NAME'], $message, "From: " . $_SERVER['SERVER_NAME'] . " <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n" . "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8");
            }

            $json['success'] = JText::_('COM_LASERCITY_ERROR_RECORDING_BY_SERVICES');
        }
        die(json_encode($json));
    }*/

    function getServices()
    {

        $json = null;

        $lang_tag = JFactory::getLanguage()->getTag();
        $db = JFactory::getDbo();

        $post_find = JFactory::getApplication()->input->getString('service');
        $post_affiliate = JFactory::getApplication()->input->getString('affiliate');

        $query = "SELECT a.prices,a.apparats FROM `#__lasercity_affiliates` as a 
                  WHERE a.id='" . $post_affiliate . "' 
                  AND a.published='1' ";
        // AND t.value LIKE '".$post_find."%' ";

        $db->setQuery($query);
        $result = $db->loadObjectList();

        $prices = array();
        //узнаем прайсы филиала
        foreach ($result as $row) {
            $prices = json_decode($row->prices, true);
        }

        $query = "SELECT p.id as prices_id, p.sex, p.apparat as apparat_id, p.data->>'$**.\"service\"' as service,p.data as prices,a.title as apparat_name 
                  FROM `#__lasercity_prices` as p 
                  LEFT JOIN `#__lasercity_apparats` as a ON p.apparat = a.id 
                  WHERE p.id IN('" . implode("','", $prices) . "')
                  AND p.published='1' ";
        $db->setQuery($query);
        $result = $db->loadObjectList();
//echo $query;
        //узнаем услуги в прайсе
        $service_id = array();

        $i=1;
        foreach ($result as $row) {
            $service_id += json_decode($row->service, true);
            //exit(var_dump(json_decode($row->prices, true)));

            //echo implode("','",array_unique($service_id));

            //ищем услугу по названию
            $query = "SELECT s.id,t.value FROM `#__lasercity_services` as s 
              LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON t.object_id = s.id 
              WHERE s.id IN('" . implode("','", $service_id) . "')
              AND t.object_column='title' 
              AND t.object_name='service' 
              AND s.published='1' 
              AND t.value LIKE '" . $post_find . "%' ";

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
                    'price' => preg_replace('/[^0-9]/','',$price),
                    'price_v' => $price,
                    'duration' => $duration,
                    'value' => $i,
                    'option' => $row->apparat_name,
                    'persones' => $row->sex
                );
                $i++;
            }

        }

        if (!is_array($json)) {
            $json[] = array(
                'service_id' => '',
                'service' => JText::_('COM_LASERCITY_STOCKS_TXT_NO_FIND'),
                'apparat' => '',
                'value' => '',
                'option' => '',
                'persones' => 'no'
            );
        }


        array_multisort (array_column($json, 'service'), SORT_ASC, $json);
        array_multisort (array_column($json, 'apparat'), SORT_ASC, $json);
        //exit('<pre>'.print_r($json).'</pre>');
        die(json_encode($json));
    }

    function getApparat()
    {

        $json = null;

        $lang_tag = JFactory::getLanguage()->getTag();
        $db = JFactory::getDbo();

        $post_apparat = JFactory::getApplication()->input->getString('apparat');
        $post_service = JFactory::getApplication()->input->getString('service');

        $query = "SELECT a.id,a.title,p.data as price
                  FROM `#__lasercity_apparats` as a 
                  LEFT JOIN `#__lasercity_prices` as p ON p.apparat = a.id 
                  WHERE a.published='1' 
                  AND a.id='" . (int)$post_apparat . "' ";

        $db->setQuery($query);
        $result = $db->loadObjectList();

        $price = 0;
        foreach ($result as $row) {

            $mas_data = json_decode($row->price, true);
            if ($mas_data) {
                foreach ($mas_data as $data) {
                    if ($data['service'] == $post_service) {
                        $price = $data['price'];
                        break;
                    }
                }
            }

            $json[] = array(
                'id' => $row->id,
                'price' => $price,
                'value' => $row->title
            );
        }
        die(json_encode($json));
    }

    function setFAQ()
    {
        $json = null;
        $db = JFactory::getDbo();

        $post_title = JFactory::getApplication()->input->getString('title');
        $post_description = JFactory::getApplication()->input->getString('description');

        $lang_tag = JFactory::getLanguage()->getTag();

        //узнаем ссылку на язык
        $language_uri = self::getLanguageUri();

        if (utf8_strlen($post_title) < 3) {
            $json['error']['title'] = sprintf(JText::_('COM_LASERCITY_ERROR_FAQ_TITLE'), 3);
        }
        if (utf8_strlen($post_description) < 3) {
            $json['error']['description'] = sprintf(JText::_('COM_LASERCITY_ERROR_FAQ_DESCRIPTION'), 3);
        }


        if (empty($json['error'])) {

            $str = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe($post_title);

            $query = "INSERT INTO `#__lasercity_faq` 
                      SET alias='$str',                                   
                      date = NOW() ";
            $db->setQuery($query);
            $db->query();
            $last_id = $db->insertid();

            $data['id'] = $last_id;
            $data['title'] = array($lang_tag => $post_title);
            $data['description'] = array($lang_tag => $post_description);
            $object_columns = array('title', 'description');
            Translator::saveTranslations($data, 'faq', $object_columns);

            $json['success'] = JText::_('COM_LASERCITY_SUCCESS_FAQ');
        }
        die(json_encode($json));
    }

    function setAnswer()
    {
        $json = null;
        $db = JFactory::getDbo();

        $post_answer = JFactory::getApplication()->input->getString('answer');
        $post_question = JFactory::getApplication()->input->getString('question');
        $post_file = JFactory::getApplication()->input->getString('load_file');

        $user_id = JFactory::getSession()->get('my_user_id');

        $lang_tag = JFactory::getLanguage()->getTag();

        //узнаем ссылку на язык
        $language_uri = self::getLanguageUri();

        if (utf8_strlen($post_answer) < 3) {
            $json['error']['answer'] = sprintf(JText::_('COM_LASERCITY_ERROR_FAQ_ANSWER'), 3);
        }


        if (empty($json['error'])) {

            @rename(JPATH_BASE . "/images/reviews/tmp/" . $post_file, JPATH_BASE . "/images/answers/" . $post_file);
            $imageConversion = new ImageConversion(JPATH_BASE . "/images/answers/" . $post_file);
            $dimensions = array( // Розміри
                array(
                    'width' => 45,
                    'height' => 45,
                    'quality' => 55
                ),
                array(
                    'width' => 90,
                    'height' => 90,
                    'quality' => 55
                ),
                array(
                    'width' => 180,
                    'height' => 180,
                    'quality' => 55
                )
            );
            $imageConversion->convert($dimensions, ['jpg', 'webp'], true);

            $query = "INSERT INTO `#__lasercity_answer` 
                      SET answer='$post_answer',                                   
                      user_id='" . (int)$user_id . "',                                   
                      city_id='" . (int)JFactory::$session->get('current_city_id') . "',                                   
                      image='" . "images/answers/" .$post_file . "',                                   
                      question_id='" . (int)$post_question . "',                                   
                      date_added = NOW() ";

            $db->setQuery($query);
            $db->query();
            $last_id = $db->insertid();

            $json['success'] = JText::_('COM_LASERCITY_SUCCESS_ANSWER');
        }
        die(json_encode($json));
    }

    function setAffiliatePhone()
    {
        $json = null;
        $db = JFactory::getDbo();

        $post_affiliate = JFactory::getApplication()->input->getString('affiliate');
        $db->setQuery("UPDATE `#__lasercity_affiliates` SET phone_click = phone_click+1 WHERE id='" . (int)$post_affiliate . "'");
        $db->query();

        die(json_encode($json));
    }

    function setLikeReview()
    {
        $post_review = JFactory::getApplication()->input->getString('review');

        $json['review'] = null;

        $review_likes = json_decode(JFactory::getSession()->get('review_likes'),true);

        if(!@in_array($post_review,$review_likes)){

            $review_likes = is_array($review_likes) ? $review_likes : array();

            array_push($review_likes,$post_review);
            JFactory::getSession()->set('review_likes', json_encode($review_likes));
            //exit($post_review."====".JFactory::getSession()->get('review_likes'));
            $db = JFactory::getDbo();
            $db->setQuery("UPDATE `#__lasercity_reviews` SET likes = likes+1 WHERE id='" . (int)$post_review . "'");
            $db->query();

            $json['review'] = 'success';
        }

        die(json_encode($json['review']));
    }

    function setLikeAnswer()
    {
        $post_answer = JFactory::getApplication()->input->getString('answer');

        $json['answer'] = null;

        $answer_likes = json_decode(JFactory::getSession()->get('answer_likes'),true);

        if(!@in_array($post_answer,$answer_likes)){

            $answer_likes = is_array($answer_likes) ? $answer_likes : array();

            array_push($answer_likes,$post_answer);
            JFactory::getSession()->set('answer_likes', json_encode($answer_likes));
            //exit($post_answer."====".JFactory::getSession()->get('answer_likes'));
            $db = JFactory::getDbo();
            $db->setQuery("UPDATE `#__lasercity_answer` SET likes = likes+1 WHERE id='" . (int)$post_answer . "'");
            $db->query();

            $json['answer'] = 'success';
        }

        die(json_encode($json['answer']));
    }

    function getLastVisitUser()
    {
        $json = null;
        $db = JFactory::getDbo();

        $user_id = JFactory::getSession()->get('my_user_id');
        $db->setQuery("SELECT date_visit FROM `#__lasercity_reviews`  
                      WHERE user_id = '" . $user_id . "' ");
        $result = $db->loadObjectList();

        foreach ($result as $row) {
            if (!empty($row->date_visit)) {
                $json['date'] = date('Y-m-d H:i', strtotime($row->date_visit));
                break;
            }
        }

        die(json_encode($json));
    }

    function setRegisterByStep0(){

        $json = null;
        $post_phone = JFactory::getApplication()->input->getString('phone');

        if (!preg_match('(\+38\s\(\d{3}\)\s\d{3}\s\d{2}\s\d{2})',$post_phone) /*&& !preg_match('(\+38\s\(\d{3}\)\s\d{3}\s\d{2}\s\d{2})',JFactory::getSession()->get('register_step_phone'))*/) {
            $json['error']['phone'] = JText::_('COM_LASERCITY_ERROR_PHONE');
        }
        /*else{
            JFactory::getSession()->set('register_step_phone', $post_phone);
        }*/

        if (empty($json['error'])) {
            $json['success'] = 'success';
        }

        die(json_encode($json));
    }

    function setRegisterByStep1(){

        $json = null;
        $db = JFactory::getDbo();
        $post_organization = JFactory::getApplication()->input->getString('organization');
        $post_organization_sel = JFactory::getApplication()->input->getString('organization_sel');

        $lang_tag = JFactory::getLanguage()->getTag();

        if (empty($post_organization_sel) || (!empty($post_organization_sel) && utf8_strlen($post_organization_sel) < 3)) {
            $json['error']['organization_sel'] = sprintf(JText::_('COM_LASERCITY_ERROR_ORGANIZACION_REGISTER'), 3);

            if (empty($post_organization)) {

                $query = "SELECT t.value FROM `#__lasercity_organizations` as o 
                          LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON t.object_id = o.id 
                          WHERE t.object_column='title' AND t.value='" . $post_organization_sel . "' ";

                $db->setQuery($query);
                $result = $db->loadObjectList();
                foreach ($result as $row) {
                    if (!empty($row->value)) {
                        $json['error']['organization_sel'] = JText::_('COM_LASERCITY_ERROR_ORGANIZACION_IS_SET');
                        break;
                    }
                }
            }
        }

        if (empty($json['error'])) {
            $json['success'] = 'success';
        }

        die(json_encode($json));
    }

    function setRegisterByStep2()
    {

        $json = null;
        $arrayStatus['post_phone'] = JFactory::getApplication()->input->getString('phone1');

        if (!preg_match('(\+38\s\(\d{3}\)\s\d{3}\s\d{2}\s\d{2})', $arrayStatus['post_phone']) /*&& !preg_match('(\+38\s\(\d{3}\)\s\d{3}\s\d{2}\s\d{2})',JFactory::getSession()->get('register_step_phone'))*/) {
            $json['error']['phone'] = JText::_('COM_LASERCITY_ERROR_PHONE');
        }
        /*else{
            JFactory::getSession()->set('register_step_phone', $arrayStatus['post_phone']);
        }*/

        if (empty($json['error']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' && strtotime(JFactory::getSession()->get('register_time_code')) < strtotime(date("Y-m-d H:i:s"))) {

            /******Здесь посылаем запрос на API sms розсылки и записываем полученый код в сесию******/
            $arrayStatus['post_code'] = mt_rand(1000, 9000);

            $db = JFactory::getDbo();
            $arrayStatus['status_send'] = '';
            $arrayStatus['message_id'] = '';
            $arrayStatus['status_user'] = '';
            $arrayStatus['AuthResult'] = '';
            $arrayStatus['GetCreditBalanceResult'] = '';
            $arrayStatus['text'] = '';

// Все данные возвращаются в кодировке UTF-8
//header('Content-type: text/html; charset=utf-8');
            try {

                // Подключаемся к серверу
                $client = new SoapClient('http://turbosms.in.ua/api/wsdl.html');

                // Можно просмотреть список доступных методов сервера
                //print_r($client->__getFunctions());

                // Данные авторизации
                $auth = [
                    'login' => 'sendsmstouser',
                    'password' => '14smile11'
                ];

                //секретный ключ ---- E76[56Rqh]ds*0)

                // Авторизируемся на сервере
                $result = $client->Auth($auth);

                // Результат авторизации
                $arrayStatus['AuthResult'] = $result->AuthResult . PHP_EOL;

                // Получаем количество доступных кредитов
                $result = $client->GetCreditBalance();
                $arrayStatus['GetCreditBalanceResult'] = $result->GetCreditBalanceResult . PHP_EOL;

                if (empty(str_replace(array('.', ','), "", $arrayStatus['GetCreditBalanceResult']))) {

                    $db->setQuery("SELECT email FROM `#__lasercity_settings`  
                      WHERE published = '1' AND who='register_info' ");
                    $result = $db->loadObjectList();

                    foreach ($result as $row) {
                        if (!empty($row->email)) {
                            mail($row->email, "Сообщение с формы регистриции", 'У вас нулевой баланс, пополните пожалуйста счет!', "From: " . $_SERVER['SERVER_NAME'] . " <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n" . "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8");
                        }
                    }
                }

                // Текст сообщения ОБЯЗАТЕЛЬНО отправлять в кодировке UTF-8
                $arrayStatus['text'] = sprintf(JText::_('COM_LASERCITY_REGISTER_BY_STEP_SEND_CODE'), $arrayStatus['post_code']);

                // Отправляем сообщение на один номер.
                // Подпись отправителя может содержать английские буквы и цифры. Максимальная длина - 11 символов.
                // Номер указывается в полном формате, включая плюс и код страны
                $sms = [
                    'sender' => 'LaserCity',
                    //'sender' => 'Lumenis',//************************************************************когда одобрят заменить на LaserCity
                    'destination' => preg_replace('/\s+/', '', str_replace(array('(', ')'), "", $arrayStatus['post_phone'])),
                    'text' => $arrayStatus['text'],
                    'wappush' => 'https://lasercity.com.ua'
                ];
                $result = $client->SendSMS($sms);

                //echo var_dump($result);

                // Выводим результат отправки.
                if (!is_array($result->SendSMSResult->ResultArray[1])) {
                    //статус отправки с ошибкой
                    $arrayStatus['status_send'] = $result->SendSMSResult->ResultArray;
                } else {
                    $arrayStatus['status_send'] = $result->SendSMSResult->ResultArray[0];
                    $arrayStatus['message_id'] = $result->SendSMSResult->ResultArray[1];

                    // Запрашиваем статус конкретного сообщения по ID
                    //$sms = ['MessageId' => '01eb5925-5d3a-59d3-acfd-5b0a955862b4'];
                    $sms = ['MessageId' => $arrayStatus['message_id']];
                    $status_user = $client->GetMessageStatus($sms);
                    $arrayStatus['status_user'] = $status_user->GetMessageStatusResult . PHP_EOL;
                }

            } catch (Exception $e) {
                //echo 'Ошибка: ' . $e->getMessage() . PHP_EOL;
                //сохраняем статус сообщения
                $arrayStatus['status_send'] = 'Ошибка: ' . $e->getMessage() . PHP_EOL;
            }

            //сохраняем статус сообщения
            self::saveStatusSms($arrayStatus);

            exit();

            //если запрос удачный записываем код в сесию
            JFactory::getSession()->set('register_phone_code', $arrayStatus['post_code']);
            //создаем защиту на повтор в 30 секунд
            JFactory::getSession()->set('register_time_code', date("Y-m-d H:i:s", strtotime("+30 seconds")));

            $json['success'] = 'success';
        }

        die(json_encode($json));
    }

    function setSmsStatus($array){

    }
    function saveStatusSms($array){

        $array['user_id'] = JFactory::getSession()->get('my_user_id');
        $db = JFactory::getDbo();
        $query = "INSERT INTO `#__lasercity_status_sms` SET 
                  user_id='" . (int)$array['user_id'] . "',
                  message_id='".$array['message_id']."',
                  phone='".$array['post_phone']."',
                  post_code='".$array['post_code']."',
                  text_sms='".$array['text']."',
                  status_user='".$array['status_user']."',
                  status_send = '".$array['status_send']."', 
                  auth_result = '".$array['AuthResult']."', 
                  getcredit_balance = '".$array['GetCreditBalanceResult']."', 
                  date_added = NOW() ";

        $db->setQuery($query);
        $db->query();
    }

    function setRegisterByStep3(){

        $json = null;
        $db = JFactory::getDbo();

        $post_name = JFactory::getSession()->get('post_name');
        $post_register_for = JFactory::getSession()->get('post_register_for');
        $post_phone = JFactory::getSession()->get('post_phone');
        $post_password = JFactory::getSession()->get('post_password');
        $post_email = JFactory::getSession()->get('post_email');

        $post_organization = JFactory::getApplication()->input->getString('organization');
        $post_organization_sel = JFactory::getApplication()->input->getString('organization_sel');
        $post_phone = (empty($post_phone)) ? JFactory::getApplication()->input->getString('phone') : $post_phone;
        $post_phone1 = JFactory::getApplication()->input->getString('phone1');
        $post_code = JFactory::getApplication()->input->getString('code');
        $get_register_phone_code = JFactory::getSession()->get('register_phone_code');
        //$get_register_phone_code=8888;

        if (empty($get_register_phone_code) || empty($post_code) || $get_register_phone_code!=$post_code || !preg_match('(\d{4})',$post_code)) {
            $json['error']['code'] = JText::_('COM_LASERCITY_REGISTER_BY_STEP_ERROR_CODE');
        }

        if (empty($json['error'])) {

            $lang_tag = JFactory::getLanguage()->getTag();

            //узнаем ссылку на язык
            $language_uri = self::getLanguageUri();

            //если введена новая организация
            if (empty($post_organization) && !empty($post_organization_sel)) {

                $username = explode("@", $post_email)[0];

                //если введена новая организация
                if (empty($post_organization) && !empty($post_organization_sel)) {

                    //добавлям новую запись оранизации - не опубликованная
                    $query = "INSERT INTO `#__lasercity_organizations` SET 
                      published='0' ";

                    $db->setQuery($query);
                    $db->query();
                    $post_organization = $db->insertid();

                    //добавляем название огранизации в таблицу языков
                    $query = "SELECT lang_code FROM `#__languages`  
                          WHERE lang_id > 0 ";

                    $db->setQuery($query);
                    $result = $db->loadObjectList();

                    foreach ($result as $row) {
                        if (!empty($row->lang_code)) {
                            $db->setQuery("INSERT INTO `#__lasercity_translations_" . $row->lang_code . "` SET 
                                      object_name='organization',
                                      object_column='title',
                                      value = '" . $post_organization_sel . "', 
                                      object_id = '" . (int)$post_organization . "'
                                      ");
                            $db->query();
                        }
                    }

                }

                //добавляем пользователя и данные организации в базу - пользователь пока еще не подтвердил регистрацию по почте activation = '0'
                $query = "INSERT INTO `#__users` SET 
                  email='" . $post_email . "',
                  password='" . $post_password . "',
                  username = '$username', 
                  name = '" . $post_name . "', 
                  activation = '0', 
                  registerDate = NOW() ";

                $db->setQuery($query);
                $db->query();
                $last_user_id = $db->insertid();

                $query = "INSERT INTO `#__users_description` SET 
                  tip_user='" . $post_register_for . "',
                  phone = '" . $post_phone . "', 
                  organization_id = '" . (int)$post_organization . "', 
                  user_id = '" . (int)$last_user_id . "' ";

                $db->setQuery($query);
                $db->query();

                //отправляем письмо с подтверждением регистрации
                $reg_code = mt_rand(1000, 9000);
                $href_reg = "https://" . $_SERVER['SERVER_NAME'] . "/?option=com_lasercity&task=statusRegistration&language=" . $language_uri . "&regcode=" . $reg_code . "&posts=" . $last_user_id;

                $message = self::sendLetterForRegister(array('language_uri' => $language_uri,'href_reg' => $href_reg));

                if (mail($post_email, "Регистрация на " . $_SERVER['SERVER_NAME'], $message, "From: " . $_SERVER['SERVER_NAME'] . " <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n" . "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8")) {


                    $message = "
                <!DOCTYPE html>
                <head>
                <meta charset='utf-8'>
                <title>Регистрация " . (($post_register_for == 'organization') ? 'Партнера' : 'Пользователя') . "</title>
                </head>
                    <body>
                        <div title='" . $_SERVER['SERVER_NAME'] . "' style='background:#F0F0F0;width:700px;height:auto;margin:auto;padding:20px 50px 40px 50px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;'>
                        <a href='https://" . $_SERVER['SERVER_NAME'] . "/" . $language_uri . "/'>" . strtoupper($_SERVER['SERVER_NAME']) . "<!--<img src='https://" . $_SERVER['SERVER_NAME'] . "/img/logo.svg' alt=''>--></a>
                            <div style='text-align:justify;margin-top:20px;padding:20px;background:white;font-size:15px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-icab-border-radius:5px;border-radius:5px;-moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);-webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);'>
                               Тип: <b>" . (($post_register_for == 'organization') ? 'Партнер' : 'Пользователь') . "</b><br>
                               " . (!empty($post_organization_sel) ? 'Название организации: <b>' . $post_organization_sel . '</b><br>' : '') . "
                               ФИО: <b>" . $post_name . "</b><br>
                               " . (!empty($post_phone) ? 'Телефон: <b>' . $post_phone . '</b><br>' : '') . "
                               " . (!empty($post_email) ? 'E-mail: <b>' . $post_email . '</b><br>' : '') . "
                            </div>
                            <div style='font-size:12px;margin-top:20px;width:100%;text-align:center;color:#989898;'>Пожалуйста, не отвечайте на это сообщение оно было отправлено автоматически.</div>
                        </div>
                    </body>
                </html>";

                    $db->setQuery("SELECT email FROM `#__lasercity_settings`  
                      WHERE published = '1' AND who='register_info' ");
                    $result = $db->loadObjectList();

                    foreach ($result as $row) {
                        if (!empty($row->email)) {
                            mail($row->email, "Регистрация " . (($post_register_for == 'organization') ? 'ОРГАНИЗАЦИИ' : 'Пользователя'), $message, "From: " . $_SERVER['SERVER_NAME'] . " <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n" . "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8");
                        }
                    }

                    if (!empty($post_organization_sel)) {
                        $json['success'] = 'На ваш e-mail ' . explode("@", $post_email)[0] . '@<a href="https://' . explode("@", $post_email)[1] . '">' . explode("@", $post_email)[1] . '</a> было отправлено письмо с подтверждение регистрации, пожалуйста подтвердите регистрацию! Наш менеджер свяжется с Вами в ближайшее время.';
                    } else {
                        $json['success'] = 'На ваш e-mail ' . explode("@", $post_email)[0] . '@<a href="https://' . explode("@", $post_email)[1] . '">' . explode("@", $post_email)[1] . '</a> было отправлено письмо с подтверждение регистрации, пожалуйста подтвердите регистрацию!';
                    }
                }

            }

            $json['success'] = 'success';
        }

        die(json_encode($json));
    }



}


