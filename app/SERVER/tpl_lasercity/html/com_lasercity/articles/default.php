<?php defined('_JEXEC') or die;
$sef = LangHelper::getCurrentSef();
$input = JFactory::$application->input;



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

//echo '<pre>';
//print_r($this->items);
//echo '</pre>';

ContentLoader::addScript('articles');

?>

<main>
  <section class="articles-filters filters" aria-labelledby="ArticlesSearch">
    <h1 class="filters__title" id="ArticlesSearch"><?=JText::_('COM_LASERCITY_ARTICLE_H1_1')?></h1>
    <div class="filters__wrapper">
      <form class="filters__salon-filter" method="post">
        <div class="articles-filters__wrapper">
          <input class="filters__name" value="<?= $search ?>" type="search" autocomplete="off" name="search" aria-label="<?=JText::_('COM_LASERCITY_ALL_FIND_KEY')?>" placeholder="<?=JText::_('COM_LASERCITY_ALL_FIND_KEY')?>">
          <div class="articles-filters__autocomplete autocomplete">
            <ul class="autocomplete__list">
            </ul>
            <p class="autocomplete__results"><?=JText::_('COM_LASERCITY_ALL_RESULT_FIND')?>
              <output></output> <?=JText::_('COM_LASERCITY_ALL_RESULT_TXT')?>
            </p>
          </div>
          <?php if ($search != null): ?>
            <a class="filters__button-clear button-cross" href="#" title="<?=JText::_('COM_LASERCITY_ALL_CLEAR_FIND')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_CLEAR_FIND')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_CLEAR_FIND')?></span></a>
          <?php endif; ?>
        </div>
        <button class="filters__button button"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ARTICLE_BUTTON_1')?></span></button>
      </form>
    </div>
  </section>
  <nav class="breadcrumbs">
      <ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
          <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="<?= $current_sef ?>"><span itemprop="name"><?= JText::_('COM_LASERCITY_ALL_GENERAL') ?></span></a>
              <meta itemprop="position" content="1"/>
          </li>
          <li class="breadcrumbs__item breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="#"><span itemprop="name"><?= JText::_('COM_LASERCITY_ARTICLE_LI_1') ?></span></a>
              <meta itemprop="position" content="2"/>
          </li>
      </ol>
  </nav>
  <section class="articles" aria-labelledby="UsefulArticles">
    <h2 class="visually-hidden" id="UsefulArticles"><?=JText::_('COM_LASERCITY_ARTICLE_H2_1')?></h2>
    <ul class="articles__article-list list__block" id="load_more_item">

<!--        --><?php //var_dump($this->items['articles']); ?>

        <?php foreach ($this->items['articles'] as $article):
            $image = @deleteFormat($article->main_image);
            ?>
          <li class="articles__article-item">
            <article class="articles__article articles__article--block">
              <picture>
                <source data-srcset="/<?= $image ?>_262x198.webp 1x, /<?= $image ?>_524x396.webp 2x" media="(min-width: 1200px)" type="image/webp">
                <img class="articles-index__image lazyload" data-src="/<?= $image ?>_262x198.jpg" data-srcset="/<?= $image ?>_524x396.jpg 2x" loading="lazy" title="<?=JText::_('COM_LASERCITY_ALL_IMG_TITLE')?>: <?= $article->title ?>" alt="<?=JText::_('COM_LASERCITY_ALL_IMG_TITLE')?>: <?= $article->title ?>">
              </picture>
              <h3 class="articles__article-title article__text"><?= $article->title ?></h3>
              <div class="articles__article-wrapper">
                <p class="articles__article-text"><?= $article->description ?></p>
                <div class="articles__article-links-wrapper">
                  <a class="articles__article-button button" href="<?= $current_sef."articles/".$article->alias ?>"><?=JText::_('COM_LASERCITY_ALL_SHOW_GO')?></a>
                  <ul class="articles__article-links">
                    <li class="articles__article-links-item articles__article-links-item--facebook">
                      <output><?= $article->likes ?></output>
                    </li>
                    <li class="articles__article-links-item articles__article-links-item--view">
                      <output><?= $article->see ?></output>
                    </li>
                  </ul>
                </div>
              </div>
            </article>
          </li>
        <?php endforeach; ?>
    </ul>





      <?php

      if ($this->items['count'] > $this->items['limit']): ?>
          <?php
          $start_pagination = $this->items['page'] - 2 > 0 ? $this->items['page'] - 2 : 1;
          $end_pagination = $this->items['page'] + 2 <= $this->items['pages'] ? $this->items['page'] + 2 : $this->items['pages'];
          ?>
          <p class="salons__show-all-wrapper">
              <button class="salons__show-all" type="button" title="" aria-label=""><?=JText::_('COM_LASERCITY_ALL_SHOW_YET')?>
                  <output><?php
                      echo $this->items['count'] - $this->items['limit'];
                      ?></output>
                  <?=JText::_('COM_LASERCITY_ARTICLE_SHOW_YET')?>
              </button>
          </p>
        <nav class="articles__pagination pagination">
            <?php if ($this->items['page'] > 1): ?>
                <?php if($this->items['page']==2): ?>
                    <a class="pagination__button pagination__button--prev button-corner" href="<?=$current_sef ?>articles/" title="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?></span></a>
                <?php else: ?>
                    <a class="pagination__button pagination__button--prev button-corner" href="<?=$current_sef ?>articles/<?= getURL($this->items['page'] - 1, $url) ?>" title="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?></span></a>
                <?php endif;?>
            <?php endif; ?>
          <ul class="pagination__page-list">
              <?php for ($i = $start_pagination; $i <= $end_pagination; $i++): ?>
                  <?php if($i==1): ?>
                    <li class="pagination__page-item<?= $i == $this->items['page'] ? ' pagination__page-item--current' : '' ?>"><a <?php if ($i != $this->items['page']): ?>href="<?=$current_sef ?>articles/"<?php endif; ?>><?= $i ?></a></li>
                  <?php else: ?>
                    <li class="pagination__page-item<?= $i == $this->items['page'] ? ' pagination__page-item--current' : '' ?>"><a <?php if ($i != $this->items['page']): ?>href="<?=$current_sef ?>articles/<?= getURL($i, $url) ?>"<?php endif; ?>><?= $i ?></a></li>
                  <?php endif;?>
              <?php endfor; ?>
          </ul>
            <?php if ($this->items['page'] < $this->items['pages']): ?>
              <a class="pagination__button pagination__button--next button-corner" href="<?=$current_sef ?>articles/<?= getURL($this->items['page'] + 1, $url) ?>" title="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_NEXT')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_NEXT')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_NEXT')?></span></a>
            <?php endif; ?>
        </nav>
      <?php endif; ?>
  </section>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        //Pagination
        plugins.loadMore(
            '.salons__show-all',
            'https://lasercity.com.ua/?option=com_lasercity&task=loadMoreArticles',
            '.articles__pagination',
            '#load_more_item');

    });
</script>