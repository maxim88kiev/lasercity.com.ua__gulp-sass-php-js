<?php defined('_JEXEC') or die;

$filters_list = Search::getFiltersList(array('services', 'apparatus'));
//$prices_list = $this->prices;

$obj_city = CityHelper::getCurrent();
$city_alias = $obj_city->alias;

$language = JFactory::$language->getTag();

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

$link_mok = $current_sef."clinics/{$city_alias}/";


ContentLoader::addScript('index');
JHtml::_('script', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCnqzpizVNCHvN4_d5m-5m2e1RULeN5JGU');
ContentLoader::addScript('drawMarker');

$db->setQuery("
  SELECT `id`, `logo`, `main_image`, `alias`, `organization`, (
   SELECT `value`
    FROM `#__lasercity_translations_{$language}`
    WHERE
        `object_name`='affiliate' AND
        `object_column`='title' AND
        `object_id`=`main`.`id`
  
  ) as `title`,(
   SELECT `value`
    FROM `#__lasercity_translations_{$language}`
    WHERE
        `object_name`='affiliate' AND
        `object_column`='type' AND
        `object_id`=`main`.`id`
     
  ) as `type`
   
   FROM `#__lasercity_affiliates` as `main` ORDER BY `premium` DESC, `ordering` DESC LIMIT 8
");

$topAffiliates = $db->loadObjectList();
?>


<main>
  <h1 class="visually-hidden"><?=JText::_('COM_LASERCITY_H1_HOME')?></h1>
  <section class="advantages" aria-labelledby="LaserCityIs">
    <div class="advantages__wrapper">
      <header class="advantages__header" id="LaserCityIs">
        <h2 class="advantages__slogan"><?=JText::_('COM_LASERCITY_H2_1_HOME')?></h2>
        <p class="advantages__text"><?=JText::_('COM_LASERCITY_P_1_HOME')?></p>
      </header>
      <div class="advantages__input-wrapper">
        <input class="advantages__input" type="search" autocomplete="off" aria-label="<?=JText::_('COM_LASERCITY_PLACEHOLDER_1_HOME')?>"
               placeholder="<?=JText::_('COM_LASERCITY_PLACEHOLDER_1_HOME')?>">
        <div class="advantages__autocomplete autocomplete">
          <ul class="autocomplete__list"></ul>
          <p class="autocomplete__results">
            <output></output>
          </p>
        </div>
      </div>
      <p class="advantages__information"><?=JText::_('COM_LASERCITY_P_2_HOME')?></p>
        <?php
        $db = JFactory::getDbo();
        $db->setQuery("SELECT COUNT(*) FROM `#__lasercity_affiliates` WHERE `published`");
        $affiliatesCount = $db->loadResult();
        $db->setQuery("SELECT COUNT(*) FROM `#__lasercity_last_visit` WHERE `object_type`='home'");
        $usersCount = $db->loadResult();
        ?>
      <ul class="advantages__list">
        <li class="advantages__item advantages__item--salons">
          <output><?= $affiliatesCount ?></output>
          <?=JText::_('COM_LASERCITY_LI_1_HOME')?>
        </li>
        <li class="advantages__item advantages__item--visitors">
          <output><?= $usersCount ?></output>
          <?=JText::_('COM_LASERCITY_LI_2_HOME')?>
        </li>
        <li class="advantages__item advantages__item--contract">
            <?php
            $total_service = ContentLoader::getWriteServiceTotal();
            ?>
          <output><?=$total_service?></output>
            <?=ContentLoader::replaceSuffix($total_service,'write',true)?> <?=JText::_('COM_LASERCITY_LI_3_HOME')?>
        </li>
      </ul>
    </div>
  </section>
  <section class="top-rating" aria-labelledby="BestSalons">
    <h2 class="top-rating__title title-lines" id="BestSalons"><?=JText::_('COM_LASERCITY_H2_2_HOME')?></h2>
    <p class="top-rating__text"><?=JText::_('COM_LASERCITY_P_3_HOME')?></p>
    <div class="top-rating__slider slider">
      <ul class="slider__list">
          <?php foreach ($topAffiliates as $affiliate):
              $link = $current_sef."clinics/{$affiliate->alias}"; ?>
            <li class="slider__item">
              <div class="slider__item-wrapper">
                <picture>
                    <?php
                    $image = 'https://placehold.it/';
                    if ($affiliate->organization == 0) {
                        if (mb_strlen($affiliate->logo)) {
                            $image = '/' . $affiliate->logo;
                        }
                    } else {
                        if (mb_strlen($affiliate->main_image)) {
                            $image = '/' . $affiliate->main_image;
                        }
                    }
                    $image = deleteFormat($image);
                    ?>
                  <source data-srcset="<?= $image ?>_262x198.webp 1x, <?= $image ?>_524x396.webp 2x" type="image/webp">
                  <img class="slider__img lazyload" data-src="<?= $image ?>_262x198.jpg" data-srcset="<?= $image ?>_524x396.jpg 2x" loading="lazy" title="<?=JText::_('COM_LASERCITY_IMG_TITLE_1_HOME')?> <?= $affiliate->title ?>" alt="<?=JText::_('COM_LASERCITY_IMG_TITLE_1_HOME')?> <?= $affiliate->title ?>">
                </picture>
                <h4 class="slider__item-title"><a href="<?= $link ?>"><?= $affiliate->title ?></a></h4>
                <p class="slider__item-description"><?= $affiliate->type ?></p>
                <a class="slider__rating rating-salon" href="<?= $link ?>#SalonRation">
                  <div class="rating-salon__point-wrapper">
                    <span class="rating-salon__point">
                        <?php
                            $raiting_array = ContentLoader::getRaiting($affiliate->id);
                            echo $raiting_array['raiting'];
                        ?>
                    </span>
                    <ul class="rating-salon-stars">
                        <?php for ($r=0;$r<5;$r++): ?>
                            <?php if($r<floor($raiting_array['raiting'])): ?>
                                <li class="rating-salon__star rating-salon__star--silver">
                                    <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207" aria-hidden="true">
                                        <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
                                    </svg>
                                    <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_RAITING_GOOD')?></span>
                                </li>
                            <?php else:?>
                                <li class="rating-salon__star rating-salon__star--transparent">
                                    <svg fill="#cacaca" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 487.222 487.222" aria-hidden="true">
                                        <g>
                                            <path d="M486.554,186.811c-1.6-4.9-5.8-8.4-10.9-9.2l-152-21.6l-68.4-137.5c-2.3-4.6-7-7.5-12.1-7.5l0,0c-5.1,0-9.8,2.9-12.1,7.6 l-67.5,137.9l-152,22.6c-5.1,0.8-9.3,4.3-10.9,9.2s-0.2,10.3,3.5,13.8l110.3,106.9l-25.5,151.4c-0.9,5.1,1.2,10.2,5.4,13.2 c2.3,1.7,5.1,2.6,7.9,2.6c2.2,0,4.3-0.5,6.3-1.6l135.7-71.9l136.1,71.1c2,1,4.1,1.5,6.2,1.5l0,0c7.4,0,13.5-6.1,13.5-13.5 c0-1.1-0.1-2.1-0.4-3.1l-26.3-150.5l109.6-107.5C486.854,197.111,488.154,191.711,486.554,186.811z M349.554,293.911 c-3.2,3.1-4.6,7.6-3.8,12l22.9,131.3l-118.2-61.7c-3.9-2.1-8.6-2-12.6,0l-117.8,62.4l22.1-131.5c0.7-4.4-0.7-8.8-3.9-11.9 l-95.6-92.8l131.9-19.6c4.4-0.7,8.2-3.4,10.1-7.4l58.6-119.7l59.4,119.4c2,4,5.8,6.7,10.2,7.4l132,18.8L349.554,293.911z"></path>
                                        </g>
                                    </svg>
                                    <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_RAITING_NO_GOOD')?></span>
                                </li>
                            <?php endif;?>
                        <?php endfor;?>
                    </ul>
                  </div>
                  <p class="rating-salon__reviews">
                      <output><?=$raiting_array['count_raiting']?></output> <?=ContentLoader::replaceSuffix($raiting_array['count_raiting'],'review')?>
                  </p>
                </a>
              </div>
            </li>
          <?php endforeach; ?>
      </ul>
      <button class="slider__button slider__button--prev" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?></span>
        <svg width="7" height="62" aria-hidden="true">
          <path d="M296.643,1188.99l3.325-30.99-3.325-30.99L304,1158Z" transform="translate(-296.656 -1127)"></path>
        </svg>
      </button>
      <button class="slider__button slider__button--next" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?></span>
        <svg width="7" height="62" aria-hidden="true">
          <path d="M296.643,1188.99l3.325-30.99-3.325-30.99L304,1158Z" transform="translate(-296.656 -1127)"></path>
        </svg>
      </button>
    </div>
  </section>
  <section class="attendance" aria-labelledby="Services">
    <h2 class="visually-hidden" id="Services"><?=JText::_('COM_LASERCITY_H2_3_HOME')?></h2>
    <ul class="attendance__search-list">
      <li class="attendance__search-item attendance__search-item--services">
        <div class="attendance__header">
            <?php
            /*$count_services = 0;
            foreach ($filters_list['services'] as $zone => $services) {
                foreach ($services as $service) {
                    $count_services += $service['count'];
                }
            }*/
            ?>
            <h3 class="attendance__title">
                <span class="attendance__title--mob"><?=JText::_('COM_LASERCITY_SPAN_1_HOME')?></span>
                <span class="attendance__title--pc"><?=JText::_('COM_LASERCITY_SPAN_2_HOME')?></span>
            </h3>
            <button class="attendance__button" type="button" title="<?=JText::_('COM_LASERCITY_BUTTON_1_HOME')?>" aria-label="<?=JText::_('COM_LASERCITY_BUTTON_1_HOME')?>">
                <span class="visually-hidden"><?=JText::_('COM_LASERCITY_BUTTON_1_HOME')?></span>
            </button>

        </div>
          <div class="attendance__for-wrapper">
              <b class="attendance__for-title"><?=JText::_('COM_LASERCITY_B_7_HOME')?></b>
              <p class="attendance__for buttons-for">
                  <button class="buttons-for__for buttons-for__for--women buttons-for__for--active" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SALON_SHOW_PRICE_FOR_WONEN')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_SHOW_PRICE_FOR_WONEN')?>"><b class="women"><span><b class="for">Для</b> <?=JText::_('COM_LASERCITY_ALL_SALON_SHOW_FOR_WONEN')?></span></b></button>
                  <button class="buttons-for__for buttons-for__for--men" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SALON_SHOW_PRICE_FOR_NEN')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_SHOW_PRICE_FOR_NEN')?>"><b class="men"><span><b class="for">Для</b> <?=JText::_('COM_LASERCITY_ALL_SALON_SHOW_FOR_NEN')?></span></b></button>
              </p>
          </div>
          <div class="attendance__list-wrapper attendance__list-wrapper--women">
            <ul class="attendance__list attendance__services-list attendance__services-list--women">

            <?php foreach ($filters_list['services'] as $zone => $services):
                $count = 0;
                foreach ($services as $service) {
                    $count += $service['count'];
                } ?>
                <li class="attendance__services-item">
                    <div class="attendance__services-item-wrapper">
                        <div class="attendance__services-header">
                            <h4 class="attendance__services-title"><?= $zone ?> (<output><?= $count ?></output>)</h4>
                            <button class="attendance__services-button" type="button" title="<?=JText::_('COM_LASERCITY_BUTTON_4_HOME')?>" aria-label="<?=JText::_('COM_LASERCITY_BUTTON_4_HOME')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_BUTTON_4_HOME')?></span></button>
                        </div>
                        <div class="attendance__services-table-wrapper">

                            <table class="attendance__services-table services-table">
                                <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td class="services-table__name">
                                            <a href="<?= $link_mok . $service['alias'] ?>"><?= $service['title'] ?></a>
                                        </td>
                                        <td class="services-table__count">
                                            <output><?= $service['count'] ?></output>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <?php if (count($services) > 5): ?>
                            <button class="attendance__services-all"><?=JText::_('COM_LASERCITY_BUTTON_5_HOME')?></button>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
            <ul class="attendance__list attendance__services-list attendance__services-list--men"></ul>
          </div>
      </li>
      <li class="attendance__search-item attendance__search-item--aparats">
        <div class="attendance__aparats-wrapper">
            <?php $all_count = 0;
              foreach ($filters_list['apparatus'] as $type => $apparatus) {
                  foreach ($apparatus as $apparatus_single) {
                      $all_count += $apparatus_single->count;
                  }
              } ?>
            <div class="attendance__header">
                <h3 class="attendance__title">
                    <span class="attendance__title--mob"><?=JText::_('COM_LASERCITY_SPAN_3_HOME')?></span>
                    <span class="attendance__title--pc"><?=JText::_('COM_LASERCITY_SPAN_4_1_HOME')?></span>
                </h3>
                <button class="attendance__button" type="button" title="<?=JText::_('COM_LASERCITY_BUTTON_6_HOME')?>" aria-label="<?=JText::_('COM_LASERCITY_BUTTON_6_HOME')?>">
                    <span class="visually-hidden"><?=JText::_('COM_LASERCITY_BUTTON_6_HOME')?></span>
                </button>
            </div>
            <div class="attendance__list-wrapper">
                <ul class="attendance__list attendance__aparats-list">
              <?php foreach ($filters_list['apparatus'] as $type => $apparatus): ?>
                <li class="attendance__aparats-item">
                  <div class="attendance__aparats-header">
                      <?php $count = 0;
                      foreach ($apparatus as $apparatus_single) {
                          $count += $apparatus_single->count;
                      } ?>
                    <h4 class="attendance__aparats-name"><?= $type ?> <!--(<output><?= $count ?></output>)--></h4>
                    <button class="attendance__aparats-button" type="button" title="<?=JText::_('COM_LASERCITY_BUTTON_7_HOME')?>" aria-label="<?=JText::_('COM_LASERCITY_BUTTON_7_HOME')?>"><span
                        class="visually-hidden"><?=JText::_('COM_LASERCITY_BUTTON_7_HOME')?></span></button>
                  </div>
                    <table class="attendance__aparats-table services-table">
                        <tbody>
                            <?php foreach ($apparatus as $apparatus_single): ?>
                                <tr>
                                    <td class="services-table__name"><a href="<?= $link_mok . $apparatus_single->alias ?>"><?= $apparatus_single->title ?></a></td>
                                    <td class="services-table__count"><output><?= $apparatus_single->count ?></output></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                  <!--<ul class="attendance__aparats-sort-list">
                      <?php foreach ($apparatus as $apparatus_single): ?>
                        <li class="attendance__aparats-sort-item">
                          <a href="<?= $link_mok . $apparatus_single->alias ?>">
                          <?= $apparatus_single->title ?>
                            <output>(<?= $apparatus_single->count ?>)</output>
                          </a>
                        </li>
                      <?php endforeach; ?>
                  </ul>-->

                </li>
              <?php endforeach; ?>
          </ul>
            </div>

            <button class="attendance__aparats-full" type="button">
                <?=JText::_('COM_LASERCITY_ALL_SHOW_ALL')?> <output><?=$all_count?></output> <?=ContentLoader::replaceSuffix($all_count,'apparat')?>
            </button>
            <!--<a class="attendance__aparats-link" href="#">
                <span class="attendance__aparats-link--mob"><?=JText::_('COM_LASERCITY_A_SEE_ALL_HOME')?></span>
                <span class="attendance__aparats-link--pc"><?=JText::_('COM_LASERCITY_ALL_SHOW_ALL')?> <output><?=$all_count?></output> <?=ContentLoader::replaceSuffix($all_count,'apparat')?></span>
            </a>-->
        </div>
      </li>
    </ul>
  </section>
  <section class="index-stocks" aria-labelledby="LaserCityStocks">
    <h2 class="index-stocks__title title-lines" id="LaserCityStocks"><span><?=JText::_('COM_LASERCITY_H2_4_HOME')?></span>
      <output>(<?=ContentLoader::getStockTotal()?>)</output>
    </h2>
    <div class="index-stocks__slider stock-slider slider">
      <ul class="slider__list">

          <?php
          $language = JFactory::$language->getTag();
          $db = JFactory::getDbo();
          $db->setQuery("SELECT s.id,s.alias,s.image,s.discount,s.organization,t.value 
                        FROM `#__lasercity_stock` as s 
                        LEFT JOIN `#__lasercity_translations_" . $language . "` as t ON t.object_id = s.id 
                        WHERE t.object_column='title' 
                        AND t.object_name='stock' 
                        AND s.published='1' 
                        AND s.city_id='".$obj_city->id."' 
                        AND UNIX_TIMESTAMP(NOW())>=UNIX_TIMESTAMP(s.date_added) 
			            AND UNIX_TIMESTAMP(NOW())<=UNIX_TIMESTAMP(s.date_remove) 
			            ORDER BY s.ordering ASC Limit 6");

          $stocks = $db->loadObjectList();
          ?>
          <?php foreach ($stocks as $stock): $stock->image = deleteFormat($stock->image) ?>
            <li class="slider__item">
              <div class="slider__item-wrapper">
                <output class="stock-slider__item-discount">-<?= $stock->discount ?>%</output>
                <picture>
                  <source data-srcset="/<?= $stock->image ?>_358x258.webp 1x, /<?= $stock->image ?>_716x516.webp 2x" media="(min-width: 1200px)" type="image/webp">
                  <source data-srcset="/<?= $stock->image ?>_262x198.webp 1x, /<?= $stock->image ?>_524x396.webp 2x" type="image/webp">
                  <source data-srcset="/<?= $stock->image ?>_358x258.jpg 1x, /<?= $stock->image ?>_716x516.jpg 2x" media="(min-width: 1200px)">
                  <img class="stock-slider__item-img lazyload" data-src="/<?= $stock->image ?>_262x198.jpg" data-srcset="/<?= $stock->image ?>_524x396.jpg 2x" loading="lazy" title="<?=JText::_('COM_LASERCITY_ALL_IMG_1_TITLE')?>: <?= $stock->value ?>" alt="<?=JText::_('COM_LASERCITY_ALL_IMG_1_TITLE')?>: <?= $stock->value ?>">
                </picture>
                <div class="stock-slider__item-wrapper">
                  <h4 class="stock-slider__item-title"><?= $stock->value ?></h4>
                    <?php $count_addres = 0;
                    $db->setQuery("SELECT f.city FROM `#__lasercity_affiliates` as f WHERE f.published='1' AND f.organization!=0 AND f.organization='" . $stock->organization . "' AND f.city='" . $obj_city->id . "'");
                    $count_addres = $db->loadObjectList(); ?>
                  <p class="stock-slider__item-address"><?= count($count_addres) ?> <?=ContentLoader::replaceSuffix(count($count_addres),'adress')?> в <?= $obj_city->title ?></p>
                  <p class="stock-slider__item-footer">
                    <output class="stock-slider__item-purchased">(<?=ContentLoader::getWriteServiceTotal('stock',$stock->id)?>)</output>
                    <a class="stock-slider__item-look button" href="<?=$current_sef."stocks/{$stock->alias}"?>/"><?=JText::_('COM_LASERCITY_ALL_A_SEE')?></a>
                  </p>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
      </ul>
      <button class="slider__button slider__button--prev" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?></span>
        <svg width="7" height="62" aria-hidden="true">
          <path d="M296.643,1188.99l3.325-30.99-3.325-30.99L304,1158Z" transform="translate(-296.656 -1127)"></path>
        </svg>
      </button>
      <button class="slider__button slider__button--next" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?></span>
        <svg width="7" height="62" aria-hidden="true">
          <path d="M296.643,1188.99l3.325-30.99-3.325-30.99L304,1158Z" transform="translate(-296.656 -1127)"></path>
        </svg>
      </button>
    </div>
  </section>
  <section class="map" aria-labelledby="MapSalons">
    <h2 class="map__title" id="MapSalons"><?=JText::_('COM_LASERCITY_H2_5_HOME')?> <?= $obj_city->title ?></h2>
    <div class="map__maps">
      <div class="map__iframe" title="<?=JText::_('COM_LASERCITY_DIV_1_HOME')?>"></div>
      <p class="map__img-wrapper">
        <picture>
          <source srcset="https://placehold.it/1170x685/red.wepb 1x, https://placehold.it/2340x1370/red.wepb 2x" media="(min-width: 1200px)" type="image/webp">
          <source srcset="https://placehold.it/320x400/red.wepb 1x, https://placehold.it/640x800/red.wepb 2x" type="image/webp">
          <source srcset="https://placehold.it/1170x685/red.jpg 1x, https://placehold.it/2340x1370/red.jpg 2x" media="(min-width: 1200px)">
          <img class="map__img" src="https://placehold.it/320x400/red.jpg" srcset="https://placehold.it/640x800/red.jpg 2x" loading="lazy" title="" alt="">
        </picture>
      </p>
    </div>
  </section>
  <section class="lasercity" aria-labelledby="LaserCityItIsJust">
    <h2 class="lasercity__title title-lines" id="LaserCityItIsJust"><?=JText::_('COM_LASERCITY_H2_6_HOME')?></h2>
    <div class="laserciti__wrapper">
      <p class="lasercity__for">Для
        <button class="lasercity__for-button lasercity__for-button--active"><?=JText::_('COM_LASERCITY_BUTTON_8_HOME')?></button>
        /
        <button class="lasercity__for-button"><?=JText::_('COM_LASERCITY_BUTTON_9_HOME')?></button>
      </p>
      <div class="lasercity__steps slider">
        <div class="slider__list slider__list--partner slider__list--active">
          <ol class="lasercity__steps-list">
            <li class="lasercity__steps-item">
              <p class="lasercity__steps-wrapper lasercity__steps-wrapper--partner-one">
                <span class="lasercity__step"><?=JText::_('COM_LASERCITY_SPAN_5_HOME')?> <span>1</span></span>
                <b class="lasercity__task"><?=JText::_('COM_LASERCITY_B_1_HOME')?></b>
              </p>
              <p class="lasercity__steps-description"><?=JText::_('COM_LASERCITY_P_4_HOME')?></p>
            </li>
            <li class="lasercity__steps-item lasercity__steps-item--active">
              <p class="lasercity__steps-wrapper lasercity__steps-wrapper--partner-two">
                <span class="lasercity__step"><?=JText::_('COM_LASERCITY_SPAN_5_HOME')?> <span>2</span></span>
                <b class="lasercity__task"><?=JText::_('COM_LASERCITY_B_2_HOME')?></b>
              </p>
              <p class="lasercity__steps-description"><?=JText::_('COM_LASERCITY_P_5_HOME')?></p>
            </li>
            <li class="lasercity__steps-item">
              <p class="lasercity__steps-wrapper lasercity__steps-wrapper--partner-three">
                <span class="lasercity__step"><?=JText::_('COM_LASERCITY_SPAN_5_HOME')?> <span>3</span></span>
                <b class="lasercity__task"><?=JText::_('COM_LASERCITY_B_3_HOME')?></b>
              </p>
              <p class="lasercity__steps-description"><?=JText::_('COM_LASERCITY_P_6_HOME')?></p>
            </li>
          </ol>
        </div>
        <div class="slider__list slider__list--customer">
          <ol class="lasercity__steps-list">
            <li class="lasercity__steps-item lasercity__steps-item--active">
              <p class="lasercity__steps-wrapper lasercity__steps-wrapper--customer-one">
                <span class="lasercity__step"><?=JText::_('COM_LASERCITY_SPAN_5_HOME')?> <span>1</span></span>
                <b class="lasercity__task"><?=JText::_('COM_LASERCITY_B_4_HOME')?></b>
              </p>
              <p class="lasercity__steps-description"><?=JText::_('COM_LASERCITY_P_7_HOME')?></p>
            </li>
            <li class="lasercity__steps-item">
              <p class="lasercity__steps-wrapper lasercity__steps-wrapper--customer-two">
                <span class="lasercity__step"><?=JText::_('COM_LASERCITY_SPAN_5_HOME')?> <span>2</span></span>
                <b class="lasercity__task"><?=JText::_('COM_LASERCITY_B_5_HOME')?></b>
              </p>
              <p class="lasercity__steps-description"><?=JText::_('COM_LASERCITY_P_8_HOME')?></p>
            </li>
            <li class="lasercity__steps-item">
              <p class="lasercity__steps-wrapper lasercity__steps-wrapper--customer-three">
                <span class="lasercity__step"><?=JText::_('COM_LASERCITY_SPAN_5_HOME')?> <span>3</span></span>
                <b class="lasercity__task"><?=JText::_('COM_LASERCITY_B_6_HOME')?></b>
              </p>
              <p class="lasercity__steps-description"><?=JText::_('COM_LASERCITY_P_9_HOME')?></p>
            </li>
          </ol>
        </div>
        <button class="slider__button slider__button--prev" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?></span>
          <svg width="7" height="62" aria-hidden="true">
            <path d="M296.643,1188.99l3.325-30.99-3.325-30.99L304,1158Z" transform="translate(-296.656 -1127)"></path>
          </svg>
        </button>
        <button class="slider__button slider__button--next" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?></span>
          <svg width="7" height="62" aria-hidden="true">
            <path d="M296.643,1188.99l3.325-30.99-3.325-30.99L304,1158Z" transform="translate(-296.656 -1127)"></path>
          </svg>
        </button>
      </div>
    </div>
    <p class="lasercity__registers">
      <a href="<?=$current_sef?>register/?type=organization" class="lasercity__register lasercity__register--organization button lasercity__register--active" target="_blank" rel="noreferrer noopener"><?=JText::_('COM_LASERCITY_BUTTON_10_HOME')?></a>
      <a href="<?=$current_sef?>register/?type=client" class="lasercity__register lasercity__register--customers button" target="_blank" rel="noreferrer noopener"><?=JText::_('COM_LASERCITY_BUTTON_11_HOME')?></a>
    </p>
  </section>
  <section class="articles-index" aria-labelledby="UsefulArticles">
    <h2 class="articles-index__title title-lines" id="UsefulArticles"><?=JText::_('COM_LASERCITY_H2_7_HOME')?></h2>
      <?php
      $db = JFactory::getDbo();
      $tag = JFactory::$language->getTag();
      $db->setQuery("
SELECT `alias`, `main_image`, `date`, `likes`, `see`,
    (
        SELECT `value`
        FROM `#__lasercity_translations_{$tag}`
        WHERE 
            `object_id`=`main`.`id` AND
            `object_column`='title' AND
            `object_name`='article'
    ) as `title`
FROM `#__lasercity_articles` as `main`
WHERE `published`
ORDER BY `ordering` ASC, `date` DESC
LIMIT 4
        ");
      $articles = $db->loadObjectList();
      ?>
    <ul class="articles-index__list">
        <?php foreach ($articles as $article): $article->main_image = deleteFormat($article->main_image) ?>
          <li class="articles-index__item">
            <article class="articles-index__article">
              <picture>
                <source data-srcset="/<?= $article->main_image ?>_262x198.webp 1x, /<?= $article->main_image ?>_524x396.webp 2x" media="(min-width: 1200px)" type="image/webp">
                <source data-srcset="/<?= $article->main_image ?>_90x68.webp 1x, /<?= $article->main_image ?>_180x136.webp 2x" type="image/webp">
                <source data-srcset="/<?= $article->main_image ?>_262x198.jpg 1x, /<?= $article->main_image ?>_524x396.jpg 2x" media="(min-width: 1200px)">
                <img class="articles-index__image lazyload" data-src="/<?= $article->main_image ?>_90x68.jpg" data-srcset="/<?= $article->main_image ?>_180x136.jpg 2x" loading="lazy" title="<?=JText::_('COM_LASERCITY_ALL_IMG_TITLE')?>: <?= $article->title ?>" alt="<?=JText::_('COM_LASERCITY_ALL_IMG_TITLE')?>: <?= $article->title ?>">
              </picture>
              <a class="articles-index__article-information" href="<?=$current_sef."articles/{$article->alias}"?>/">
                <h4 class="articles-index__article-title"><?= $article->title ?></h4>
                <ul class="articles-index__article-stats">
                  <li class="articles-index__article-stat articles-index__article-stat--views">
                    <svg fill="#cccccc" width="18" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 511.999 511.999" aria-hidden="true">
                      <path d="M508.745,246.041c-4.574-6.257-113.557-153.206-252.748-153.206S7.818,239.784,3.249,246.035 c-4.332,5.936-4.332,13.987,0,19.923c4.569,6.257,113.557,153.206,252.748,153.206s248.174-146.95,252.748-153.201 C513.083,260.028,513.083,251.971,508.745,246.041z M255.997,385.406c-102.529,0-191.33-97.533-217.617-129.418 c26.253-31.913,114.868-129.395,217.617-129.395c102.524,0,191.319,97.516,217.617,129.418 C447.361,287.923,358.746,385.406,255.997,385.406z"></path>
                      <path d="M255.997,154.725c-55.842,0-101.275,45.433-101.275,101.275s45.433,101.275,101.275,101.275 s101.275-45.433,101.275-101.275S311.839,154.725,255.997,154.725z M255.997,323.516c-37.23,0-67.516-30.287-67.516-67.516 s30.287-67.516,67.516-67.516s67.516,30.287,67.516,67.516S293.227,323.516,255.997,323.516z"></path>
                    </svg>
                    <output><?= $article->likes ?></output>
                    <span class="visually-hidden"><?=ContentLoader::replaceSuffix($article->likes,'like',true)?></span>
                  </li>
                  <li class="articles-index__article-stat articles-index__article-stat--likes">
                    <svg fill="#cacaca" width="15" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 487.222 487.222" aria-hidden="true">
                      <path d="M486.554,186.811c-1.6-4.9-5.8-8.4-10.9-9.2l-152-21.6l-68.4-137.5c-2.3-4.6-7-7.5-12.1-7.5l0,0c-5.1,0-9.8,2.9-12.1,7.6 l-67.5,137.9l-152,22.6c-5.1,0.8-9.3,4.3-10.9,9.2s-0.2,10.3,3.5,13.8l110.3,106.9l-25.5,151.4c-0.9,5.1,1.2,10.2,5.4,13.2 c2.3,1.7,5.1,2.6,7.9,2.6c2.2,0,4.3-0.5,6.3-1.6l135.7-71.9l136.1,71.1c2,1,4.1,1.5,6.2,1.5l0,0c7.4,0,13.5-6.1,13.5-13.5 c0-1.1-0.1-2.1-0.4-3.1l-26.3-150.5l109.6-107.5C486.854,197.111,488.154,191.711,486.554,186.811z M349.554,293.911 c-3.2,3.1-4.6,7.6-3.8,12l22.9,131.3l-118.2-61.7c-3.9-2.1-8.6-2-12.6,0l-117.8,62.4l22.1-131.5c0.7-4.4-0.7-8.8-3.9-11.9 l-95.6-92.8l131.9-19.6c4.4-0.7,8.2-3.4,10.1-7.4l58.6-119.7l59.4,119.4c2,4,5.8,6.7,10.2,7.4l132,18.8L349.554,293.911z"></path>
                    </svg>
                    <output><?= $article->see ?></output>
                    <span class="visually-hidden"><?=ContentLoader::replaceSuffix($article->see,'see',true)?></span>
                  </li>
                </ul>
              </a>
            </article>
          </li>
        <?php endforeach; ?>
    </ul>
  </section>
</main>
<aside class="page-aside index-aside">
  <div class="mailing">
    <b class="mailing__slogan"><?=JText::_('COM_LASERCITY_ALL_WRITING_RSS')?></b>
    <form class="mailing__form" method="post">
      <p class="mailing__input-wrapper">
        <input class="mailing__input" type="text" autocomplete="off" name="mailing" aria-label="<?=JText::_('COM_LASERCITY_ALL_WRITE_RSS')?>" placeholder="Email">
      </p>
      <button class="mailing__button" title="<?=JText::_('COM_LASERCITY_ALL_WRITE_RSS')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_WRITE_RSS')?>">Ок</button>
    </form>
  </div>
</aside>