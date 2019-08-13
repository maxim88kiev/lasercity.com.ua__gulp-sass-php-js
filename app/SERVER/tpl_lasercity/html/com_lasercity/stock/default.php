<?php defined('_JEXEC') or die;

ContentLoader::addScript('stock');
ContentLoader::addPopup('popup-recording');
ContentLoader::addPopup('popup-question');

$filters_list = Search::getFiltersList(array('services', 'apparatus'));
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

$link_mok = $current_sef."stocks/{$city_alias}/";

/*echo '<pre>';
print_r($this->item);
echo '</pre>';*/

$stocks = $this;
?>
<main>
  <nav class="breadcrumbs breadcrumbs--border">
      <ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
          <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="<?=$current_sef?>"><span itemprop="name"><?=JText::_('COM_LASERCITY_ALL_GENERAL')?></span></a>
              <meta itemprop="position" content="1" />
          </li>
          <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="<?=$link_mok?>"><span itemprop="name"><?=JText::_('COM_LASERCITY_ALL_STOCKS')?> <?=$obj_city->title?></span></a>
              <meta itemprop="position" content="2" />
          </li>
          <li class="breadcrumbs__item breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="#"><span itemprop="name"><?= $this->item->title ?></span></a>
              <meta itemprop="position" content="3" />
          </li>
      </ol>
  </nav>
  <section class="stock" aria-labelledby="SalonStock">
    <h1 class="stock__title" id="SalonStock"><?=$this->item->title?></h1>
    <div class="stock__wrapper">
      <div class="stock__slider slider">
        <ul class="slider__list">

                <?php if(!empty($this->item->images) && $this->item->images!="null"): $images=json_decode($this->item->images,true) ?>

                    <?php if(!empty($images)): ?>
                        <?php foreach ($images as $img): ?>
                            <?php if(!empty($img)): $img = deleteFormat($img) ?>
                              <li class="slider__item">
                                <div class="slider__item-wrapper">
                                  <picture>
                                      <source srcset="/<?= $img ?>_798x367.webp 1x, /<?= $img ?>_1596x734.webp 2x" media="(min-width: 1200px)" type="image/webp">
                                      <source srcset="/<?= $img ?>_320x150.webp 1x, /<?= $img ?>_640x300.webp 2x" type="image/webp">
                                      <source srcset="/<?= $img ?>_798x367.jpg 1x, /<?= $img ?>_1596x734.jpg 2x" media="(min-width: 1200px)">
                                      <img class="stock__img" data-src="/<?= $img ?>_320x150.jpg" data-srcset="/<?= $img ?>_640x300.jpg 2x" loading="lazy" title="<?= $this->item->title ?>" alt="<?= $this->item->title ?>">
                                  </picture>
                                </div>
                              </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="slider__item">
                            <div class="slider__item-wrapper">
                                <picture>
                                    <source srcset="https://placehold.it/798x367/red.wepb 1x, https://placehold.it/1596x734/red.wepb 2x" media="(min-width: 1200px)" type="image/webp">
                                    <source srcset="https://placehold.it/320x150/red.wepb 1x, https://placehold.it/640x300/red.wepb 2x" type="image/webp">
                                    <source srcset="https://placehold.it/798x367/red.jpg 1x, https://placehold.it/1596x734/red.wepb 2x" media="(min-width: 1200px)">
                                    <img class="stock__img" src="https://placehold.it/320x150/red.jpg" srcset="https://placehold.it/640x300/red.jpg 2x" loading="lazy" title="" alt="">
                                </picture>
                            </div>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="slider__item">
                        <div class="slider__item-wrapper">
                            <picture>
                                <source srcset="https://placehold.it/798x367/red.wepb 1x, https://placehold.it/1596x734/red.wepb 2x" media="(min-width: 1200px)" type="image/webp">
                                <source srcset="https://placehold.it/320x150/red.wepb 1x, https://placehold.it/640x300/red.wepb 2x" type="image/webp">
                                <source srcset="https://placehold.it/798x367/red.jpg 1x, https://placehold.it/1596x734/red.wepb 2x" media="(min-width: 1200px)">
                                <img class="stock__img" src="https://placehold.it/320x150/red.jpg" srcset="https://placehold.it/640x300/red.jpg 2x" loading="lazy" title="" alt="">
                            </picture>
                        </div>
                    </li>
                <?php endif; ?>
        </ul>
        <div class="stock__slider-nav">
          <button class="slider__button slider__button--prev" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_PREV')?></span>
            <svg width="7" height="62" aria-hidden="true">
              <path d="M296.643,1188.99l3.325-30.99-3.325-30.99L304,1158Z" transform="translate(-296.656 -1127)"></path>
            </svg>
          </button>
          <p class="stock__slider-toggles">
              <?php if(!empty($this->item->images) && $this->item->images!="null"): $images=json_decode($this->item->images,true) ?>
                 <?php $i=0; foreach ($images as $img): ?>
                      <button class="stock__slider-toggle <?=(($i==0) ? ' stock__slider-toggle--current' : '')?>" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_CIRCLE')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_CIRCLE')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_CIRCLE')?></span></button>
                  <?php $i++; endforeach; ?>
              <?php endif; ?>
          </p>
          <button class="slider__button slider__button--next" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SLIDER_BUTTON_NEXT')?></span>
            <svg width="7" height="62" aria-hidden="true">
              <path d="M296.643,1188.99l3.325-30.99-3.325-30.99L304,1158Z" transform="translate(-296.656 -1127)"></path>
            </svg>
          </button>
        </div>
      </div>
      <div class="stock__aside">
        <ul class="stock__advantages-list">
          <li class="stock__advantages-item">
            <b><?=JText::_('COM_LASERCITY_ALL_TXT_SKIDKA')?></b>
            <p class="stock__advantages-wrapper">
              <output>-<?=$this->item->discount?>%</output>
            </p>
          </li>
          <li class="stock__advantages-item stock__advantages-item--saving">
            <b><?=JText::_('COM_LASERCITY_ALL_TXT_EKONOMIA')?></b>
            <p class="stock__advantages-wrapper">
              <output class="saving--old">1000 грн.</output>
              <output class="saving--new">567 грн</output>
            </p>
          </li>
          <li class="stock__advantages-item">
            <b><?=JText::_('COM_LASERCITY_ALL_TXT_WRITER')?></b>
            <p class="stock__advantages-wrapper">
              <output><?=ContentLoader::getWriteServiceTotal('stock',$this->item->id)?></output>
            </p>
          </li>
        </ul>
        <div class="stock__timer" id="clockdiv">
          <div class="stock__timer-wrapper">
            <b class="stock__timer-title"><?=JText::_('COM_LASERCITY_STOCKS_END')?>:</b>
            <ul class="stock__timer-list">
              <li class="stock__timer-item stock__timer-item--day">
                <output>01</output>
                <?=JText::_('COM_LASERCITY_STOCKS_DAY')?>
              </li>
              <li class="stock__timer-item stock__timer-item--hour">
                <output>04</output>
                  <?=JText::_('COM_LASERCITY_STOCKS_HOUR')?>
              </li>
              <li class="stock__timer-item stock__timer-item--min">
                <output>15</output>
                  <?=JText::_('COM_LASERCITY_STOCKS_MINUT')?>
              </li>
              <li class="stock__timer-item stock__timer-item--sec">
                <output>46</output>
                  <?=JText::_('COM_LASERCITY_STOCKS_SEKUND')?>
              </li>
            </ul>
          </div>
          <p class="stock__timer-end"><?=JText::_('COM_LASERCITY_STOCKS_START')?> <?=date("d.m.Y",strtotime($stocks->item->date_added))?> по <?=date("d.m.Y",strtotime($stocks->item->date_remove))?></p>
        </div>
      </div>
    </div>
    <section class="stock__description" aria-labelledby="StockDescription">
      <h2 class="stock__description-title" id="StockDescription"><?=JText::_('COM_LASERCITY_ALL_DESCRIPTION')?></h2>
      <p class="stock__description-text"><?=$this->item->description?></p>
    </section>
    <section class="stock__conditions" aria-labelledby="StockConditions">
      <h2 class="stock__conditions-title" id="StockConditions"><?=JText::_('COM_LASERCITY_ALL_RULS')?></h2>
      <p class="stock__conditions-text"><?=$this->item->conditions?></p>
    </section>
    <section class="stock__organizers" aria-labelledby="StockOrganizers">
      <h2 class="stock__organizers-title" id="StockOrganizers"><?=JText::_('COM_LASERCITY_ALL_ORGANIZATOR')?></h2>
      <ul class="stock__organizers-list branches-list">
          <?php if((!empty($this->item->affiliate_id) && $this->item->affiliate_id!='null')): ?>

              <?php

          $stokModel = new lasercityModelStock;
          //echo var_dump(json_decode($this->item->affiliate_id,true));
          $array_aff = "'".implode("','",json_decode($this->item->affiliate_id,true))."'";
          $affiliates = $stokModel->getItemAffiliate($array_aff);


          //echo $array_aff;
          /*echo '<pre>';
          print_r($affiliates);
          echo '</pre>';*/

          ?>

          <?php $i=-1; foreach ($affiliates as $affiliate): $i++;?>
        <li class="branches-list__branch<?=($i>2) ? ' box-hidden' : ''?>" data-main_image="/<?=$affiliate->main_image?>">
          <div class="branches-list__information">
            <div class="branches-list__contacts">
              <h4 class="branches-list__title"><a href="#"><?=$affiliate->title?></a></h4>
              <div class="branches-list__address salon-address">
                <p class="salon-address__underground">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="337.5 232.3 125 85.9" aria-hidden="true">
                    <polygon points="453.9,306.2 424.7,232.3 400,275.5 375.4,232.3 346.1,306.2 337.5,306.2 337.5,317.4 381.7,317.4 381.7,306.2 375.1,306.2 381.5,287.8 400,318.2 418.5,287.8 424.9,306.2 418.3,306.2 418.3,317.4 462.5,317.4 462.5,306.2 "></polygon>
                  </svg>
                    <?=$affiliate->metro?>
                </p>
                <p class="salon-address__street"><?=$affiliate->location?>, <?=$affiliate->number_home?>, <?=$affiliate->district?></p>
              </div>
              <div class="branches-list__schedule">
                <div class="branches-list__phones phonebook">
                  <div class="phonebook__wrapper">
                    <ul class="phonebook__list">
                    <?php foreach ($affiliate->phones as $phone): ?>
                      <li class="phonebook__item">
                        <a><?=$phone?></a>
                      </li>
                    <?php endforeach; ?>
                    </ul>
                    <button class="phonebook__button button-corner" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SEE_PHONE')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SEE_PHONE')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SEE_PHONE')?></span></button>
                    <div class="phonebook__popup">
                      <p class="phonebook__popup-text"><?=JText::_('COM_LASERCITY_STOCKS_SVIASI')?></p>
                      <a class="phonebook__popup-link" href="#"><?=JText::_('COM_LASERCITY_STOCKS_RAITING')?></a>
                      <ul class="phonebook__list">
                          <?php foreach ($affiliate->phones as $phone): ?>
                            <li class="phonebook__popup-item">
                              <a href="tel:<?=trim(preg_replace('/[^0-9]/', '',$phone))?>"><?=$phone?></a>
                              <button class="phonebook__popup-show-number" data-affiliate="<?=$affiliate->id?>"><?=JText::_('COM_LASERCITY_ALL_SHOW_ME')?></button>
                            </li>
                          <?php endforeach; ?>
                      </ul>
                      <button class="phonebook__popup-close button-cross" type="button" title="<?=JText::_('COM_LASERCITY_ALL_CLOSE_PHONE')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_CLOSE_PHONE')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_CLOSE_PHONE')?></span></button>
                    </div>
                  </div>
                </div>
                <div class="branches-list__workdays workdays">
                  <button class="workdays__button-open" type="button" title="<?=JText::_('COM_LASERCITY_ALL_OPEN_GRAPHIK')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_OPEN_GRAPHIK')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_OPEN_GRAPHIK')?></span></button>
                  <p class="workdays__title"><?=JText::_('COM_LASERCITY_ALL_GRAPHIK_JOB')?></p>
                  <div class="workdays__list-wrapper">
                    <ul class="workdays__list">
                        <?php foreach ($affiliate->schedule as $schedule): ?>
                          <li class="workday__item">
                              <?=$schedule['days']?>: <span><?=$schedule['times']?></span>
                          </li>
                        <?php endforeach;?>
                    </ul>
                    <button class="workday__button-close button-cross" type="button" title="<?=JText::_('COM_LASERCITY_ALL_CLOSE_GRAPHIK_JOB')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_CLOSE_GRAPHIK_JOB')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_CLOSE_GRAPHIK_JOB')?></span></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="branches-list__aside">
              <a class="branches-list__rating rating-salon" href="<?=$current_sef."clinics/{$affiliate->alias}/#SalonRation"?>">
                <div class="rating-salon__point-wrapper">
                  <output class="rating-salon__point">
                      <?php
                          $raiting_array = ContentLoader::getRaiting($affiliate->id);
                          echo $raiting_array['raiting'];
                      ?>
                  </output>
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
              <a href="https://maps.google.com/?q=<?=$affiliate->coordinate?>" target="_blank" class="branches-list__map button-onmap" title="<?=JText::_('COM_LASERCITY_ALL_OPEN_MAP')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_OPEN_MAP')?>">
                  <?=JText::_('COM_LASERCITY_ALL_TXT_MAP_GOOGLE')?>
              </a>
              <button class="branches-list__question" data-affiliate="<?=$affiliate->id?>" data-set-modal-value="question"><?=JText::_('COM_LASERCITY_ALL_TXT_QUESTION')?></button>
            </div>
          </div>
          <button class="branches-list__button button" data-affiliate="<?=$affiliate->id?>" data-stock="<?=$this->item->id?>" data-set-modal-value="recording"><?=JText::_('COM_LASERCITY_ALL_TXT_WRITERING')?></button>
        </li>
          <?php endforeach; ?>
          <?php endif; ?>
      </ul>
      <div class="stock__organizers-more-wrapper">
        <p class="stock__organizers-more">
            <?=JText::_('COM_LASERCITY_ALL_SHOW_ALL')?>
          <button class="stock__organizers-button button-corner" type="button" title="<?=JText::_('COM_LASERCITY_STOCKS_SALONS')?>" aria-label="<?=JText::_('COM_LASERCITY_STOCKS_SALONS')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_STOCKS_SALONS')?></span></button>
        </p>
        <p class="stock__organizers-results">
          <output><?=@count($affiliates)?></output>
            <?=ContentLoader::replaceSuffix(@count($affiliates),'adress')?> в <?=$obj_city->title?>
        </p>
      </div>
    </section>
  </section>
  <section class="stock-offer" aria-labelledby="interestingOffers">
    <h2 class="stock-offer__title title-lines" id="interestingOffers"><?=JText::_('COM_LASERCITY_STOCKS_INTRESTIN')?></h2>
    <div class="stock-offer__slider stock-slider slider">
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
                        AND s.id!='".$stocks->item->id."' 
                        AND s.city_id='".$obj_city->id."' 
                        AND UNIX_TIMESTAMP(NOW())>=UNIX_TIMESTAMP(s.date_added) 
			            AND UNIX_TIMESTAMP(NOW())<=UNIX_TIMESTAMP(s.date_remove) 
			            ORDER BY s.ordering ASC Limit 6");

          $stocks_slider = $db->loadObjectList();
          ?>
          <?php foreach ($stocks_slider as $stock): $stock->image = deleteFormat($stock->image) ?>
            <li class="slider__item">
              <div class="slider__item-wrapper">
                <output class="stock-slider__item-discount">-<?= $stock->discount ?>%</output>
                <picture>
                    <source srcset="/<?= $stock->image ?>_262x198.webp 1x, /<?= $stock->image ?>_524x396.webp 2x" type="image/webp">
                    <img class="stock-slider__item-img lazyload" data-src="/<?= $stock->image ?>_262x198.jpg" data-srcset="/<?= $stock->image ?>_524x396.jpg 2x" loading="lazy" title="<?= $stock->value ?>" alt="<?= $stock->value ?>">
                </picture>
                <div class="stock-slider__item-wrapper">
                  <h4 class="stock-slider__item-title"><?= $stock->value ?></h4>
                    <?php $count_addres = 0;
                    $db->setQuery("SELECT f.city FROM `#__lasercity_affiliates` as f WHERE f.published='1' AND f.organization!=0 AND f.organization='" . $stock->organization . "' AND f.city='" . $obj_city->id . "'");
                    $count_addres = $db->loadObjectList(); ?>
                    <p class="stock-slider__item-address"><?= count($count_addres) ?> <?=ContentLoader::replaceSuffix(count($count_addres),'adress')?> в <?= $obj_city->title ?></p>
                  <p class="stock-slider__item-footer">
                    <output class="stock-slider__item-purchased">(<?=ContentLoader::getWriteServiceTotal('stock',$stock->id)?>)</output>
                    <a class="stock-slider__item-look button" href="<?=$current_sef."stocks/".$stock->alias?>/"><?=JText::_('COM_LASERCITY_ALL_A_SEE')?></a>
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
</main>
<style>
    .box-hidden{
        display: none;
    }
    .box-active{
        display: block;
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

</style>
<script>

    document.addEventListener('DOMContentLoaded', function() {

        jQuery(function ($) {

        });

        function getTimeRemaining(endtime) {
            var t = Date.parse(endtime) - Date.parse(new Date());
            var seconds = Math.floor((t / 1000) % 60);
            var minutes = Math.floor((t / 1000 / 60) % 60);
            var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
            var days = Math.floor(t / (1000 * 60 * 60 * 24));
            return {
                'total': t,
                'days': days,
                'hours': hours,
                'minutes': minutes,
                'seconds': seconds
            };
        }

        function initializeClock(id, endtime) {
            var clock = document.getElementById(id);
            var daysSpan = clock.querySelector('.stock__timer-item--day output');
            var hoursSpan = clock.querySelector('.stock__timer-item--hour output');
            var minutesSpan = clock.querySelector('.stock__timer-item--min output');
            var secondsSpan = clock.querySelector('.stock__timer-item--sec output');

            function updateClock() {
                var t = getTimeRemaining(endtime);

                daysSpan.innerHTML = t.days;
                hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
                minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
                secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

                if (t.total <= 0) {
                    clearInterval(timeinterval);
                }
            }

            updateClock();
            var timeinterval = setInterval(updateClock, 1000);
        }

        //var deadline = "January 01 2018 00:00:00 GMT+0300"; //for Ukraine
        var deadline = "<?=date("Y-m-d",strtotime($stocks->item->date_remove))?>";
        //var deadline = new Date(Date.parse(new Date()) + 15 * 24 * 60 * 60 * 1000); // for endless timer
        initializeClock('clockdiv', deadline);
    });
</script>