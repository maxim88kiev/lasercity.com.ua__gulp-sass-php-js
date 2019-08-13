<?php defined('_JEXEC') or die;

class lasercityModelfaq_item extends JModelItem
{
    public function getItem($id = null)
    {
        $tag = JFactory::$language->getTag();
        $id = (!empty($id)) ? $id : (int)$this->getState('item.id');
        $this->_db->setQuery("
SELECT *,
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
) as `description`,
(
    SELECT `value`
    FROM `#__lasercity_translations_{$tag}`
    WHERE 
        `object_id`=`main`.`id` AND
        `object_column`='head_description' AND
        `object_name`='faq'
) as `head_description`
FROM `#__lasercity_faq` as `main`
WHERE `id`={$id}
        ");
        return $this->_db->loadObject();
    }

    function populateState()
    {
        $app = JFactory::$application;
        $id = $app->input->getInt('id', 0);
        $this->setState('item.id', $id);
        parent::populateState();
    }

    public static function getAnswers($id)
    {
        $language = JFactory::$language->getTag();
        $db = JFactory::getDbo();

        $query = "SELECT a.*,u.name,d.avatar,d.rank, TO_DAYS(NOW())-TO_DAYS(u.registerDate) as day_togeva,t.value,

                  (SELECT COUNT(*) FROM `#__lasercity_reviews` WHERE user_id=a.user_id AND a.user_id!=0) as count_review,
                  (SELECT COUNT(*) FROM `#__lasercity_question` WHERE user_id=a.user_id AND a.user_id!=0) as count_question,
                  (SELECT COUNT(*) FROM `#__lasercity_answer` WHERE user_id=a.user_id AND a.user_id!=0) as count_answer
                  
                  FROM `#__lasercity_answer` as a
                  LEFT JOIN `#__users` as u ON(a.user_id=u.id) 
                  LEFT JOIN `#__users_description` as d ON(u.id=d.user_id) 
                  LEFT JOIN `#__lasercity_cities` as c ON (a.city_id=c.id) 
                  LEFT JOIN `#__lasercity_translations_" . $language . "` as t ON (c.id = t.object_id) 
                  WHERE a.published='1'
                  AND t.object_name='citie' 
                  AND t.object_column='title' 
                  AND a.question_id='$id' 
                  ORDER BY a.id DESC";

        $db->setQuery($query);
        $answers = $db->loadObjectList();
        /*foreach ($answers as $answer) {
            $answer->description = substr(strip_tags($answer->description), 0, 20) . '...';
        }*/
        $limit = 1;
        $count = count($answers);
        $pages = ceil($count / $limit);
        $page = JFactory::$application->input->getInt('page', 1);
        $page = is_numeric($page) ? $page : 1;
        $page = $page > $pages ? $pages : $page;

        $answers = array_slice($answers, ($page - 1) * $limit, $limit);

        $result['answers'] = $answers;
        $result['page'] = $page;
        $result['pages'] = $pages;
        $result['count'] = $count;
        $result['limit'] = $limit;
        return $result;
    }

}