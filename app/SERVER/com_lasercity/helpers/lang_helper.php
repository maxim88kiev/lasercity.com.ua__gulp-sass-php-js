<?php defined('_JEXEC') or die;

class LangHelper
{
    private static $list = null;
    private static $sef = null;

    public static function getList()
    {
        if (self::$list != null) {
            return self::$list;
        }
        $db = JFactory::getDbo();
        $current_tag = JFactory::getLanguage()->getTag();
        $query = $db->getQuery(true)->select('sef, lang_code')->from('#__languages')->where('published');
        $out = array();
        $current = array();
        foreach ($db->setQuery($query)->loadObjectList() as $key => $item) {
            if ($current_tag == $item->lang_code) {
                $item->current = true;
                $current[$key] = $item;
            } else {
                $out[] = $item;
            }
        }
        $result = array_merge($current, $out);
        self::$list = $result;
        return $result;
    }

    public static function getCurrentSef()
    {
        if (self::$sef != null) {
            return self::$sef;
        }
        $list = self::$list == null ? self::getList() : self::$list;
        $sef = null;
        foreach ($list as $lang) {
            if (isset($lang->current)) {
                $sef = $lang->sef;
                break;
            }
        }
        return $sef;
    }
}