<?php defined('_JEXEC') or die;

class lasercityModelarticles extends JModelList
{
    public function getItems()
    {
        $tag = JFactory::$language->getTag();


        $query = "
SELECT * FROM (
    SELECT `alias`, `main_image`, `date`,`likes`,`see`,
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
    ORDER BY `ordering` ASC, `date` DESC
) as `t`
        ";


        $search = addslashes(JFactory::$application->input->getString('search'));
        if ($search != null) {
            $query .= "WHERE LOWER(`title`) LIKE LOWER('%{$search}%')";
        }
        $this->_db->setQuery($query);
        $articles = $this->_db->loadObjectList();

        foreach ($articles as $article) {
            $article->description = substr(strip_tags($article->description), 0, 105) . '...';
        }
        $limit = 8;
        $count = count($articles);
        $pages = ceil($count / $limit);
        $page = JFactory::$application->input->getInt('page', 1);
        $page = is_numeric($page) ? $page : 1;
        $page = $page > $pages ? $pages : $page;

        $articles = array_slice($articles, ($page - 1) * $limit, $limit);

        $result['articles'] = $articles;
        $result['page'] = $page;
        $result['pages'] = $pages;
        $result['count'] = $count;
        $result['limit'] = $limit;


        return $result;
    }
}
