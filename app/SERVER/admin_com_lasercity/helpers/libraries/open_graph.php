<?php defined('_JEXEC') or die;

class OpenGraph
{
    private static $tags = array(
        'og:locale' => '',
        'og:type' => 'article',
        'og:title' => '',
        'og:description' => '',
        'og:url' => '',
        'og:image' => '',
        'og:site_name' => '',
    );

    private static $table = '#__lasercity_open-graph';

    public static function save($objectName, $objectId, $params)
    {
        $db = JFactory::getDbo();
        $objectName = $db->quote($objectName);
        $params = $db->quote(json_encode($params));
        $objectId = (int)$objectId;
        $tableName = self::$table;
        $count = $db->setQuery("SELECT COUNT(*) FROM `{$tableName}` WHERE `object_name`={$objectName} AND `object_id`={$objectId}")->loadResult();
        if ($count) {
            $db->setQuery("UPDATE `{$tableName}` SET `params`={$params} WHERE `object_name`={$objectName} AND `object_id`={$objectId}")->execute();
        } else {
            $db->setQuery("INSERT INTO `{$tableName}` (`object_name`, `object_id`, `params`) VALUES ({$objectName}, {$objectId}, {$params})")->execute();
        }
    }

    public static function loadParams($objectName, $objectId)
    {
        $db = JFactory::getDbo();
        $objectId = (int)$objectId;
        $objectName = $db->quote($objectName);
        $tableName = self::$table;
        $result = $db->setQuery("SELECT `params` FROM `{$tableName}` WHERE `object_name`={$objectName} AND `object_id`={$objectId}")->loadResult();
        if ($result !== null) {
            return json_decode($result, true);
        }
        return array();
    }

    public static function setTag($name, $value)
    {
        $name = "og:{$name}";
        if (isset(self::$tags[$name])) {
            self::$tags[$name] = $value;
        }
    }

    public static function getTag($name = null)
    {
        if ($name === null) {
            return self::$tags;
        } else if (isset(self::$tags[$name])) {
            return self::$tags[$name];
        }
        return null;
    }

    public static function drawTags()
    {
        foreach (self::$tags as $property => $content) {
            echo "<meta property=\"{$property}\" content=\"$content\">\r\n";
        }
    }

    public static function autoSet($params)
    {
        $tag = JFactory::getLanguage()->getTag();
        foreach ($params as $property => $content) {
            $property = str_replace('og:', '', $property);
            if (is_array($content)) {
                if (isset($content[$tag])) {
                    self::setTag($property, $content[$tag]);
                }
            } else {
                self::setTag($property, $content);
            }
        }
    }
}
