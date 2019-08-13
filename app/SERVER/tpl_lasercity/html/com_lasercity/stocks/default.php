<?php defined('_JEXEC') or die;
$sef = LangHelper::getCurrentSef();
$input = JFactory::$application->input;

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


$language = JFactory::$language->getTag();

$search = addslashes($input->getString('search'));
$url['page'] = null;
if ($search != null) {
    $url['search'] = $search;
}
function getURL($page, $url)
{
    $url['page'] = $page;
    $result = array();
    foreach ($url as $key => $item) {
        $result[] = $key . '=' . $item;
    }
    return '?' . implode('&', $result);
}

/*echo '<pre>';
print_r($this->items);
echo '</pre>';*/

ContentLoader::addScript('stocks');

ContentLoader::addPopup('clinics-popup');


$db = JFactory::getDbo();

?>
<main>
  <section class="stocks__filters filters" aria-labelledby="Filter">
    <h2 class="visually-hidden" id="Filter">Фильтр акций</h2>
    <div class="filters__wrapper">
      <form class="filters__salon-filter">
        <div class="filters__name-wrapper">
          <input class="filters__name" type="search" autocomplete="off" aria-label="Поиск по названию салона" placeholder="Название салона">
          <div class="filters__name-autocomplete autocomplete">
            <ul class="autocomplete__list">
              <li class="autocomplete__item">
                <a href="#">Люменис на м Дарница</a>
              </li>
              <li class="autocomplete__item">
                <a href="#">Люменис на м Дарница</a>
              </li>
              <li class="autocomplete__item">
                <a href="#">Люменис на м Дарница</a>
              </li>
              <li class="autocomplete__item">
                <a href="#">Люменис на м Дарница</a>
              </li>
            </ul>
            <p class="autocomplete__results">Найдено
              <output>17</output>
              результатов
            </p>
          </div>
        </div>
        <div class="filters__place-wrapper">
          <input class="filters__place" type="search" autocomplete="off" aria-label="Поиск по местонахождению" placeholder="Местонахождение">
          <button class="filters__place-button" type="button" title="Открыть модальное окно выбора района" aria-label="Открыть модальное окно выбора района" data-set-modal-value="multiPopupPlace"><span class="visually-hidden">Открыть модальное окно выбора района</span></button>
          <div class="filters__place-autocomplete autocomplete">
            <ul class="autocomplete__list">
              <li class="autocomplete__item">
                <a href="#">Люменис на м Дарница</a>
              </li>
              <li class="autocomplete__item">
                <a href="#">Люменис на м Дарница</a>
              </li>
              <li class="autocomplete__item">
                <a href="#">Люменис на м Дарница</a>
              </li>
              <li class="autocomplete__item">
                <a href="#">Люменис на м Дарница</a>
              </li>
            </ul>
            <p class="autocomplete__results">Найдено
              <output>17</output>
              результатов
            </p>
          </div>
        </div>
      </form>
      <div class="filter-desktop">
        <div class="filter-desktop__zona" data-set-modal-value="multiPopupZona">
          <p class="filter-desktop__zona-title">Зоны процедуры</p>
          <button class="filter-desktop__zona-button" type="button" title="Открыть модальное окно выбора зоны процедуры" aria-label="Открыть модальное окно выбора зоны процедуры">
            <span class="visually-hidden">Открыть фильтр</span>
            <svg width="13" height="6" aria-hidden="true">
              <path d="M1069.01,43l6.49,1.074L1081.99,43,1075.5,49Z" transform="translate(-1069 -43)"></path>
            </svg>
          </button>
        </div>
        <div class="filter-desktop__aparat" data-set-modal-value="multiPopupAparat">
          <p class="filter-desktop__aparat-title">Аппарат</p>
          <button class="filter-desktop__aparat-button" type="button" title="Открыть модальное окно выбора аппарата" aria-label="Открыть модальное окно выбора аппарата">
            <span class="visually-hidden">Открыть фильтр</span>
            <svg width="13" height="6" aria-hidden="true">
              <path d="M1069.01,43l6.49,1.074L1081.99,43,1075.5,49Z" transform="translate(-1069 -43)"></path>
            </svg>
          </button>
        </div>
        <div class="filter-desktop__comfort" data-set-modal-value="multiPopupComfort">
          <p class="filter-desktop__comfort-title">Комфорт</p>
          <button class="filter-desktop__comfort-button" type="button" title="Открыть модальное окно выбора комфорта" aria-label="Открыть модальное окно выбора комфорта">
            <span class="visually-hidden">Открыть фильтр</span>
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
          <button class="filters__sorting-button" type="button" title="Открыть список фильтров" aria-label="Открыть список фильтров" data-set-modal-value="multiPopupPlace">Фильтр</button>
          <output>123 салона</output>
        </p>
        <div class="filters__type-sort">
          <b class="filters__sort-name">
            <span class="filters__sort-name--mob">Сортировка</span><span class="filters__sort-name--pc">Наша рекомендация</span>
          </b>
          <div class="filters__type-sort-wrapper">
            <ul class="filters__type-sort-list">
              <li class="filters__type-sort-item">
                По рейтингу
                <a class="filters__type-sort-item-button button-corner" href="#" title="Отлифльтровать по рейтингу" aria-label="Отлифльтровать по рейтингу">
                  <span class="visually-hidden">Отлифльтровать по рейтингу</span>
                </a>
              </li>
              <li class="filters__type-sort-item">
                По количеству отзывов
                <a class="filters__type-sort-item-button button-corner" href="#" title="Отлифльтровать по количеству отзывов" aria-label="Отлифльтровать по количеству отзывов">
                  <span class="visually-hidden">Отлифльтровать по количеству отзывов</span>
                </a>
              </li>
              <li class="filters__type-sort-item">
                По количеству<br> просмотров
                <a class="filters__type-sort-item-button button-corner" href="#" title="Отлифльтровать по количеству просмотров" aria-label="Отлифльтровать по количеству просмотров">
                  <span class="visually-hidden">Отлифльтровать по количеству просмотров</span>
                </a>
              </li>
            </ul>
            <button class="filters__type-sort-button button-corner" type="button" title="Открыть список сортировок" aria-label="Открыть список сортировок"><span class="visually-hidden">Открыть список сортировок</span>
            </button>
          </div>
        </div>
        <button class="filters__map-button" type="button" title="Открыть карту салонов" aria-label="Открыть карту салонов">Карта</button>
      </div>
    </div>
  </section>
  <nav class="breadcrumbs">
      <ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
          <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="<?=$current_sef?>"><span itemprop="name"><?=JText::_('COM_LASERCITY_ALL_GENERAL')?></span></a>
              <meta itemprop="position" content="1" />
          </li>
          <li class="breadcrumbs__item breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="#"><span itemprop="name"><?=JText::_('COM_LASERCITY_ALL_STOCKS')?> в <?=$obj_city->title?></span></a>
              <meta itemprop="position" content="2" />
          </li>
      </ol>
  </nav>
  <section class="stocks salons" aria-labelledby="StocksInKiev">
    <h1 class="salons__title" id="StocksInKiev">Акции лазерная эпиляции в <?=$obj_city->title?></h1>
    <div class="salons__choose">
      <div class="salons__choose-container">
        <div class="salons__choose-wrapper">
          <p class="salons__choose-title">По запросу:</p>
          <ul class="salons__choose-list">
            <li class="salons__choose-item">
              Верхняя губа
              <a href="#" class="salons__choose-del button-cross" title="Убрать элемент из списка фильтров" aria-label="Убрать элемент из списка фильтров">
                <span class="visually-hidden">Убрать из списка</span>
              </a>
            </li>
            <li class="salons__choose-item">
              Lumenis light sheer DESIRE
              <a href="#" class="salons__choose-del button-cross" title="Убрать элемент из списка фильтров" aria-label="Убрать элемент из списка фильтров">
                <span class="visually-hidden">Убрать из списка</span>
              </a>
            </li>
            <li class="salons__choose-item">
              Арсенальная
              <a href="#" class="salons__choose-del button-cross" title="Убрать элемент из списка фильтров" aria-label="Убрать элемент из списка фильтров">
                <span class="visually-hidden">Убрать из списка</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <p class="salons__choose-results">Найдено:
        <output>456</output>
        салонов <span class="salons__choose-results--pc"> в <?=$obj_city->title?></span></p>
    </div>
    <div class="stocks__wrapper">
      <div class="stocks__slider stock-slider slider">
        <ul class="slider__list" id="load_more_item">
            <?php foreach ($this->items['stocks'] as $stock): $stock->image = deleteFormat($stock->image) ?>
              <li class="slider__item">
                <div class="slider__item-wrapper">
                  <output class="stock-slider__item-discount">-<?=$stock->discount?>%</output>
                  <picture>
                      <source data-srcset="/<?= $stock->image ?>_262x198.webp 1x, /<?= $stock->image ?>_524x396.webp 2x" type="image/webp">
                      <img class="stock-slider__item-img lazyload" data-src="/<?= $stock->image ?>_262x198.jpg" data-srcset="/<?= $stock->image ?>_524x396.jpg 2x" loading="lazy" title="<?= $stock->title ?>" alt="<?= $stock->title ?>">
                  </picture>
                  <div class="stock-slider__item-wrapper">
                    <h4 class="stock-slider__item-title"><?=$stock->title?></h4>
                      <?php $count_addres = 0; $db->setQuery("SELECT f.city FROM `#__lasercity_affiliates` as f WHERE f.published='1' AND f.organization!=0 AND f.organization='".$stock->organization."' AND f.city='".$obj_city->id."'"); $count_addres = $db->loadObjectList();?>
                    <p class="stock-slider__item-address"><?=count($count_addres)?> <?=ContentLoader::replaceSuffix(count($count_addres),'adress')?> в <?= $obj_city->title ?></p>
                    <p class="stock-slider__item-footer">
                      <output class="stock-slider__item-purchased">(<?=ContentLoader::getWriteServiceTotal('stock',$stock->id)?>)</output>
                      <a class="stock-slider__item-look button" href="<?=$current_sef."stocks/".$stock->alias?>/"><?=JText::_('COM_LASERCITY_ALL_A_SEE')?></a>
                    </p>
                  </div>
                </div>
              </li>
            <?php endforeach;?>
            <?php if(!$this->items['stocks']): ?>
                <?=JText::_('COM_LASERCITY_STOCKS_TXT_NO_FIND')?>
            <?php endif; ?>
        </ul>
      </div>

        <?php if ($this->items['count'] > $this->items['limit']): ?>
            <?php
            $start_pagination = $this->items['page'] - 2 > 0 ? $this->items['page'] - 2 : 1;
            $end_pagination = $this->items['page'] + 2 <= $this->items['pages'] ? $this->items['page'] + 2 : $this->items['pages'];
            ?>
            <p class="salons__show-all-wrapper">
                <button class="salons__show-all" type="button" title="" aria-label=""><?=JText::_('COM_LASERCITY_ALL_SHOW_YET')?>
                    <output>8</output>
                    <?=JText::_('COM_LASERCITY_STOCKS_YET_TXT')?>
                </button>
            </p>
            <nav class="stocks__pagination pagination">
                <?php if ($this->items['page'] > 1): ?>
                    <?php if($this->items['page']==2): ?>
                        <a class="pagination__button pagination__button--prev button-corner" href="<?=$current_sef."stocks/"?>" title="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?></span></a>
                    <?php else: ?>
                        <a class="pagination__button pagination__button--prev button-corner" href="<?=$current_sef."stocks/".getURL($this->items['page'] - 1, $url) ?>" title="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?></span></a>
                    <?php endif;?>
                <?php endif; ?>
                <ul class="pagination__page-list">
                    <?php for ($i = $start_pagination; $i <= $end_pagination; $i++): ?>
                        <?php if($i==1): ?>
                            <li class="pagination__page-item<?= $i == $this->items['page'] ? ' pagination__page-item--current' : '' ?>"><a <?php if ($i != $this->items['page']): ?>href="<?=$current_sef."stocks/" ?>"<?php endif; ?>><?= $i ?></a></li>
                        <?php else: ?>
                            <li class="pagination__page-item<?= $i == $this->items['page'] ? ' pagination__page-item--current' : '' ?>"><a <?php if ($i != $this->items['page']): ?>href="<?=$current_sef."stocks/".getURL($i, $url) ?>"<?php endif; ?>><?= $i ?></a></li>
                        <?php endif;?>
                    <?php endfor; ?>
                </ul>
                <?php if ($this->items['page'] < $this->items['pages']): ?>
                    <a class="pagination__button pagination__button--next button-corner" href="<?=$current_sef."stocks/".getURL($this->items['page'] + 1, $url) ?>" title="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_NEXT')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_NEXT')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_NEXT')?></span></a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
  </section>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        //Pagination
        plugins.loadMore(
            '.salons__show-all',
            'https://lasercity.com.ua/?option=com_lasercity&task=loadMoreResult',
            '.stocks__pagination',
            '#load_more_item');
        
    });
</script>