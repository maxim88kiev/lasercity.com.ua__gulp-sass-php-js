<?php defined('_JEXEC') or die;

class lasercityModelstocks extends JModelList
{
    public function getItems()
    {
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
    AND `city_id`='".JFactory::$session->get('current_city_id')."'
    ORDER BY `ordering` ASC
) as `t`
        ";
        $search = addslashes(JFactory::$application->input->getString('search'));
        if ($search != null) {
            $query .= "WHERE LOWER(`title`) LIKE LOWER('%{$search}%')";
        }
        $this->_db->setQuery($query);
        $stocks = $this->_db->loadObjectList();
        foreach ($stocks as $stock) {
            $stock->description = substr(strip_tags($stock->description), 0, 20) . '...';
        }
        $limit = 8;
        $count = count($stocks);
        $pages = ceil($count / $limit);
        $page = JFactory::$application->input->getInt('page', 1);
        $page = is_numeric($page) ? $page : 1;
        $page = $page > $pages ? $pages : $page;

        $stocks = array_slice($stocks, ($page - 1) * $limit, $limit);

        $result['stocks'] = $stocks;
        $result['page'] = $page;
        $result['pages'] = $pages;
        $result['count'] = $count;
        $result['limit'] = $limit;
        return $result;
    }
}
