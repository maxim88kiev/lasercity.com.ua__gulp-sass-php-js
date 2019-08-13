<?php defined('_JEXEC') or die;

jimport('components.com_lasercity.models.affiliate', JPATH_SITE);

class LasercityViewstock extends JViewLegacy
{
    public $item = null;

    function display($tpl = null)
    {

        /*$affiliateModel = new lasercityModelAffiliate;

        echo '<pre>';
        print_r($affiliateModel->getItem(3));
        echo '</pre>';*/
        $this->item = $this->get('Item');
        $this->item->description = strip_tags($this->item->description);
        $this->document->setTitle($this->item->title);
        $this->document->setDescription($this->item->head_description);
        $params = OpenGraph::loadParams('stock', $this->item->id);
        OpenGraph::autoSet($params);
        return parent::display($tpl);
    }
}
