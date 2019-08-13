<?php defined('_JEXEC') or die;
ContentLoader::addScript('faq');

ContentLoader::addPopup('faq-item_popup');


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

?>

<main>
  <div class="faq-filter">
    <ul class="faq-filter__list">
      <li class="faq-filter__item faq-filter__item--curent"><a href="#"><?=JText::_('COM_LASERCITY_FAQ_QUESTION_ANSVER_POPULAR')?></a></li>
      <li class="faq-filter__item"><a href="#"><?=JText::_('COM_LASERCITY_FAQ_QUESTION_ANSVER_NEW')?></a></li>
      <li class="faq-filter__item"><a href="#"><?=JText::_('COM_LASERCITY_FAQ_QUESTION_ANSVER_GOT')?></a></li>
    </ul>
  </div>
  <nav class="breadcrumbs">
      <ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
          <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="<?=$current_sef?>"><span itemprop="name"><?=JText::_('COM_LASERCITY_ALL_GENERAL')?></span></a>
              <meta itemprop="position" content="1" />
          </li>
          <li class="breadcrumbs__item breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="#"><span itemprop="name"><?=JText::_('COM_LASERCITY_FAQ_QUESTION_AND_ANSVER')?></span></a>
              <meta itemprop="position" content="2" />
          </li>
      </ol>
  </nav>
  <section class="faq-search" aria-labelledby="QuestionSearch">
    <h1 class="faq-search__title" id="QuestionSearch"><?=JText::_('COM_LASERCITY_FAQ_QUESTION_AND_ANSVER')?></h1>
    <form class="faq-search__form" method="post">
      <input class="faq-search__input" type="search" name="questions" aria-label="<?=JText::_('COM_LASERCITY_FAQ_QUESTION_YOU')?>" placeholder="<?=JText::_('COM_LASERCITY_FAQ_QUESTION_YOU')?>">
      <div class="faq-search__autocomplete autocomplete">
        <ul class="autocomplete__list">
        </ul>
        <p class="autocomplete__results"><?=JText::_('COM_LASERCITY_ALL_RESULT_FIND')?>
          <output>17</output>
            <?=JText::_('COM_LASERCITY_ALL_RESULT_TXT')?>
        </p>
      </div>
      <button class="faq-search__button" title="<?=JText::_('COM_LASERCITY_FAQ_FIND_QUESTION')?>" aria-label="<?=JText::_('COM_LASERCITY_FAQ_FIND_QUESTION')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_FAQ_FIND_QUESTION')?></span></button>
    </form>
  </section>
  <section class="faq" aria-labelledby="QuestionsList">
    <h2 class="visually-hidden" id="QuestionsList"><?=JText::_('COM_LASERCITY_FAQ_LIST_QUESTION')?></h2>
    <ul class="faq__list">
        <?php foreach ($this->items['faq'] as $item): ?>
          <li class="faq__item">
            <h3 class="faq__title"><?=$item->title?></h3>
            <div class="faq__item-wrapper">
              <ul class="faq__information-list">
                <li class="faq__information-item faq__information-item--data"><?=$item->date?></li>
                <li class="faq__information-item faq__information-item--followers">
                  <output>2</output>
                  <?=ContentLoader::replaceSuffix(2,'subscriber')?>
                </li>
                <li class="faq__information-item faq__information-item--answers">
                    <?php
                        $count_answer = ContentLoader::getAnswerTotal('question',$item->id);
                        echo empty($count_answer) ? JText::_('COM_LASERCITY_FAQ_DO_NOT_ANSVER') : $count_answer." ".ContentLoader::replaceSuffix($count_answer,'answer');
                    ?>
                </li>
              </ul>
              <p class="faq__text"><?=$item->description?></p>
              <a class="faq__full" href="<?=$current_sef."faq/".$item->alias?>"><?=JText::_('COM_LASERCITY_FAQ_READ_QUESTION')?></a>
            </div>
          </li>
        <?php endforeach; ?>
    </ul>
    <div class="faq__notfound-wrapper">
      <b class="faq__notfound-title"><?=JText::_('COM_LASERCITY_FAQ_DO_NOT_QUESTION')?></b>
      <button class="faq__notfound-button button" data-set-modal-value="discussion"><?=JText::_('COM_LASERCITY_ALL_TXT_QUESTION')?></button>
    </div>
  </section>
</main>
<aside class="page-aside faq-aside">
  <div class="mailing">
    <b class="mailing__slogan"><?=JText::_('COM_LASERCITY_ALL_WRITING_RSS')?></b>
    <form class="mailing__form" method="post">
      <p class="mailing__input-wrapper">
        <input class="mailing__input" type="text" autocomplete="off" name="mailing" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>">
      </p>
      <button class="mailing__button">Ок</button>
    </form>
  </div>
</aside>