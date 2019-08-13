<?php defined('_JEXEC') or die;

class LasercityRouter extends JComponentRouterBase
{
    private function provokeException($message = 'Page no found!')
    {
        throw new RuntimeException($message, '404');
    }

    private function unionBuilder($objects, $alias, $as)
    {
        $union_query = [];
        foreach ($objects as $object) {
            $union_query[] = "(SELECT `id`, `alias`, '{$object['view']}' as `{$as}` FROM `#__lasercity_{$object['table_part']}` WHERE `alias`={$alias})";
        }
        return implode(' UNION ALL ', $union_query);
    }

    public function build(&$query)
    {
        return array();
    }

    public function parse(&$segments)
    {
        $vars = array();
        $item = $this->menu->getActive();
        if (!isset($item)) {
            $vars['view'] = 'home';
            return $vars;
        }

        $db = JFactory::getDbo();
        $count = count($segments);
        $count_decrement = $count - 1;

        $page = $segments[0];

        try {
            $input = JFactory::getApplication()->input;
        } catch (Exception $e) {
            die('Can\'t get input from Application in class JFactory');
        }

        if ($page == 'clinics') {
            if ($count_decrement < 1) {
                $this->provokeException();
            }
            if (!$count_decrement) {
                $vars['view'] = $page;
                return $vars;
            }
            $alias = $db->quote($segments[1]);
            $query = $this->unionBuilder(array(
                array(
                    'view' => 'clinics',
                    'table_part' => 'cities'
                ),
                array(
                    'view' => 'organization',
                    'table_part' => 'organizations'
                ),
                array(
                    'view' => 'affiliate',
                    'table_part' => 'affiliates'
                )
            ), $alias, 'view');
            $db->setQuery($query);
            $result_obj = $db->loadObject();

            if ($result_obj != null) {
                if ($count_decrement == 2) {
                    $toFilterSegment = $db->quote($segments[2]);
                    $query = $this->unionBuilder(array(
                        array(
                            'view' => 'apparatus',
                            'table_part' => 'apparats'
                        ),
                        array(
                            'view' => 'comforts',
                            'table_part' => 'comforts'
                        ),
                        array(
                            'view' => 'districts',
                            'table_part' => 'districts'
                        ),
                        array(
                            'view' => 'locations',
                            'table_part' => 'locations'
                        ),
                        array(
                            'view' => 'metro',
                            'table_part' => 'metro'
                        ),
                        array(
                            'view' => 'micro-districts',
                            'table_part' => 'micro_districts'
                        ),
                        array(
                            'view' => 'services',
                            'table_part' => 'services'
                        ),
                        array(
                            'view' => 'zones',
                            'table_part' => 'zones'
                        ),
                    ), $toFilterSegment, 'name');
                    $db->setQuery($query);

                    $toFilterObj = $db->loadObject();

                    if ($toFilterObj == null) {
                        $this->provokeException();
                    } else {
                        $input->set('to_filter_data', array($toFilterObj->name, $toFilterObj->alias));
                    }
                }
                if ($result_obj->view == 'affiliate') {
                    $input->set('to_template_unload_affiliate_popup', false);
                }
                if ($result_obj->view == 'clinics') {
                    $input->set('current_city_id', $result_obj->id);
                    JFactory::$session->set('current_city_id', $result_obj->id);
                }
                $vars['view'] = $result_obj->view;
                $vars['id'] = $result_obj->id;
            } else {
                $this->provokeException();
            }
        } else if (in_array($page, array('articles', 'faq'))) {
            if ($count_decrement > 1) {
                $this->provokeException();
            }
            if ($count_decrement) {
                $alias = preg_replace('/[^a-z0-9_-]/', '', end($segments));
                $db->setQuery("SELECT `id` FROM `#__lasercity_{$page}` WHERE `alias`='{$alias}'");
                $id = $db->loadResult();
                if ($id == null) {
                    $this->provokeException();
                }
                $vars['view'] = $page == 'faq' ? 'faq_item' : 'article';
                $vars['id'] = $id;
                $input->set('unset_active_menu_current', true);
            } else {
                $vars['view'] = $page;
            }
        } else if ($page == 'stocks') {
            if ($count_decrement > 1) {
                $this->provokeException();
            }
            if ($count_decrement) {
                $city_alias = end($segments);
                $db->setQuery("SELECT `id` FROM `#__lasercity_cities` WHERE `alias`='{$city_alias}'");
                $id = $db->loadResult();

                if ($id == null) {
                    $alias = preg_replace('/[^a-z0-9_-]/', '', end($segments));
                    $db->setQuery("SELECT `id` FROM `#__lasercity_stock` WHERE `alias`='{$alias}'");
                    $id = $db->loadResult();
                    if ($id == null) {
                        $this->provokeException();
                    }
                    $vars['view'] = 'stock';
                    $vars['id'] = $id;
                    $input->set('unset_active_menu_current', true);
                } else {
                    $vars['view'] = 'stocks';
                    $input->set('current_city_id', $id);
                    JFactory::$session->set('current_city_id', $id);
                }


//die;

            } else {
                $vars['view'] = $page;
            }
        } else if (in_array($page, array('register-success', 'register-error'))) {
            $vars['view'] = str_replace('-', '_', $page);
        } else if ($page == 'privacy') {
            $vars['view'] = $page;
        } else if ($page == 'register') {
            $vars['view'] = $page;
        } else if ($page == 'register-step') {
            $vars['view'] = str_replace('-', '_', $page);
        } else if ($page == 'servises-record') {
            $vars['view'] = str_replace('-', '_', $page);
        } else if ($page == 'password-change') {
            $vars['view'] = str_replace('-', '_', $page);
        } else if ($page == 'records-client') {
            $vars['view'] = str_replace('-', '_', $page);
        } else if ($page == 'record-client') {
            $vars['view'] = str_replace('-', '_', $page);
        } else if ($page == 'record-salon') {
            $vars['view'] = str_replace('-', '_', $page);
        } else if ($page == 'records-salon') {
            $vars['view'] = str_replace('-', '_', $page);
        } else {
            $city = $db->quote($page);
            $db->setQuery("SELECT id FROM #__lasercity_cities WHERE alias={$city}");
            $city_id = $db->loadResult();
            if ($city_id == null) {
                $this->provokeException();
            } else {
                $input->set('current_city_id', $city_id);
                JFactory::$session->set('current_city_id', $city_id);
            }
        }

        return $vars;
    }
}