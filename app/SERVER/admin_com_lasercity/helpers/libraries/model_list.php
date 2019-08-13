<?php defined('_JEXEC') or die;

class ModelList extends JModelList
{
    public $filter_fields = array();

    function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = $this->filter_fields;
        }
        parent::__construct($config);
    }

    function setQuery($query, $translate = false, $data_columns = array()) {
        if ($translate) {
            $lang = $this->getState('filter.lang');
            if ($lang == null) {
                $lang = JFactory::getLanguage()->getTag();
            }
            $query = Translator::setQuery($query, $lang, $data_columns);
        }
        $where = array();

        $search = mb_strtolower($this->getState('filter.search'));
        if (!empty($search)) {
            $where[] = "LOWER(`table`.`title`) LIKE '%{$search}%'";
        }

        $line = $this->getState('filter.line');
        if (!empty($line)) {
            $where[] = "LOWER(`table`.`line`) LIKE '%{$line}%'";
        }

        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $where[] = "`table`.`published`={$published}";
        }

        $city = $this->getState('filter.city');
        if (is_numeric($city)) {
            $where[] = "`table`.`city`={$city}";
        }

        $district = $this->getState('filter.district');
        if (is_numeric($district)) {
            $where[] = "`table`.`district`={$district}";
        }

        $type = $this->getState('filter.type');
        if (is_numeric($type)) {
            $where[] = "`table`.`type`={$type}";
        }

        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $orderCol = $this->state->get('list.ordering', 'id');
        $orderDir = $this->state->get('list.direction', 'desc');

        $query .= " ORDER BY `{$orderCol}` {$orderDir}";

        //var_dump($query);
        
        return $query;
    }
}