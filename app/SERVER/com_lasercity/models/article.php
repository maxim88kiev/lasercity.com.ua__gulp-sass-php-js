<?php defined('_JEXEC') or die;

class lasercityModelArticle extends JModelItem
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
        `object_name`='article'
) as `title`,
(
    SELECT `value`
    FROM `#__lasercity_translations_{$tag}`
    WHERE 
        `object_id`=`main`.`id` AND
        `object_column`='description' AND
        `object_name`='article'
) as `description`,
(
    SELECT `value`
    FROM `#__lasercity_translations_{$tag}`
    WHERE 
        `object_id`=`main`.`id` AND
        `object_column`='head_description' AND
        `object_name`='article'
) as `head_description`
FROM `#__lasercity_articles` as `main`
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
}