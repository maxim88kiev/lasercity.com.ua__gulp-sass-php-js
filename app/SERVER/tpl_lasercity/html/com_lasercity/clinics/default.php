<?php defined('_JEXEC') or die;
$obj_city = CityHelper::getCurrent();
$city_alias = $obj_city->alias;


//узнаем ссылку на язык
$default_sef = "";
$current_sef = LangHelper::getCurrentSef();
$language_default = JComponentHelper::getParams('com_languages')->get('site');
$db = JFactory::getDbo();
$db->setQuery("SELECT sef FROM `#__languages` WHERE lang_code = '".$language_default."' ");
$result = $db->loadObjectList();
foreach ($result as $row) {
    if (!empty($row->sef)) {$default_sef = $row->sef; break;}
}
$current_sef = str_replace($default_sef,"",$current_sef);
$current_sef = empty($current_sef) ? "/" : "/".$current_sef."/";


ContentLoader::addScript('salons');

ContentLoader::addPopup('clinics-popup');
ContentLoader::addPopup('popup-question');
ContentLoader::addPopup('popup-recording');

$city_title = null;
$city_list = CityHelper::getList();

foreach ($city_list as $item) {
    if ($item->alias == $city_alias) {
        $city_title = $item->title;
        break;
    }
}
?>


