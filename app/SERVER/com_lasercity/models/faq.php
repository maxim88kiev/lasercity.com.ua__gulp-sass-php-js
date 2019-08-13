<?php defined('_JEXEC') or die;

class lasercityModelfaq extends JModelList
{
    public function getItems()
    {
        $tag = JFactory::$language->getTag();
        $query = "
SELECT * FROM (
    SELECT `id`, `alias`, `date`,
        (
            SELECT `value`
            FROM `#__lasercity_translations_{$tag}`
            WHERE 
                `object_id`=`main`.`id` AND
                `object_column`='title' AND
                `object_name`='faq'
        ) as `title`,
        (
            SELECT `value`
            FROM `#__lasercity_translations_{$tag}`
            WHERE 
                `object_id`=`main`.`id` AND
                `object_column`='description' AND
                `object_name`='faq'
        ) as `description`
    FROM `#__lasercity_faq` as `main`
    WHERE `published`
    ORDER BY `ordering` ASC, `date` DESC, `id` DESC
) as `t`
        ";
        $search = addslashes(JFactory::$application->input->getString('search'));
        if ($search != null) {
            $query .= "WHERE LOWER(`t`.`title`) LIKE LOWER('%{$search}%')";
        }
        $this->_db->setQuery($query);
        $faq = $this->_db->loadObjectList();

        $limit = 8;
        $count = count($faq);
        $pages = ceil($count / $limit);
        $page = JFactory::$application->input->getInt('page', 1);
        $page = is_numeric($page) ? $page : 1;
        $page = $page > $pages ? $pages : $page;

        $faq = array_slice($faq, ($page - 1) * $limit, $limit);

        foreach ($faq as $item) {
            $item->description = strip_tags($item->description);
        }

        $result['faq'] = $faq;
        $result['page'] = $page;
        $result['pages'] = $pages;
        $result['count'] = $count;
        $result['limit'] = $limit;
        return $result;
    }
}