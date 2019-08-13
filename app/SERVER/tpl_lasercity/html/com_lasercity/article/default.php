<?php defined('_JEXEC') or die;

$obj_city = CityHelper::getCurrent();
$city_alias = $obj_city->alias;

//узнаем ссылку на язык
$default_sef = "";
$current_sef = LangHelper::getCurrentSef();
$language_default = JComponentHelper::getParams('com_languages')->get('site');
$db = JFactory::getDbo();
$db->setQuery("SELECT sef FROM `#__languages` WHERE lang_code = '" . $language_default . "' ");
$result = $db->loadObjectList();
foreach ($result as $row) {
    if (!empty($row->sef)) {
        $default_sef = $row->sef;
        break;
    }
}
$current_sef = str_replace($default_sef, "", $current_sef);
$current_sef = empty($current_sef) ? "/" : "/" . $current_sef . "/";

//$link_mok = $current_sef."articles/{$city_alias}/";
$link_mok = $current_sef . "articles/";

//echo '<pre>';
//print_r($this->item);
//echo '</pre>';
?>
<main>
  <nav class="breadcrumbs breadcrumbs--border">
    <ul class="breadcrumbs__list">
        <ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="<?= $current_sef ?>"><span itemprop="name"><?= JText::_('COM_LASERCITY_ALL_GENERAL') ?></span></a>
                <meta itemprop="position" content="1"/>
            </li>
            <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="<?= "{$current_sef}articles/" ?>"><span itemprop="name"><?= JText::_('COM_LASERCITY_ARTICLE_LI_1') ?></span></a>
                <meta itemprop="position" content="2"/>
            </li>
            <li class="breadcrumbs__item breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="#"><span itemprop="name"><?= $this->item->title ?></span></a>
                <meta itemprop="position" content="3"/>
            </li>
        </ol>
    </ul>
  </nav>
<?php
/* вывод шаблонов статей */
$path = $this->item->alias.".php";
$adress_path = $_SERVER['DOCUMENT_ROOT']."templates/lasercity/html/com_lasercity/article/poleznyj_material_";
$page_leng = LangHelper::getCurrentSef();
if($page_leng =="ru"){
    include $adress_path."ru/".$path;
}else{
    include $adress_path."ua/".$path;
}
?>

</main>