<main>
  <section class="salons__filters filters" aria-labelledby="Filter">
    <h2 class="visually-hidden" id="Filter"><?=JText::_('COM_LASERCITY_ALL_SALON_LASER_FILTER')?></h2>
    <div class="filters__wrapper">
      <form class="filters__salon-filter">
        <div class="filters__name-wrapper">
          <input class="filters__name" type="search" autocomplete="off" aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_LASER_NAME')?>" placeholder="<?=JText::_('COM_LASERCITY_ALL_SALON_LASER_NAME')?>">
          <div class="filters__name-autocomplete autocomplete">
            <ul class="autocomplete__list">
            </ul>
            <p class="autocomplete__results"><?=JText::_('COM_LASERCITY_ALL_RESULT_FIND')?> <output></output> <?=JText::_('COM_LASERCITY_ALL_RESULT_TXT')?></p>
          </div>
        </div>
        <div class="filters__place-wrapper">
          <input class="filters__place" type="search" autocomplete="off" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_LOCATION_TXT')?>" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_LOCATION_TXT')?>">
          <div class="filters__place-autocomplete autocomplete">
            <ul class="autocomplete__list">
            </ul>
            <p class="autocomplete__results"><?=JText::_('COM_LASERCITY_ALL_RESULT_FIND')?> <output></output> <?=JText::_('COM_LASERCITY_ALL_RESULT_TXT')?></p>
          </div>
          <button class="filters__place-button" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_OPEN')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_OPEN')?>" data-set-modal-value="multiPopupPlace">
              <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_OPEN')?></span></button>
        </div>
      </form>
      <div class="filter-desktop">
        <div class="filter-desktop__zona" data-set-modal-value="multiPopupZona">
          <p class="filter-desktop__zona-title"><?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_ZONE_OPEN')?></p>
          <button class="filter-desktop__zona-button" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_ZONE_PROCEDURE')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_ZONE_PROCEDURE')?>">
            <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN')?></span>
            <svg width="13" height="6" aria-hidden="true">
              <path d="M1069.01,43l6.49,1.074L1081.99,43,1075.5,49Z" transform="translate(-1069 -43)"></path>
            </svg>
          </button>
        </div>
        <div class="filter-desktop__aparat" data-set-modal-value="multiPopupAparat">
          <p class="filter-desktop__aparat-title"><?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_APARAT_TXT')?></p>
          <button class="filter-desktop__aparat-button" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_APARAT_OPEN')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_APARAT_OPEN')?>">
            <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN')?></span>
            <svg width="13" height="6" aria-hidden="true">
              <path d="M1069.01,43l6.49,1.074L1081.99,43,1075.5,49Z" transform="translate(-1069 -43)"></path>
            </svg>
          </button>
        </div>
        <div class="filter-desktop__comfort" data-set-modal-value="multiPopupComfort">
          <p class="filter-desktop__comfort-title"><?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_KOMFORT_TXT')?></p>
          <button class="filter-desktop__comfort-button" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_KOMFORT_OPEN')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_FILTER_KOMFORT_OPEN')?>">
            <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN')?></span>
            <svg width="13" height="6" aria-hidden="true">
              <path d="M1069.01,43l6.49,1.074L1081.99,43,1075.5,49Z" transform="translate(-1069 -43)"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
    <div class="filters__type">
      <div class="filters__type-wrapper">
        <p class="filters__type-sorting">
          <button class="filters__sorting-button" type="button" title="<?=JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN')?>" data-set-modal-value="multiPopupPlace"><?=JText::_('COM_LASERCITY_ALL_SALON_LASER_FILTER_TXT')?></button>
          <output><?= $this->items['search_info']['count'] ?>
              <?=ContentLoader::replaceSuffix($this->items['search_info']['count'],'salon',true)?></output>
        </p>
        <div class="filters__type-sort">
          <b class="filters__sort-name"><span class="filters__sort-name--mob"><?=JText::_('COM_LASERCITY_ALL_SALON_LASER_SORT')?></span><span class="filters__sort-name--pc"><?=JText::_('COM_LASERCITY_ALL_SALON_LASER_SORT_TXT')?></span></b>
          <div class="filters__type-sort-wrapper">
            <ul class="filters__type-sort-list">
              <li class="filters__type-sort-item">
                  <?=JText::_('COM_LASERCITY_ALL_SALON_LASER_SORT_REITING')?>
                <a class="filters__type-sort-item-button button-corner" href="#" title="<?=JText::_('COM_LASERCITY_ALL_SALON_LASER_FILTER_REITING')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_LASER_FILTER_REITING')?>">
                  <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SALON_LASER_FILTER_REITING')?></span>
                </a>
              </li>
              <li class="filters__type-sort-item">
                  <?=JText::_('COM_LASERCITY_ALL_SALON_LASER_SORT_REVIEW')?>
                <a class="filters__type-sort-item-button button-corner" href="#" title="<?=JText::_('COM_LASERCITY_ALL_SALON_LASER_FILTER_REVIEW')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_LASER_FILTER_REVIEW')?>">
                  <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SALON_LASER_FILTER_REVIEW')?></span>
                </a>
              </li>
              <li class="filters__type-sort-item">
                  <?=JText::_('COM_LASERCITY_ALL_SALON_LASER_SORT_SEE')?>
                <a class="filters__type-sort-item-button button-corner" href="#" title="<?=JText::_('COM_LASERCITY_ALL_SALON_LASER_FILTER_SEE')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_LASER_FILTER_SEE')?>">
                  <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SALON_LASER_FILTER_SEE')?></span>
                </a>
              </li>
            </ul>
            <button class="filters__type-sort-button button-corner" title="<?=JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN')?></span></button>
          </div>
        </div>
        <button class="filters__map-button" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SALON_ON_MAP_OPEN')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_ON_MAP_OPEN')?>"><?=JText::_('COM_LASERCITY_ALL_SALON_ON_MAP_OPEN_TXT')?></button>
      </div>
    </div>
  </section>
  <nav class="breadcrumbs breadcrumbs--nopadding">
      <ul class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
          <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="<?= $current_sef.$city_alias?>"><span itemprop="name"><?= JText::_('COM_LASERCITY_ALL_GENERAL') ?></span></a>
              <meta itemprop="position" content="1"/>
          </li>
          <li class="breadcrumbs__item breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="#"><span itemprop="name"><?=JText::_('COM_LASERCITY_A_1_HEADER')?> <?= $city_title ?></span></a>
              <meta itemprop="position" content="2"/>
          </li>
      </ul>
  </nav>
  <section class="salons" aria-labelledby="SalonsIn<?= ucfirst($obj_city->alias) ?>">
    <h1 class="salons__title" id="SalonsIn<?= ucfirst($obj_city->alias) ?>"><?=JText::_('COM_LASERCITY_ALL_SALON_LASER_EPIL_ON')?> <?= $city_title ?></h1>
      <?php if (!empty($this->items['active_filters'])): ?>
        <div class="salons__choose">
          <div class="salons__choose-container">
            <div class="salons__choose-wrapper">
              <p class="salons__choose-title"><?=JText::_('COM_LASERCITY_ALL_SALON_ON_FILTER_REQUEST')?>:</p>
              <ul class="salons__choose-list">
                  <?php foreach ($this->items['active_filters'] as $active_filter): ?>
                    <li class="salons__choose-item"><?= $active_filter->title ?>
                      <a href='<?= $active_filter->link ?>' class="salons__choose-del button-cross" title="<?=JText::_('COM_LASERCITY_ALL_SALON_DEL_ELEM_ON_FILTER')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_DEL_ELEM_ON_FILTER')?>">
                        <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SALON_DEL_ELEM_ON_FILTER')?></span>
                      </a>
                    </li>
                  <?php endforeach; ?>
              </ul>
                <button class="salons__choose-reset-button" type="button"><?=JText::_('COM_LASERCITY_ALL_FORM_RESET_ALL')?></button>
            </div>
          </div>
          <p class="salons__choose-results"><?=JText::_('COM_LASERCITY_ALL_RESULT_FIND')?>:
            <output><?= $this->items['search_info']['count'] ?></output>
              <?=ContentLoader::replaceSuffix($this->items['search_info']['count'],'salon',true)?> <span class="salons__choose-results--pc"> в <?= $city_title ?></span></p>
        </div>
      <?php endif; ?>
    <div class="salons__colums">
      <div class="salons__wrapper">
        <ul class="salons_salon-list">
            <?php
            $affiliates = $this->items['affiliates'];
            $organizations = $this->items['affiliates'];
            ob_start();
            require JPATH_BASE . (isset($this->items['organizations']) ? '/templates/lasercity/crutch/clinics_draw_organizations.php' : '/templates/lasercity/crutch/clinics_draw_affiliates.php');
            $output = ob_get_contents();
            ob_end_clean();
            echo $output;
            ?>
        </ul>
          <?php if ($this->items['search_info']['page'] < $this->items['search_info']['pages']): ?>
            <p class="salons__show-all-wrapper">
              <button class="<?=isset($this->items['organizations']) ? 'organizations__show-all' : 'salons__show-all'?>" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SHOW_YET')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SHOW_YET')?>"><?=JText::_('COM_LASERCITY_ALL_SHOW_YET')?>
                <output class="salons__show-results"><?=
                    ($this->items['search_info']['page'] + 1) * $this->items['search_info']['config']['limit'] <= $this->items['search_info']['count'] ?
                        $this->items['search_info']['config']['limit'] :
                        $this->items['search_info']['count'] - ($this->items['search_info']['page']) * $this->items['search_info']['config']['limit']
                    ?></output>
                <?= isset($this->items['organizations']) ? JText::_('COM_LASERCITY_ALL_ORGANIZ_ON_YET') : JText::_('COM_LASERCITY_ALL_SALON_ON_YET')?>
              </button>
            </p>
          <?php endif; ?>
          <?php
          $start_pagination = $this->items['search_info']['page'] - 2 > 0 ? $this->items['search_info']['page'] - 2 : 1;
          $end_pagination = $this->items['search_info']['page'] + 2 <= $this->items['search_info']['pages'] ? $this->items['search_info']['page'] + 2 : $this->items['search_info']['pages'];
          ?>
        <nav class="salons__pagination pagination">
            <?php $blank_url_page = "{$current_sef}clinics/{$city_alias}/" . ($this->items['get_setter_string'] == null ? "?page=" : '?' . $this->items['get_setter_string'] . '&page='); ?>
            <?php if ($this->items['search_info']['page'] > 1): ?>
                <?php if($this->items['search_info']['page']==2): ?>
                    <a class="pagination__button pagination__button--prev button-corner" href='<?= $current_sef."clinics/{$city_alias}/".($this->items['get_setter_string'] == null ? "" : '?' . $this->items['get_setter_string']) ?>'><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?></span></a>
                <?php else: ?>
                    <a class="pagination__button pagination__button--prev button-corner" href='<?= $blank_url_page . ($this->items['search_info']['page'] - 1) ?>'><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?></span></a>
                <?php endif;?>
            <?php endif; ?>
          <ul class="pagination__page-list">
              <?php for ($i = $start_pagination; $i <= $end_pagination; $i++): ?>
                  <?php if($i==1): ?>
                      <li class="pagination__page-item <?= $i == $this->items['search_info']['page'] ? 'pagination__page-item--current' : '' ?>"><a <?php if ($i != $this->items['search_info']['page']): ?>href='<?= $current_sef."clinics/{$city_alias}/".($this->items['get_setter_string'] == null ? "" : '?' . $this->items['get_setter_string']) ?>'<?php endif; ?>><?= $i ?></a></li>
                  <?php else: ?>
                      <li class="pagination__page-item <?= $i == $this->items['search_info']['page'] ? 'pagination__page-item--current' : '' ?>"><a <?php if ($i != $this->items['search_info']['page']): ?>href='<?= $blank_url_page . $i ?>'<?php endif; ?>><?= $i ?></a></li>
                  <?php endif;?>
              <?php endfor; ?>
          </ul>
            <?php if ($this->items['search_info']['page'] < $this->items['search_info']['pages']): ?><a class="pagination__button pagination__button--next button-corner" href='<?= $blank_url_page . ($this->items['search_info']['page'] + 1) ?>'><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_NEXT')?></span></a><?php endif; ?>
        </nav>
        <div class="salons__mailing mailing">
          <b class="mailing__slogan"><?=JText::_('COM_LASERCITY_ALL_WRITING_RSS')?></b>
          <p class="mailing__subscription"><?=JText::_('COM_LASERCITY_ALL_SALON_ON_STATE_EMAIL')?></p>
          <form class="mailing__form" method="post">
            <p class="mailing__input-wrapper">
              <input class="mailing__input" type="text" autocomplete="off" name="mailing" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>">
            </p>
            <p class="mailing__rules"><?=JText::_('COM_LASERCITY_ALL_SALON_ON_PUSH_BTN')?> <br><a href="#"><?=JText::_('COM_LASERCITY_ALL_FORM_RULS_SITE')?></a> <?=JText::_('COM_LASERCITY_ALL_FORM_MY_SITE')?></p>
            <button class="mailing__button" title="<?=JText::_('COM_LASERCITY_ALL_WRITE_RSS')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_WRITE_RSS')?>"><span class="mailing__button--mob">Ок</span><span class="mailing__button--pc"><?=JText::_('COM_LASERCITY_ALL_SALON_ON_GOT_LETTER')?></span></button>
          </form>
          <div class="mailing__followers">
              <?php
              $total_subscription = ContentLoader::getSubscriptionTotal();
              ?>
              <output><?=$total_subscription?></output> <br>
            <p class="mailing__followers-accept"><?=ContentLoader::replaceSuffix($total_subscription,'people',true)?> <?=JText::_('COM_LASERCITY_ALL_SALON_ON_GOT_MESSAGE')?></p>
          </div>
        </div>
      </div>
      <div class="salons__aside-information">
        <div class="salons__onmap salon-map">
          <div class="salon-map__title-wrapper">
            <h3 class="salon-map__title"><?=JText::_('COM_LASERCITY_H2_5_HOME')?></h3>
            <a class="salon-map__button" href="#" target="_blank" rel="noreferrer noopener" title="<?=JText::_('COM_LASERCITY_ALL_SALON_ON_MAP_OPEN')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_ON_MAP_OPEN')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SALON_ON_MAP_OPEN')?></span></a>
            <button class="salon-map__button-close" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SALON_ON_CLOSE_MAP')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_ON_CLOSE_MAP')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SALON_ON_CLOSE_MAP')?></span></button>
          </div>
          <div class="salon-map__maps">
            <iframe class="salon-map__map-iframe" title="<?=JText::_('COM_LASERCITY_DIV_1_HOME')?>" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5083.339992641428!2d30.60359835124302!3d50.42861983394887!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40d4c57b4a855177%3A0xc055cd0c36be6d70!2z0J_QsNC8J9GP0YLQvdCwINC00L7RiNC60LAg0L3QsNGA0L7QtNC90ZbQuSDQsNGA0YLQuNGB0YLRhtGWINCj0LrRgNCw0ZfQvdC4INCc0LDRgNCz0LDRgNC40YLRliDQmtGA0LjQvdC40YbQuNC90ZbQuQ!5e0!3m2!1suk!2sua!4v1549883725531" allowfullscreen></iframe>
            <p class="salon-map__map-img-wrapper">
              <img class="salon-map__map-img" src="https://placehold.it/260x232" title="<?=JText::_('COM_LASERCITY_DIV_1_HOME')?>" alt="<?=JText::_('COM_LASERCITY_DIV_1_HOME')?>">
            </p>
          </div>
        </div>
        <div class="salons__aside-progress">
          <b class="salons__aside-progress__title"><?=JText::_('COM_LASERCITY_ALL_SALON_ON_LASERCITY')?>:</b>
          <ul class="salons__aside-progress-list">
            <li class="salons__aside-progress-item salons__aside-progress-item--visitors">
              <output><?= $this->usersCount ?></output> <br><?=ContentLoader::replaceSuffix($this->usersCount,'user')?><br> <?=JText::_('COM_LASERCITY_ALL_SALON_ON_THE_MONTH')?>
            </li>
            <li class="salons__aside-progress-item salons__aside-progress-item--nodes">
                <?php
                    $total_service = ContentLoader::getWriteServiceTotal('day');
                ?>
              <output><?=$total_service?></output> <br><?=ContentLoader::replaceSuffix($total_service,'write')?> <?=JText::_('COM_LASERCITY_ALL_SALON_ON_THE_DAY')?>
            </li>
            <li class="salons__aside-progress-item salons__aside-progress-item--customers">
                <?php
                    $total_review = ContentLoader::getReviewTotal();
                ?>
              <output><?=$total_review?></output> <br><?=ContentLoader::replaceSuffix($total_review,'review')?> <?=JText::_('COM_LASERCITY_BUTTON_9_HOME')?>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</main>
<style>
    .error {
        border: 1px solid #d4124c;
    }

    .text-danger {
        color: #d4124c;
    }

    .text-success {
        color: #09dd7f;
    }

    .status {
        display: none;
        color: #000;
        padding: 15px;
        background: #09f28c;
        border-radius: 3px;
        margin-top: 10px;
        text-align: center;
        width: 100%;
        float: left;
        clear: both;
    }

    .organizations__show-all {
        position: relative;
        padding-right: 40px;
        font-size: 18px;
        color: #333;
        background-color: transparent;
        border: 0;
        overflow: hidden;
    }
    .organizations__show-all::before {
        position: absolute;
        top: 0;
        right: 3px;
        content: '';
        width: 15px;
        height: 15px;
        border-right: 3px solid #333;
        border-bottom: 3px solid #333;
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);

</style>