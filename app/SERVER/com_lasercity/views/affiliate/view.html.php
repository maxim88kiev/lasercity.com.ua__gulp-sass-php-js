<?php defined('_JEXEC') or die;

jimport('components.com_lasercity.helpers.ip', JPATH_BASE);

class lasercityViewaffiliate extends JViewLegacy
{
    public $item = null;
    public $countView = 0;

    function display($tpl = null)
    {
        $this->item = $this->get('Item');
        $id = $this->item->id;
        lastVisit('affiliate', $this->item->id);

        $db = JFactory::getDbo();
        $this->countView = $db->setQuery("SELECT COUNT(*) FROM `#__lasercity_last_visit` WHERE `object_type`='affiliate' AND `object_id`={$id} AND DATE(`last_visit`) = CURDATE()")->loadResult();

        $params = OpenGraph::loadParams('affiliate', $this->item->id);
        OpenGraph::autoSet($params);

        /*$id = $this->item->id;
        $ip = IP::get();
        $db = JFactory::getDbo();

        $db->setQuery("SELECT `last_visit` FROM `#__lasercity_affiliates_last_visit` WHERE `ip`={$ip}");
        
        $last_visit = $db->loadResult();
        
        if ($last_visit == null) {
            $db->setQuery("INSERT INTO `#__lasercity_affiliates_last_visit` (`affiliate_id`, `last_visit`, `ip`) VALUES ({$id}, NOW(), '{$ip}')")->execute();
            $db->setQuery("UPDATE `#__lasercity_affiliates` SET `see`=`see`+1 WHERE `id`={$id}")->execute();
        } else {
            if (strtotime($last_visit) != strtotime(date('Y-m-d'))) {
                $db->setQuery("UPDATE `#__lasercity_affiliates_last_visit` SET `last_visit`=NOW()")->execute();
                $db->setQuery("UPDATE `#__lasercity_affiliates` SET `see`=`see`+1 WHERE `id`={$id}")->execute();
            }
        }*/
        
        
        JFactory::getDocument()->setTitle($this->item->title);
        JFactory::getDocument()->setDescription($this->item->head_description);
        return parent::display($tpl);
    }
    

}
