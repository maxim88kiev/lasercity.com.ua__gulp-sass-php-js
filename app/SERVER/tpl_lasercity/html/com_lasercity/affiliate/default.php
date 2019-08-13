<?php defined('_JEXEC') or die;

ContentLoader::addScript('salon');

ContentLoader::addPopup('clinic-popup');
ContentLoader::addPopup('popup-question');
ContentLoader::addPopup('popup-recording');
ContentLoader::addPopup('gallery');

$urNoHTTPS = str_replace('https://', '//', JUri::base());

$location = array();
$types_to_location = array('location', 'number_home', 'district', 'micro_district');

foreach ($types_to_location as $type) {
    if ($this->item->$type !== null) {
        $location[] = $this->item->$type;
    }
}

//$sef = LangHelper::getCurrentSef();
$city_list = CityHelper::getList();
$city_alias = CityHelper::getCurrent()->alias;
$city_title = null;

foreach ($city_list as $item) {
    if ($item->alias == $city_alias) {
        $city_title = $item->title;
        break;
    }
}

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

/*echo '<pre>';
print_r($this->item->prices);
echo '</pre>';*/

//echo var_dump($this->item);
?>

  <main>
    <nav class="salon-navigation">
      <a class="salon-navigation__button-back button-back" href="#back" onclick="history.go(-1)"><?= JText::_('COM_LASERCITY_ALL_SALON_LASER_GO_TO_BACK') ?></a>
      <div class="salon-navigation__breadcrumbs breadcrumbs">
        <ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
          <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="<?= $current_sef ?>"><span itemprop="name"><?= JText::_('COM_LASERCITY_ALL_GENERAL') ?></span></a>
            <meta itemprop="position" content="1"/>
          </li>
          <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="<?= "{$current_sef}clinics/{$city_alias}" ?>/"><span itemprop="name"><?= JText::_('COM_LASERCITY_A_1_HEADER') ?></span> <?= $city_title ?></a>
            <meta itemprop="position" content="2"/>
          </li>
          <li class="breadcrumbs__item breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="#"><span itemprop="name"><?= $this->item->title ?></span></a>
            <meta itemprop="position" content="3"/>
          </li>
        </ol>
      </div>
    </nav>

    <section aria-labelledby="microData" itemscope itemtype="https://schema.org/Product">

      <section class="salon" aria-labelledby="SalonSection">
        <h1 class="salon__title" id="SalonSection"><?= $this->item->title ?></h1>
          <?php if (!empty($this->item->images)): ?>
            <div class="salon__slider slider">
              <ul class="slider__list">
                  <?php foreach ($this->item->images as $image):
                      $image = '/' . deleteFormat($image);
                      ?>
                    <li class="slider__item">
                      <div class="slider__item-wrapper">
                        <picture>
                          <source data-srcset="<?= $image ?>_262x198.webp 1x, <?= $image ?>_524x396.webp 2x" type="image/webp">
                          <img data-original-name="<?= $image ?>" class="salons__img lazyload" data-src="<?= $image ?>_262x198.jpg" data-srcset="<?= $image ?>_524x396.jpg 2x" loading="lazy" title="<?= JText::_('COM_LASERCITY_IMG_TITLE_1_HOME') ?> <?= $this->item->title ?>" alt="<?= JText::_('COM_LASERCITY_IMG_TITLE_1_HOME') ?> <?= $this->item->title ?>">
                        </picture>
                      </div>
                    </li>
                  <?php endforeach; ?>
              </ul>
                <?php if (count($this->item->images) > 4): ?>
                  <button class="slider__button slider__button--prev" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV') ?></span>
                    <svg width="13" height="69" viewBox="0 0 20 103">
                      <path d="M1547.99,1052.01l16.24-51.51-16.24-51.515h3.78l16.24,51.515-16.24,51.51h-3.78Z" transform="translate(-1548 -949)"></path>
                    </svg>
                  </button>
                  <button class="slider__button slider__button--next" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT') ?></span>
                    <svg width="13" height="69" viewBox="0 0 20 103">
                      <path d="M1547.99,1052.01l16.24-51.51-16.24-51.515h3.78l16.24,51.515-16.24,51.51h-3.78Z" transform="translate(-1548 -949)"></path>
                    </svg>
                  </button>
                <?php endif; ?>
            </div>
          <?php endif; ?>
        <div class="salon__wrapper">
          <div class="salon__description">
            <div class="salon__information-wrapper">
              <p class="salon__text"><?= $this->item->organization ? $this->item->type_organization : $this->item->type ?></p>
              <a class="salon__rating rating-salon" href="<?= $current_sef . "clinics/{$this->item->alias}/#SalonRation" ?>">
                <div class="rating-salon__point-wrapper">
                  <output class="rating-salon__point">
                      <?php
                      $raiting_array = ContentLoader::getRaiting($this->item->id);
                      echo $raiting_array['raiting'];
                      ?>
                  </output>
                  <ul class="rating-salon-stars">
                      <?php for ($r = 0; $r < 5; $r++): ?>
                          <?php if ($r < floor($raiting_array['raiting'])): ?>
                          <li class="rating-salon__star rating-salon__star--silver">
                            <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207" aria-hidden="true">
                              <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
                            </svg>
                            <span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_RAITING_GOOD') ?></span>
                          </li>
                          <?php else: ?>
                          <li class="rating-salon__star rating-salon__star--transparent">
                            <svg fill="#cacaca" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 487.222 487.222" aria-hidden="true">
                              <g>
                                <path d="M486.554,186.811c-1.6-4.9-5.8-8.4-10.9-9.2l-152-21.6l-68.4-137.5c-2.3-4.6-7-7.5-12.1-7.5l0,0c-5.1,0-9.8,2.9-12.1,7.6 l-67.5,137.9l-152,22.6c-5.1,0.8-9.3,4.3-10.9,9.2s-0.2,10.3,3.5,13.8l110.3,106.9l-25.5,151.4c-0.9,5.1,1.2,10.2,5.4,13.2 c2.3,1.7,5.1,2.6,7.9,2.6c2.2,0,4.3-0.5,6.3-1.6l135.7-71.9l136.1,71.1c2,1,4.1,1.5,6.2,1.5l0,0c7.4,0,13.5-6.1,13.5-13.5 c0-1.1-0.1-2.1-0.4-3.1l-26.3-150.5l109.6-107.5C486.854,197.111,488.154,191.711,486.554,186.811z M349.554,293.911 c-3.2,3.1-4.6,7.6-3.8,12l22.9,131.3l-118.2-61.7c-3.9-2.1-8.6-2-12.6,0l-117.8,62.4l22.1-131.5c0.7-4.4-0.7-8.8-3.9-11.9 l-95.6-92.8l131.9-19.6c4.4-0.7,8.2-3.4,10.1-7.4l58.6-119.7l59.4,119.4c2,4,5.8,6.7,10.2,7.4l132,18.8L349.554,293.911z"></path>
                              </g>
                            </svg>
                            <span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_RAITING_NO_GOOD') ?></span>
                          </li>
                          <?php endif; ?>
                      <?php endfor; ?>
                  </ul>
                </div>
                <p class="rating-salon__reviews">
                  <output><?= $raiting_array['count_raiting'] ?></output> <?= ContentLoader::replaceSuffix($raiting_array['count_raiting'], 'review') ?>
                </p>
              </a>
            </div>
            <div class="salon__affiliate">
              <div class="salon__address salon-address">
                  <?php if ($this->item->metro != null): ?>
                    <p class="salon-address__underground">
                      <svg fill="<?= $this->item->metro_line ?>" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="337.5 232.3 125 85.9" aria-hidden="true">
                        <polygon points="453.9,306.2 424.7,232.3 400,275.5 375.4,232.3 346.1,306.2 337.5,306.2 337.5,317.4 381.7,317.4 381.7,306.2 375.1,306.2 381.5,287.8 400,318.2 418.5,287.8 424.9,306.2 418.3,306.2 418.3,317.4 462.5,317.4 462.5,306.2 "></polygon>
                      </svg>
                        <?= $this->item->metro ?>
                    </p>
                  <?php endif; ?>
                <p class="salon-address__street"><?= implode(', ', $location) ?></p>
              </div>
              <a class="salon__affiliate-map button-onmap" href="https://maps.google.com/?q=<?= $this->item->coordinate ?>" target="_blank" rel="noreferrer noopener" title="<?= JText::_('COM_LASERCITY_ALL_OPEN_MAP') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_OPEN_MAP') ?>"><?= JText::_('COM_LASERCITY_ALL_TXT_MAP_GOOGLE') ?></a>
            </div>
              <?php if (count($this->item->apparatus) || count($this->item->comforts)): ?>
                <div class="salon__apartments">
                    <?php if (count($this->item->apparatus)): ?>
                      <p class="salon__apartments-description"><?= JText::_('COM_LASERCITY_SPAN_4_HOME') ?>: <?= implode(', ', $this->item->apparatus) ?></p>
                    <?php endif; ?>
                    <?php if (count($this->item->comforts)): ?>
                      <ul class="salon__services-list services-list">
                          <?php foreach ($this->item->comforts as $comfort): ?>
                            <li class="services-list-item services-list-item--<?= $comfort->icon ?>" title="<?= $comfort->title ?>"><span><?= $comfort->title ?></span></li>
                          <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                </div>
              <?php endif; ?>
              <?php if (isset($this->item->description)): ?>
                <p class="salon__advice-description" itemprop="description"><?= strip_tags($this->item->description) ?></p>
              <?php endif; ?>
          </div>
          <div class="salon__contact">
            <p class="salon__contact-photo-wrapper">
              <picture>
                  <?php
                  $image = 'https://placehold.it/';
                  if ($this->item->organization == 0) {
                      if (mb_strlen($this->item->logo)) {
                          $image = '/' . $this->item->logo;
                      }
                  } else {
                      if (mb_strlen($this->item->main_image)) {
                          $image = '/' . $this->item->main_image;
                      }
                  }
                  $logo_micro = $image;
                  $image = deleteFormat($image);
                  ?>
                <source data-srcset="<?= $image ?>_262x198.webp 1x, <?= $image ?>_524x396.webp 2x" type="image/webp">
                <img itemprop="image" class="salon__contact-photo lazyload" src="<?= $image ?>_262x198.jpg" data-srcset="<?= $image ?>_524x396.jpg 2x" loading="lazy" title="<?= JText::_('COM_LASERCITY_IMG_TITLE_1_HOME') ?> <?= $this->item->title ?>" alt="<?= JText::_('COM_LASERCITY_IMG_TITLE_1_HOME') ?> <?= $this->item->title ?>">
              </picture>
            </p>
            <div class="salon__phones phonebook">
              <div class="phonebook__wrapper">
                <ul class="phonebook__list">
                    <?php foreach ($this->item->phones as $phone): ?>
                      <li class="phonebook__item">
                        <a><?= $phone ?></a>
                      </li>
                    <?php endforeach; ?>
                </ul>
                  <?php if (count($this->item->phones) > 1): ?>
                    <button class="phonebook__button button-corner" type="button" title="<?= JText::_('COM_LASERCITY_AFFILIALES_SEE_PHONES') ?>" aria-label="<?= JText::_('COM_LASERCITY_AFFILIALES_SEE_PHONES') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_AFFILIALES_SEE_PHONES') ?></span></button>
                  <?php endif; ?>
                <div class="phonebook__popup">
                  <p class="phonebook__popup-text"><?= JText::_('COM_LASERCITY_STOCKS_SVIASI') ?></p>
                  <a class="phonebook__popup-link" href="#"><?= JText::_('COM_LASERCITY_STOCKS_RAITING') ?></a>
                  <ul class="phonebook__list">
                      <?php foreach ($this->item->phones as $phone): ?>
                        <li class="phonebook__popup-item">
                          <a href="tel:<?= $phone ?>"><?= $phone ?></a>
                          <button class="phonebook__popup-show-number" data-affiliate="<?= $this->item->id ?>"><?= JText::_('COM_LASERCITY_ALL_SHOW_ME') ?></button>
                        </li>
                      <?php endforeach; ?>
                  </ul>
                  <button class="phonebook__popup-close button-cross" type="button" title="<?= JText::_('COM_LASERCITY_ALL_CLOSE_PHONE') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_CLOSE_PHONE') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_CLOSE_PHONE') ?></span></button>
                </div>
              </div>
            </div>
            <div class="salon__workdays workdays">
              <button class="workdays__button-open" type="button" title="<?= JText::_('COM_LASERCITY_ALL_OPEN_GRAPHIK') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_OPEN_GRAPHIK') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_OPEN_GRAPHIK') ?></span></button>
              <p class="workdays__title"><?= JText::_('COM_LASERCITY_ALL_GRAPHIK_JOB') ?></p>
              <div class="workdays__list-wrapper">
                <ul class="workdays__list">
                    <?php foreach ($this->item->schedule as $schedule): ?>
                      <li class="workday__item">
                          <?= $schedule['days'] ?>: <span><?= $schedule['times'] ?></span>
                      </li>
                    <?php endforeach; ?>
                </ul>
                <button class="workday__button-close button-cross"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_CLOSE_GRAPHIK_JOB') ?></span></button>
              </div>
            </div>
            <button class="salon__contact-question" data-set-modal-value="question" data-affiliate="<?= $this->item->id ?>"><?= JText::_('COM_LASERCITY_ALL_TXT_QUESTION') ?></button>
            <button class="salon__contact-sing button" data-set-modal-value="recording" data-affiliate="<?= $this->item->id ?>"><?= JText::_('COM_LASERCITY_ALL_TXT_WRITERING') ?></button>
              <?php if (count($this->item->social_networks)) : ?>
                <ul class="salon__social-list">
                    <?php foreach ($this->item->social_networks as $social_network): ?>
                      <li class="salon__social-item">
                        <a class="salon__social-link salon__social-link--<?= $social_network['modification'] ?>" target="_blank" rel="noreferrer noopener" href="<?= $social_network['link'] ?>">
                            <?= $social_network['svg'] ?>
                          <span class="visually-hidden"><?= $social_network['text'] ?></span>
                        </a>
                      </li>
                    <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            <a class="salon__contact-reach" href="<?= $this->item->site ?>" rel="nofollow" target="_blank"><?= JText::_('COM_LASERCITY_ALL_LINK_TO_SITE') ?></a>
            <button class="salon__contact-owner"><?= JText::_('COM_LASERCITY_ALL_YOU_VLADELEC_ORG') ?></button>
          </div>
          <div class="salon__information">
            <div class="salon__onmap salon-map">
              <div class="salon-map__title-wrapper">
                <h3 class="salon-map__title"><?= JText::_('COM_LASERCITY_ALL_SALON_ON_MAP') ?></h3>
                <a class="salon-map__button" href="https://maps.google.com/?q=<?= $this->item->coordinate ?>" target="_blank" rel="noreferrer noopener" title="<?= JText::_('COM_LASERCITY_ALL_SALON_ON_MAP_GO') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SALON_ON_MAP_GO') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_SALON_ON_MAP_OPEN') ?></span></a>
              </div>
              <div class="salon-map__maps">
                <iframe class="salon-map__map-iframe" title="<?= JText::_('COM_LASERCITY_ALL_SALON_ADDRESS') ?>: <?= implode(', ', $location) ?>" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5083.339992641428!2d30.60359835124302!3d50.42861983394887!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40d4c57b4a855177%3A0xc055cd0c36be6d70!2z0J_QsNC8J9GP0YLQvdCwINC00L7RiNC60LAg0L3QsNGA0L7QtNC90ZbQuSDQsNGA0YLQuNGB0YLRhtGWINCj0LrRgNCw0ZfQvdC4INCc0LDRgNCz0LDRgNC40YLRliDQmtGA0LjQvdC40YbQuNC90ZbQuQ!5e0!3m2!1suk!2sua!4v1549883725531" allowfullscreen></iframe>
                <p class="salon-map__map-img-wrapper">
                  <img class="salon-map__map-img" src="https://placehold.it/260x232" title="<?= JText::_('COM_LASERCITY_ALL_SALON_ADDRESS') ?>: <?= implode(', ', $location) ?>" alt="<?= JText::_('COM_LASERCITY_ALL_SALON_ADDRESS') ?>: <?= implode(', ', $location) ?>">
                </p>
              </div>
            </div>
            <div class="salon__information-report">
              <ul class="salon__report-list">
                <li class="salon__report-item salon__report-item--view">
                  <output><?= $this->countView ?></output>
                </li>
                <li class="salon__report-item"><?= JText::_('COM_LASERCITY_ALL_SALON_APPLICATIONS') ?>:
                  <output><?= ContentLoader::getWriteStockTotal('affiliate', $this->item->id) ?></output>
                </li>
                <li class="salon__report-item"><?= JText::_('COM_LASERCITY_ALL_SALON_TELEPHONES') ?>:
                  <output><?= $this->item->phone_click ?></output>
                </li>
              </ul>
              <p class="salon__report-text"><?= JText::_('COM_LASERCITY_ALL_SALON_VOID_ACCESESS') ?></p>
              <button class="salon__report-owner"><?= JText::_('COM_LASERCITY_ALL_SALON_YOU_PARTMENT') ?></button>
              <button class="salon__report-error"><?= JText::_('COM_LASERCITY_ALL_SALON_MESS_ERROR') ?></button>
            </div>
          </div>
        </div>
      </section>
        <?php if (count($this->item->prices)): ?>
            <div class="salon-carousel">
            <p class="salon-carousel__buttons">
                <button data-toggle="price" class="salon-carousel__button salon-carousel__button--price salon-carousel__button--active"><span><?= JText::_('COM_LASERCITY_ALL_SALON_MESS_PRICE') ?><output><?= $this->item->all_prices ?></output></span></button>
                <button data-toggle="review" class="salon-carousel__button salon-carousel__button--review"><span><?= JText::_('COM_LASERCITY_ALL_SALON_MESS_REVIEW') ?><output><?= $raiting_array['count_raiting'] ?></output></span></button>
            </p>
          <section class="salon-price salon-price salon-carousel__section salon-carousel__section--price salon-carousel__section--active" aria-labelledby="PriceOfSalon">
            <h3 class="visually-hidden" id="PriceOfSalon"><?= JText::_('COM_LASERCITY_ALL_SALON_MESS_PRICES') ?></h3>
            
                <div class="salon-price__offer-for">
                    <b class="salon-price__offer-for-title"><?= JText::_('COM_LASERCITY_ALL_SALON_SHOW_PRICE_FOR_ALL') ?></b>
                    <p class="salon-price__for-buttons">
                        <button class="salon-price__for-button salon-price__for-button--women salon-price__for-button--active" data-toggle="women" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SALON_SHOW_PRICE_FOR_WONEN') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SALON_SHOW_PRICE_FOR_WONEN') ?>"><span class="women"><?= JText::_('COM_LASERCITY_ALL_SALON_SHOW_FOR_WONEN') ?></span></button>
                        <button class="salon-price__for-button salon-price__for-button--men" data-toggle="men" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SALON_SHOW_PRICE_FOR_NEN') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SALON_SHOW_PRICE_FOR_NEN') ?>"><span class="men"><?= JText::_('COM_LASERCITY_ALL_SALON_SHOW_FOR_NEN') ?></span></button>
                    </p>
                </div>
              <ul class="salon-price__offer-list" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                  <?php

                  /*echo '<pre>';
                  print_r($this->item->prices);
                  echo '</pre>';*/

                  foreach ($this->item->prices as $apparatus => $zones): ?>
                    <li class="salon-price__offer-item">
                      <div class="salon-price__offer-header">
                        <h4 class="salon-price__offer-title"><?= JText::_('COM_LASERCITY_ALL_SALON_PRICE_ON_APPARAT') ?> <?= $apparatus ?></h4>
                        <button class="salon-price__offer-button button-corner" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SALON_PRICE_SHOW_ON_APPARAT') ?> <?= $apparatus ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SALON_PRICE_SHOW_ON_APPARAT') ?> <?= $apparatus ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_SALON_SHOW_SPISOK') ?></span></button>
                      </div>
                      <ul class="salon-price__offer-zona-list">
                          <?php foreach ($zones as $zone => $sex_price): ?>
                            <li class="salon-price__offer-zona-item">
                              <div class="salon-price__offer-zona-header">
                                <h5 class="salon-price__offer-zona-title"><?= $zone ?></h5>
                                <button class="salon-price__offer-zona-button button-corner" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SALON_PRICE_SHOW_ON_APPARAT_1') ?> <?= $apparatus ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SALON_PRICE_SHOW_ON_APPARAT_1') ?> <?= $apparatus ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_SALON_SHOW_SPISOK') ?></span></button>
                              </div>
                              <table class="salon-price__offer-table salon-prices salon-price__offer-table--women">
                                <thead class="salon-prices__table-thead">
                                <tr class="salon-prices__thead-row">
                                  <td class="salon-prices__row-part"><?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_ZONE_OPEN') ?></td>
                                  <td class="salon-prices__row-time"><?= JText::_('COM_LASERCITY_ALL_SALON_DURATION') ?></td>
                                  <td class="salon-prices__row-price"><?= JText::_('COM_LASERCITY_ALL_SALON_HOW_MATCH') ?> <?= $apparatus ?></td>
                                  <td class="salon-prices__order"></td>
                                </tr>
                                </thead>
                                  <?php
                                  $i = 0;
                                  foreach ($sex_price as $sex => $price): ?>
                                    <tbody class="salon-prices__table-tbody salon-prices__table-tbody--<?= $sex ?>">
                                    <?php foreach ($price as $service): ?>
                                      <tr class="salon-prices__tbody-row">
                                        <td class="salon-prices__row-part"><label><input type="checkbox"><?= $service['title'] ?></label></td>
                                        <td class="salon-prices__row-time"><?= $service['duration'] ?></td>
                                        <td class="salon-prices__row-cost">
                                            <?php
                                              echo '<span itemprop="price" content="' . explode(" ", $service['price'])[0] . '">' . explode(" ", $service['price'])[0] . ' </span>';
                                              echo '<span itemprop="priceCurrency" content="' . explode(" ", $service['price'])[1] . '">' . explode(" ", $service['price'])[1] . '</span>';
                                            ?>
                                          <link itemprop="availability" href="http://schema.org/InStock"/>
                                        </td>
                                        <td class="salon-prices__order">
                                            <button data-duration="<?= $service['duration'] ?>" data-prices_id="<?= $service['prices_id'] ?>" data-aparat_id="<?= $service['aparat_id'] ?>" data-service_id="<?= $service['service_id'] ?>" data-sex="<?= $sex ?>" data-aparat="<?= $apparatus ?>" data-name="<?= $service['title'] ?>" data-price="<?=explode(" ", $service['price'])[0]?>" data-affiliate="<?= $this->item->id ?>" class="salon-prices__order-button" data-set-modal-value="recording" type="button" title="<?= JText::_('COM_LASERCITY_ALL_TXT_WRITERING') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_TXT_WRITERING') ?>">
                                                <span><?= JText::_('COM_LASERCITY_ALL_TXT_WRITERING') ?></span>
                                            </button>
                                        </td>
                                      </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                      <?php $i++; endforeach; ?>
                              </table>
                            </li>
                          <?php endforeach; ?>
                      </ul>
                    </li>
                  <?php endforeach; ?>
              </ul>

          </section>
        <?php endif; ?>

        <div class="salon-carousel__section salon-carousel__section--rating">
      <section class="stock-salon <?= (!empty($raiting_array['raiting'])) ? '' : 'stock-salon--nocomments' ?>" aria-labelledby="SalonRation">
        <div class="stock-salon__point">
          <h3 class="stock-salon__title" id="SalonRation"><?= JText::_('COM_LASERCITY_ALL_SALON_TXT_H3') ?></h3>
          <div class="stock-salon__rating rating-salon">
            <div class="rating-salon__point-wrapper">
              <output class="rating-salon__point">
                  <?php
                  $raiting_array = ContentLoader::getRaiting($this->item->id);
                  echo $raiting_array['raiting'];
                  ?>
              </output>
              <ul class="rating-salon-stars">
                  <?php for ($r = 0; $r < 5; $r++): ?>
                      <?php if ($r < floor($raiting_array['raiting'])): ?>
                      <li class="rating-salon__star rating-salon__star--silver">
                        <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207" aria-hidden="true">
                          <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
                        </svg>
                        <span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_RAITING_GOOD') ?></span>
                      </li>
                      <?php else: ?>
                      <li class="rating-salon__star rating-salon__star--transparent">
                        <svg fill="#cacaca" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 487.222 487.222" aria-hidden="true">
                          <g>
                            <path d="M486.554,186.811c-1.6-4.9-5.8-8.4-10.9-9.2l-152-21.6l-68.4-137.5c-2.3-4.6-7-7.5-12.1-7.5l0,0c-5.1,0-9.8,2.9-12.1,7.6 l-67.5,137.9l-152,22.6c-5.1,0.8-9.3,4.3-10.9,9.2s-0.2,10.3,3.5,13.8l110.3,106.9l-25.5,151.4c-0.9,5.1,1.2,10.2,5.4,13.2 c2.3,1.7,5.1,2.6,7.9,2.6c2.2,0,4.3-0.5,6.3-1.6l135.7-71.9l136.1,71.1c2,1,4.1,1.5,6.2,1.5l0,0c7.4,0,13.5-6.1,13.5-13.5 c0-1.1-0.1-2.1-0.4-3.1l-26.3-150.5l109.6-107.5C486.854,197.111,488.154,191.711,486.554,186.811z M349.554,293.911 c-3.2,3.1-4.6,7.6-3.8,12l22.9,131.3l-118.2-61.7c-3.9-2.1-8.6-2-12.6,0l-117.8,62.4l22.1-131.5c0.7-4.4-0.7-8.8-3.9-11.9 l-95.6-92.8l131.9-19.6c4.4-0.7,8.2-3.4,10.1-7.4l58.6-119.7l59.4,119.4c2,4,5.8,6.7,10.2,7.4l132,18.8L349.554,293.911z"></path>
                          </g>
                        </svg>
                        <span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_RAITING_NO_GOOD') ?></span>
                      </li>
                      <?php endif; ?>
                  <?php endfor; ?>
              </ul>
            </div>
            <p class="rating-salon__reviews">
              <output><?= JText::_('COM_LASERCITY_ALL_SALON_OSNOVE_REVIEW') ?> <?= $raiting_array['count_raiting'] ?></output> <?= ContentLoader::replaceSuffix($raiting_array['count_raiting'], 'review') ?>
            </p>
          </div>
          <button class="stock-salon__button button" data-set-modal-value="addcomment"><?= JText::_('COM_LASERCITY_ALL_SALON_PUSH_REVIEW') ?></button>
        </div>
        <div class="stock-salon__allreview">
          <h4 class="stock-salon__allreview-title"><?= JText::_('COM_LASERCITY_ALL_SALON_ALL_RATINGS') ?></h4>
          <table class="stock-salon__allreview-table-review">
            <tbody class="stock-salon__table-review-tbody">
            <tr class="stock-salon__tbody-row">
              <td class="stock-salon__row-star">5 <?= ContentLoader::replaceSuffix(5, 'star', true) ?></td>
              <td class="stock-salon__row-review-gradient">
                <div><span class="visually-hidden">Отлично</span></div>
              </td>
              <td class="stock-salon__row-percent"><?= $raiting_array['percent'][5] ?>%
                <output>(<?= $raiting_array['stars'][5] ?>)</output>
              </td>
            </tr>
            <tr class="stock-salon__tbody-row">
              <td class="stock-salon__row-star">4 <?= ContentLoader::replaceSuffix(4, 'star', true) ?></td>
              <td class="stock-salon__row-review-gradient">
                <div><span class="visually-hidden">Хорошо</span></div>
              </td>
              <td class="stock-salon__row-percent"><?= $raiting_array['percent'][4] ?>%
                <output>(<?= $raiting_array['stars'][4] ?>)</output>
              </td>
            </tr>
            <tr class="stock-salon__tbody-row">
              <td class="stock-salon__row-star">3 <?= ContentLoader::replaceSuffix(3, 'star', true) ?></td>
              <td class="stock-salon__row-review-gradient">
                <div><span class="visually-hidden">Нормально</span></div>
              </td>
              <td class="stock-salon__row-percent"><?= $raiting_array['percent'][3] ?>%
                <output>(<?= $raiting_array['stars'][3] ?>)</output>
              </td>
            </tr>
            <tr class="stock-salon__tbody-row">
              <td class="stock-salon__row-star">2 <?= ContentLoader::replaceSuffix(2, 'star', true) ?></td>
              <td class="stock-salon__row-review-gradient">
                <div><span class="visually-hidden">Сносно</span></div>
              </td>
              <td class="stock-salon__row-percent"><?= $raiting_array['percent'][2] ?>%
                <output>(<?= $raiting_array['stars'][2] ?>)</output>
              </td>
            </tr>
            <tr class="stock-salon__tbody-row">
              <td class="stock-salon__row-star">1 <?= ContentLoader::replaceSuffix(1, 'star', true) ?></td>
              <td class="stock-salon__row-review-gradient">
                <div><span class="visually-hidden">Ужасно</span></div>
              </td>
              <td class="stock-salon__row-percent"><?= $raiting_array['percent'][1] ?>%
                <output>(<?= $raiting_array['stars'][1] ?>)</output>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="stock-salon__criteria">
          <h4 class="stock-salon__criteria-title"><?= JText::_('COM_LASERCITY_ALL_SALON_ON_CRITERIERS') ?></h4>
          <table class="stock-salon__criteria-table">
            <tbody class="stock-salon__table-tbody">
            <tr class="stock-salon__tbody-row">
              <td class="stock-salon__row-service"><?= JText::_('COM_LASERCITY_FORM_REVIEW_H4_1') ?></td>
              <td class="stock-salon__row-criteria-gradient">
                <div></div>
              </td>
              <td class="stock-salon__row-quantity"><?= $raiting_array['avg']['place'] ?>
                <output>(<?= $raiting_array['criterion']['place'] ?>)</output>
              </td>
            </tr>
            <tr class="stock-salon__tbody-row">
              <td class="stock-salon__row-service"><?= JText::_('COM_LASERCITY_FORM_REVIEW_H4_2') ?></td>
              <td class="stock-salon__row-criteria-gradient">
                <div></div>
              </td>
              <td class="stock-salon__row-quantity"><?= $raiting_array['avg']['relationship'] ?>
                <output>(<?= $raiting_array['criterion']['relationship'] ?>)</output>
              </td>
            </tr>
            <tr class="stock-salon__tbody-row">
              <td class="stock-salon__row-service"><?= JText::_('COM_LASERCITY_FORM_REVIEW_H4_3') ?></td>
              <td class="stock-salon__row-criteria-gradient">
                <div></div>
              </td>
              <td class="stock-salon__row-quantity"><?= $raiting_array['avg']['purity'] ?>
                <output>(<?= $raiting_array['criterion']['purity'] ?>)</output>
              </td>
            </tr>
            <tr class="stock-salon__tbody-row">
              <td class="stock-salon__row-service"><?= JText::_('COM_LASERCITY_FORM_REVIEW_H4_4') ?></td>
              <td class="stock-salon__row-criteria-gradient">
                <div></div>
              </td>
              <td class="stock-salon__row-quantity"><?= $raiting_array['avg']['quality'] ?>
                <output>(<?= $raiting_array['criterion']['quality'] ?>)</output>
              </td>
            </tr>
            <tr class="stock-salon__tbody-row">
              <td class="stock-salon__row-service"><?= JText::_('COM_LASERCITY_FORM_REVIEW_H4_5') ?></td>
              <td class="stock-salon__row-criteria-gradient">
                <div></div>
              </td>
              <td class="stock-salon__row-quantity"><?= $raiting_array['avg']['price'] ?>
                <output>(<?= $raiting_array['criterion']['price'] ?>)</output>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </section>

        <?php
        $language = JFactory::$language->getTag();
        $db = JFactory::getDbo();
        $db->setQuery("SELECT r.id,r.name,r.avatar,r.foto,r.koment,r.date_added,r.date_visit,r.place,r.relationship,r.purity,r.quality,r.price,r.confirm,r.rank,r.likes,
                                    r.user_id,t.value,  
                                    
                                    (SELECT COUNT(*) FROM `#__lasercity_reviews` WHERE user_id=r.user_id AND r.user_id!=0) as count_review,
                                    (SELECT COUNT(*) FROM `#__lasercity_question` WHERE user_id=r.user_id AND r.user_id!=0) as count_question,
                                    (SELECT COUNT(*) FROM `#__lasercity_answer` WHERE user_id=r.user_id AND r.user_id!=0) as count_answer, 
                                    
                                    (SELECT CONCAT_WS(',',TO_DAYS(NOW())-TO_DAYS(u.registerDate), u.name, d.avatar, d.rank)
                                            FROM `#__users` u 
                                            LEFT JOIN `#__users_description` as d ON (u.id=d.user_id) 
                                            WHERE u.id=r.user_id AND r.user_id!=0) as mas_for_user 
                                    
                                    FROM `#__lasercity_reviews` as r 
                                    LEFT JOIN `#__lasercity_cities` as c ON (r.city_id=c.id) 
                                    LEFT JOIN `#__lasercity_translations_" . $language . "` as t ON (c.id = t.object_id) 
                                    WHERE r.published='1' 
                                    AND r.affiliate_id='" . $this->item->id . "' 
                                    AND t.object_name='citie' 
                                    AND t.object_column='title' 
                                    ORDER BY r.id DESC LIMIT 5");
        $reviews = $db->loadObjectList();
        ?>

      <section class="salon-reviews" aria-labelledby="SalonReviews">
        <div class="salon-reviews__title-wrapper">
          <h3 class="salon-reviews-title" id="SalonReviews"><?= JText::_('COM_LASERCITY_ALL_SALON_MESS_REVIEW') ?></h3>
          <p class="salon-reviews__place" itemprop="name"><?= $this->item->title ?></p>
        </div>
          <?php if (is_array($reviews)): ?>
            <ul class="salon-reviews__comments comments">
                <?php if ($raiting_array['raiting'] > 0): ?>
                  <div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                    <meta itemprop="ratingValue" content="<?= $raiting_array['raiting'] ?>">
                    <meta itemprop="reviewCount" content="<?= $raiting_array['count_raiting'] ?>">
                  </div>
                <?php endif; ?>
                <?php foreach ($reviews as $review):

                    $review->avatar = '/' . deleteFormat($review->avatar);
                    $mas_reiting = array($review->place, $review->relationship, $review->purity, $review->quality, $review->price);
                    $averageRating = array_sum($mas_reiting) / count($mas_reiting);

                    // дней,имя,аватар,ранг
                    $mas_for_user = explode(',', $review->mas_for_user);

                    //имя автора
                    $review->name = (!empty($mas_for_user[1]) ? $mas_for_user[1] : $review->name);
                    //аватар
                    $review->avatar = (!empty($mas_for_user[2]) ? '/' . deleteFormat($mas_for_user[2]) : $review->avatar);
                    //ранг
                    $review->rank = ((isset($mas_for_user[3]) && $mas_for_user[3] != "") ? $mas_for_user[3] : $review->rank);
                    ?>
                  <li class="commemts__item" itemprop="review" itemscope itemtype="https://schema.org/Review">
                    <meta itemprop="author" content="<?= $review->name ?>"/>
                    <meta itemprop="datePublished" content="<?= date('d/m/Y', strtotime($review->date_added)) ?>"/>
                    <article class="commemts__comment">
                      <h4 class="visually-hidden"><?= JText::_('COM_LASERCITY_FORM_REVIEW_COMENT') ?> - <?= $review->name ?></h4>
                      <div class="comments__autor">
                        <div class="comments__autor-wrapper">
                          <div class="comments__autor-popup-wrapper comment__autor-image--open-popup">
                            <p class="comments__autor-img-wrapper">
                              <picture>
                                <source srcset="<?= $review->avatar ?>_90x90.webp 1x, <?= $review->avatar ?>_180x180.webp 2x" media="(min-width: 1200px)" type="image/webp">
                                <source srcset="<?= $review->avatar ?>_45x45.webp 1x, <?= $review->avatar ?>_90x90.webp 2x" type="image/webp">
                                <source srcset="<?= $review->avatar ?>_90x90.webp 1x, <?= $review->avatar ?>_180x180.webp 2x" media="(min-width: 1200px)" type="image/webp">
                                <img class="comments__autor-image comments__autor-image--open-popup" src="<?= $review->avatar ?>_90x90.jpg" srcset="<?= $review->avatar ?>_90x90.jpg 2x" loading="lazy" alt="<?= JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR') ?>" title="<?= JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR') ?>">
                              </picture>
                            </p>
                            <div class="comments__autor-popup">
                              <div class="comments__autor-wrapper">
                                <p class="comments__autor-img-wrapper">
                                  <picture>
                                    <source srcset="<?= $review->avatar ?>_90x90.webp 1x, <?= $review->avatar ?>_180x180.webp 2x" type="image/webp">
                                    <img class="comments__autor-image" src="<?= $review->avatar ?>_90x90.jpg" srcset="<?= $review->avatar ?>_180x180.jpg 2x" loading="lazy" alt="<?= JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR') ?>" title="<?= JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR') ?>">
                                  </picture>
                                </p>
                                <div class="comments__autor-fact">
                                  <cite class="comments__autor-name"><?= $review->name ?></cite>
                                  <p class="comments__autor-city"><?= JText::_('COM_LASERCITY_ALL_SALON_TOWN_REVIEW') ?> <?= $review->value ?></p>
                                  <p class="comments__autor-rank">
                                      <?php
                                      switch ($review->rank) {
                                          case 0:
                                              echo JText::_('COM_LASERCITY_ALL_SALON_NOVICHECK');
                                              break;
                                          case 1:
                                              echo JText::_('COM_LASERCITY_ALL_SALON_BUVALUI');
                                              break;
                                          case 2:
                                              echo JText::_('COM_LASERCITY_ALL_SALON_EXPERT');
                                              break;
                                      }
                                      ?>
                                  </p>
                                </div>
                              </div>
                                <?php
                                //если пользователь не зарегистрирован танем данные из таблицы отзывов
                                $review->count_user_day = (!empty($review->count_user_day) ? $review->count_user_day : 1);

                                //если пользователь зарегистрирован тянем данные из таблицы пользователей
                                $review->count_user_day = (!empty($mas_for_user[0]) ? $mas_for_user[0] : $review->count_user_day);
                                ?>
                              <p class="comments__autor-experience"><?= JText::_('COM_LASERCITY_ALL_SALON_S_LASERCISY') . " " . $review->count_user_day . " " . ContentLoader::replaceSuffix($review->count_user_day, 'day') ?></p>
                              <ul class="comments__autor-list">
                                <li class="comments__autor-itme"><span><?= $review->count_review ?></span> <?= ContentLoader::replaceSuffix($review->count_review, 'review', true) ?></li>
                                <li class="comments__autor-itme"><span><?= $review->count_question ?></span> <?= ContentLoader::replaceSuffix($review->count_review, 'question', true) ?></li>
                                <li class="comments__autor-itme"><span><?= $review->count_answer ?></span> <?= ContentLoader::replaceSuffix($review->count_review, 'answer', true) ?></li>
                              </ul>
                              <button class="comments__autor-popup-button button-cross" type="button" title="<?= JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL') ?></span></button>
                            </div>
                          </div>
                          <div class="comments__autor-fact">
                            <cite class="comments__autor-name"><?= $review->name ?></cite>
                            <p class="comments__autor-rank">
                                <?php
                                switch ($review->rank) {
                                    case 0:
                                        echo JText::_('COM_LASERCITY_ALL_SALON_NOVICHECK');
                                        break;
                                    case 1:
                                        echo JText::_('COM_LASERCITY_ALL_SALON_BUVALUI');
                                        break;
                                    case 2:
                                        echo JText::_('COM_LASERCITY_ALL_SALON_EXPERT');
                                        break;
                                }
                                ?>
                            </p>
                            <time class="comments__autor-time"><?= date('d/m/Y', strtotime($review->date_added)) ?></time>
                          </div>
                        </div>
                        <div class="comments__rate">
                          <div class="comments__rating rating-salon">
                            <div class="rating-salon__point-wrapper">
                              <output class="rating-salon__point"><?= number_format((float)$averageRating, 1, ',', '') ?></output>
                              <ul class="rating-salon-stars">
                                  <?php for ($r = 0; $r < 5; $r++): ?>
                                      <?php if ($r < floor($averageRating)): ?>
                                      <li class="rating-salon__star rating-salon__star--silver">
                                        <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207" aria-hidden="true">
                                          <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
                                        </svg>
                                        <span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_RAITING_GOOD') ?></span>
                                      </li>
                                      <?php else: ?>
                                      <li class="rating-salon__star rating-salon__star--transparent">
                                        <svg fill="#cacaca" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 487.222 487.222" aria-hidden="true">
                                          <g>
                                            <path d="M486.554,186.811c-1.6-4.9-5.8-8.4-10.9-9.2l-152-21.6l-68.4-137.5c-2.3-4.6-7-7.5-12.1-7.5l0,0c-5.1,0-9.8,2.9-12.1,7.6 l-67.5,137.9l-152,22.6c-5.1,0.8-9.3,4.3-10.9,9.2s-0.2,10.3,3.5,13.8l110.3,106.9l-25.5,151.4c-0.9,5.1,1.2,10.2,5.4,13.2 c2.3,1.7,5.1,2.6,7.9,2.6c2.2,0,4.3-0.5,6.3-1.6l135.7-71.9l136.1,71.1c2,1,4.1,1.5,6.2,1.5l0,0c7.4,0,13.5-6.1,13.5-13.5 c0-1.1-0.1-2.1-0.4-3.1l-26.3-150.5l109.6-107.5C486.854,197.111,488.154,191.711,486.554,186.811z M349.554,293.911 c-3.2,3.1-4.6,7.6-3.8,12l22.9,131.3l-118.2-61.7c-3.9-2.1-8.6-2-12.6,0l-117.8,62.4l22.1-131.5c0.7-4.4-0.7-8.8-3.9-11.9 l-95.6-92.8l131.9-19.6c4.4-0.7,8.2-3.4,10.1-7.4l58.6-119.7l59.4,119.4c2,4,5.8,6.7,10.2,7.4l132,18.8L349.554,293.911z"></path>
                                          </g>
                                        </svg>
                                        <span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_RAITING_NO_GOOD') ?></span>
                                      </li>
                                      <?php endif; ?>
                                  <?php endfor; ?>
                              </ul>
                            </div>
                          </div>
                          <button class="comments__button-functions" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SALON_OPEN_FUNC_MENU') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SALON_OPEN_FUNC_MENU') ?>">
                            <span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_SALON_OPEN_FUNC_MENU') ?></span>
                          </button>
                          <ul class="comments__buttons-list">
                            <li class="comments__buttons-item">
                              <button class="comment__button"><?= JText::_('COM_LASERCITY_ALL_SALON_CHECK_NEPRIEM') ?></button>
                            </li>
                          </ul>
                        </div>
                      </div>

                      <div itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                        <meta itemprop="worstRating" content="1"/>
                        <meta itemprop="ratingValue" content="<?= floor($averageRating) ?>"/>
                        <meta itemprop="bestRating" content="5"/>
                      </div>
                      <meta itemprop="description" content="<?= $review->koment ?>"/>

                      <div class="comments__blockquote">
                        <p class="comments__text"><?= $review->koment ?></p>
                          <?php $fotos = json_decode($review->foto, true);
                          if (is_array($fotos)): ?>

                            <p class="comments__photos">
                                <?php foreach ($fotos as $foto): $foto = '/' . deleteFormat($foto); ?>
                                  <picture>
                                    <source srcset="<?= $foto ?>_105x80.webp 1x, <?= $foto ?>_210x160.webp 2x" media="(min-width: 1200px)" type="image/webp">
                                    <source srcset="<?= $foto ?>_82x63.webp 1x, <?= $foto ?>_164x126.webp 2x" type="image/webp">
                                    <source srcset="<?= $foto ?>_105x80.webp 1x, <?= $foto ?>_210x160.webp 2x" media="(min-width: 1200px)">
                                    <img class="comments__photo" src="<?= $foto ?>_82x63.jpg" srcset="<?= $foto ?>_164x126.jpg 2x" loading="lazy" alt="" title="">
                                  </picture>
                                <?php endforeach; ?>
                            </p>

                          <?php endif; ?>
                        <div class="comments__feedback">
                          <p class="comments__feedback-visit"><?= JText::_('COM_LASERCITY_ALL_SALON_PERIOD_VISIT') ?> <?= (!preg_match('/[1-9]/', $review->date_visit) ? JText::_('COM_LASERCITY_ALL_SALON_NO_UKAZAN') : date('d/m/Y', strtotime($review->date_visit))) ?><br>
                              <?php
                              switch ($review->confirm) {
                                  case 0:
                                      echo '<span class="comments__feedback-visit--false">' . JText::_('COM_LASERCITY_ALL_SALON_NO_CONFIRM') . '</span>';
                                      break;
                                  case 1:
                                      echo '<span class="comments__feedback-visit--true">' . JText::_('COM_LASERCITY_ALL_SALON_CONFIRM') . '</span>';
                                      break;
                              }
                              ?>
                          </p>
                          <button class="comments__feedback-like" data-review="<?= $review->id ?>">
                            <output><?= $review->likes ?></output>
                          </button>
                        </div>
                      </div>
                    </article>
                  </li>
                <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        <div class="salon-reviews__reviews-visit">
          <p class="salon-reviews__visit-question"><?= JText::_('COM_LASERCITY_ALL_SALON_VISIT_REVIEW') ?> «<?= $this->item->title ?>»?</p>
          <button class="salon-reviews__visit-button button" data-set-modal-value="addcomment"><?= JText::_('COM_LASERCITY_ALL_SALON_PUSH_REVIEW') ?></button>
        </div>
      </section>
        </div>
       </div>

    </section>
  </main>
  <aside class="page-aside salon-aside">
    <div class="mailing">
      <b class="mailing__slogan"><?= JText::_('COM_LASERCITY_ALL_WRITING_RSS') ?></b>
      <form class="mailing__form" method="post">
        <p class="mailing__input-wrapper">
          <input class="mailing__input" type="text" autocomplete="off" name="mailing" aria-label="<?= JText::_('COM_LASERCITY_ALL_FORM_EMAIL') ?>" placeholder="<?= JText::_('COM_LASERCITY_ALL_FORM_EMAIL') ?>">
        </p>
        <button class="mailing__button">Ок</button>
      </form>
    </div>
  </aside>
  <script type="application/ld+json">
	{
		"@context": "schema.org",
		"@type": "Organization",
		"address": {
			"@type": "PostalAddress",
			"addressLocality": "<?= $city_title ?>",
			"postalCode": "<?= $this->item->city_index ?>",
			"streetAddress": "<?= implode(', ', $location) ?>"
		  },
		"email": "<?= $this->item->mail ?>",
		"name": "<?= $this->item->title ?>",
		"telephone" : ["<?= implode('", "', $this->item->phones) ?>"],
		"url": "<?= $urNoHTTPS . LangHelper::getCurrentSef() ?>/",
		"logo": "https://<?= $_SERVER['SERVER_NAME'] ?><?= $logo_micro ?>"
	}

  </script>
<?php


/*echo '<pre>';
print_r($this->item);
echo '</pre>';*/
?>