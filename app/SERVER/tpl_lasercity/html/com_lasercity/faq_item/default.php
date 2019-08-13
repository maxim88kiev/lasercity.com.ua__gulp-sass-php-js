<?php defined('_JEXEC') or die;

ContentLoader::addScript('salon');
ContentLoader::addScript('faq_item');

ContentLoader::addPopup('faq-item_popup');


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

$url['page'] = null;

function getURL($page, $url)
{
    $url['page'] = $page;
    $result = array();
    foreach ($url as $key => $item) {
        $result[] = $key . '=' . $item;
    }
    return '?' . implode('&', $result);
}

?>

<?php defined('_JEXEC') or die;
//echo '<pre>';
//print_r($this->item);
//echo '</pre>';
?>
<main>
  <nav class="faq-discussion__breadcrumbs breadcrumbs">
    <ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
      <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" href="<?= $current_sef ?>"><span itemprop="name"><?= JText::_('COM_LASERCITY_ALL_GENERAL') ?></span></a>
        <meta itemprop="position" content="1"/>
      </li>
      <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" href="<?= $current_sef . "faq/" ?>"><span itemprop="name"><?= JText::_('COM_LASERCITY_FAQ_QUESTION_AND_ANSVER') ?></span></a>
        <meta itemprop="position" content="2"/>
      </li>
      <li class="breadcrumbs__item breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <a itemprop="item" href="javascript:void(0)"><span itemprop="name"><?= $this->item->title ?></span></a>
        <meta itemprop="position" content="3"/>
      </li>
    </ol>
  </nav>
  <section class="faq-discussion" aria-labelledby="QuestionAndDiscussion">
    <h1 class="faq-discussion-title" id="QuestionAndDiscussion"><?= $this->item->title ?></h1>
    <div class="faq-discussion-wrapper">
      <section class="faq-discussion__discussion" aria-labelledby="QuestionAndDiscuccion">
        <h2 class="visually-hidden" id="QuestionAndDiscuccion"><?= JText::_('COM_LASERCITY_FAQ_QUESTION_ANSVER_OBSUZD') ?></h2>
        <article class="faq-discussion__question">
          <h3 class="visually-hidden">Вопрос: </h3>
          <p class="faq-discussion__text"><?= $this->item->description ?></p>
          <div class="faq-discussion__information">
            <button class="faq-discussion__follow"><?= JText::_('COM_LASERCITY_ALL_SALON_ON_GOT_LETTER') ?></button>
            <p class="faq-discussion__followers">
              <output>12</output>
                <?= ContentLoader::replaceSuffix(12, 'subscriber') ?>
            </p>
            <p class="faq-discussion__autor">Yulya888 спросил 2 недели назад</p>
          </div>
        </article>
          <?php
          //имя автора
          $answer_name = (!empty(JFactory::getSession()->get('my_user_name')) ? JFactory::getSession()->get('my_user_name') : JText::_('COM_LASERCITY_ALL_SALON_GUESTS'));
          //аватар
          $answer_avatar = !empty(JFactory::getSession()->get('my_user_avatar')) ? '/' . deleteFormat(JFactory::getSession()->get('my_user_avatar')) : '';
          //ранг
          $answer_rank = JFactory::getSession()->get('my_user_rank')!="" ? JFactory::getSession()->get('my_user_rank') : 0;
          ?>

          <section class="faq-discussion__comments" aria-labelledby="FaqComments">
              <h3 class="visually-hidden" id="FaqComments"><?= JText::_('COM_LASERCITY_ALL_SALON_RVIEWSU_ABSUZH') ?></h3>
              <div class="faq-discussion__comments-wrapper">
                  <p class="faq-discussion__comments-count"><?= JText::_('COM_LASERCITY_ALL_SALON_RVIEWSU') ?>
                      <output><?=ContentLoader::getAnswerTotal('question',$this->item->id)?></output>
                  </p>
                  <a href="#FaqAddComment" class="faq-discussion__comments-button button"><?= JText::_('COM_LASERCITY_FORM_REVIEW_BNT_ANSW') ?></a>
              </div>
              <ul class="faq-discussion__comments-list comments">

                  <?php foreach ($this->answers['answers'] as $answer):

                      //имя автора
                      $answer->name = (!empty($answer->name) ? $answer->name : JText::_('COM_LASERCITY_ALL_SALON_GUESTS'));
                      //аватар
                      $answer->avatar = ($answer->avatar) ? deleteFormat($answer->avatar) : deleteFormat($answer->image);
                      //ранг
                      $answer->rank = ((isset($answer->rank) && $answer->rank!="") ? $answer->rank : 0);
                  ?>
                  <li class="comments__item">
                      <article class="faq-discussion__comment commemts__comment">
                          <h4 class="visually-hidden"><?= JText::_('COM_LASERCITY_FORM_REVIEW_COMENT') ?> - <?=$answer->name?></h4>
                          <div class="comments__autor">
                              <div class="comments__autor-wrapper">
                                  <div class="comments__autor-popup-wrapper">
                                      <p class="comments__autor-img-wrapper">
                                          <picture>
                                              <?php if(empty($answer->avatar)):?>
                                                  <source srcset="https://placehold.it/90x90/red.wepb 1x, https://placehold.it/180x180/red.wepb 2x" media="(min-width: 1200px)" type="image/webp">
                                                  <source srcset="https://placehold.it/45x45/red.wepb 1x, https://placehold.it/90x90/red.wepb 2x" type="image/webp">
                                                  <source srcset="https://placehold.it/90x90/red.img 1x, https://placehold.it/180x180/red.img 2x" media="(min-width: 1200px)">
                                                  <img class="comments__autor-image comments__autor-image--open-popup" src="https://placehold.it/45x45/red.jpg" srcset="https://placehold.it/90x90/fff.jpg 2x" loading="lazy" alt="Фотография автора" title="Фотография автора">
                                              <?php else: ?>
                                                  <source srcset="<?= $answer->avatar ?>_90x90.webp 1x, <?= $answer->avatar ?>_180x180.webp 2x" media="(min-width: 1200px)" type="image/webp">
                                                  <source srcset="<?= $answer->avatar ?>_45x45.webp 1x, <?= $answer->avatar ?>_90x90.webp 2x" type="image/webp">
                                                  <source srcset="<?= $answer->avatar ?>_90x90.webp 1x, <?= $answer->avatar ?>_180x180.webp 2x" media="(min-width: 1200px)" type="image/webp">
                                                  <img class="comments__autor-image comments__autor-image--open-popup" src="<?= $answer->avatar ?>_90x90.jpg" srcset="<?= $answer->avatar ?>_90x90.jpg 2x" loading="lazy" alt="<?=JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR')?>" title="<?=JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR')?>">
                                              <?php endif; ?>
                                          </picture>
                                      </p>
                                      <div class="comments__autor-popup">
                                          <div class="comments__autor-wrapper">
                                              <p class="comments__autor-img-wrapper">
                                                  <picture>
                                                      <?php if(empty($answer->avatar)):?>
                                                          <source srcset="https://placehold.it/90x90/red.wepb 1x, https://placehold.it/180x180/red.wepb 2x" type="image/webp">
                                                          <img class="comments__autor-image" src="https://placehold.it/45x45/red.jpg" srcset="https://placehold.it/90x90/fff.jpg 2x" loading="lazy" alt="<?=JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR')?>" title="<?=JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR')?>">
                                                      <?php else: ?>
                                                          <source srcset="<?= $answer->avatar ?>_90x90.webp 1x, <?= $answer->avatar ?>_180x180.webp 2x" type="image/webp">
                                                          <img class="comments__autor-image" src="<?= $answer->avatar ?>_90x90.jpg" srcset="<?= $answer->avatar ?>_180x180.jpg 2x" loading="lazy" alt="<?=JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR')?>" title="<?=JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR')?>">
                                                      <?php endif; ?>
                                                  </picture>
                                              </p>
                                              <div class="comments__autor-fact">
                                                  <cite class="comments__autor-name"><?=$answer->name?></cite>
                                                  <p class="comments__autor-city"><?=JText::_('COM_LASERCITY_ALL_SALON_TOWN_REVIEW')?> <?=$answer->value?></p>
                                                  <p class="comments__autor-rank">
                                                      <?php
                                                      switch ($answer->rank){
                                                          case 0: echo JText::_('COM_LASERCITY_ALL_SALON_NOVICHECK');
                                                              break;
                                                          case 1: echo JText::_('COM_LASERCITY_ALL_SALON_BUVALUI');
                                                              break;
                                                          case 2: echo JText::_('COM_LASERCITY_ALL_SALON_EXPERT');
                                                              break;
                                                      }
                                                      ?>
                                                  </p>
                                              </div>
                                          </div>
                                          <?php
                                            $answer->day_togeva = !empty($answer->day_togeva) ? $answer->day_togeva : 1;
                                          ?>
                                          <p class="comments__autor-experience"><?=JText::_('COM_LASERCITY_ALL_SALON_S_LASERCISY')." ".$answer->day_togeva." ".ContentLoader::replaceSuffix($answer->day_togeva,'day')?></p>
                                          <ul class="comments__autor-list">
                                              <li class="comments__autor-itme"><span><?=$answer->count_review?></span> <?=ContentLoader::replaceSuffix($answer->count_review,'review', true)?></li>
                                              <li class="comments__autor-itme"><span><?=$answer->count_question?></span> <?=ContentLoader::replaceSuffix($answer->count_review,'question', true)?></li>
                                              <li class="comments__autor-itme"><span><?=$answer->count_answer?></span> <?=ContentLoader::replaceSuffix($answer->count_review,'answer', true)?></li>
                                          </ul>
                                          <button class="comments__autor-popup-button button-cross" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?></span></button>
                                      </div>
                                  </div>
                                  <div class="comments__autor-fact">
                                      <cite class="comments__autor-name"><?=$answer->name?></cite>
                                      <p class="comments__autor-rank">
                                          <?php
                                          switch ($answer->rank){
                                              case 0: echo JText::_('COM_LASERCITY_ALL_SALON_NOVICHECK');
                                                  break;
                                              case 1: echo JText::_('COM_LASERCITY_ALL_SALON_BUVALUI');
                                                  break;
                                              case 2: echo JText::_('COM_LASERCITY_ALL_SALON_EXPERT');
                                                  break;
                                          }
                                          ?>
                                      </p>
                                      <time class="comments__autor-time"><?=date('d/m/Y',strtotime($answer->date_added))?></time>
                                  </div>
                              </div>
                              <div class="comments__rate">
                                  <button class="comments__button-functions" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SALON_OPEN_FUNC_MENU')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_OPEN_FUNC_MENU')?>">
                                      <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SALON_OPEN_FUNC_MENU')?></span>
                                  </button>
                                  <ul class="comments__buttons-list">
                                      <li class="comments__buttons-item">
                                          <button class="comment__button"><?=JText::_('COM_LASERCITY_ALL_SALON_CHECK_NEPRIEM')?></button>
                                      </li>
                                  </ul>
                              </div>
                          </div>
                          <div class="comments__blockquote">
                              <p class="comments__text"><?=$answer->answer?></p>
                              <div class="comments__feedback">
                                  <button class="comments__feedback-like"  data-answer="<?= $answer->id ?>">
                                      <output><?=$answer->likes?></output>
                                  </button>
                              </div>
                          </div>
                      </article>
                  </li>
                  <?php endforeach; ?>
              </ul>
          </section>

          <?php if ($this->answers['count'] > $this->answers['limit']): ?>
              <?php
              $start_pagination = $this->answers['page'] - 2 > 0 ? $this->answers['page'] - 2 : 1;
              $end_pagination = $this->answers['page'] + 2 <= $this->answers['pages'] ? $this->answers['page'] + 2 : $this->answers['pages'];
              ?>
              <!--<p class="salons__show-all-wrapper">
                  <button class="salons__show-all" type="button" title="" aria-label=""><?=JText::_('COM_LASERCITY_ALL_SHOW_YET')?>
                      <output>8</output>
                      <?=JText::_('COM_LASERCITY_STOCKS_YET_TXT')?>
                  </button>
              </p>-->
              <nav class="faq-discussion__pagination pagination">
                  <?php if ($this->answers['page'] > 1): ?>
                      <?php if($this->answers['page']==2): ?>
                          <a class="pagination__button pagination__button--prev button-corner" href="<?=$current_sef."faq/".$this->item->alias."/"?>#FaqComments" title="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?></span></a>
                      <?php else: ?>
                          <a class="pagination__button pagination__button--prev button-corner" href="<?=$current_sef."faq/".$this->item->alias."/".getURL($this->answers['page'] - 1, $url) ?>#FaqComments" title="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_PREV')?></span></a>
                      <?php endif;?>
                  <?php endif; ?>
                  <ul class="pagination__page-list">
                      <?php for ($i = $start_pagination; $i <= $end_pagination; $i++): ?>
                          <?php if($i==1): ?>
                              <li class="pagination__page-item<?= $i == $this->answers['page'] ? ' pagination__page-item--current' : '' ?>"><a <?php if ($i != $this->answers['page']): ?>href="<?=$current_sef."faq/".$this->item->alias."/" ?>#FaqComments"<?php endif; ?>><?= $i ?></a></li>
                          <?php else: ?>
                              <li class="pagination__page-item<?= $i == $this->answers['page'] ? ' pagination__page-item--current' : '' ?>"><a <?php if ($i != $this->answers['page']): ?>href="<?=$current_sef."faq/".$this->item->alias."/".getURL($i, $url) ?>#FaqComments"<?php endif; ?>><?= $i ?></a></li>
                          <?php endif;?>
                      <?php endfor; ?>
                  </ul>
                  <?php if ($this->answers['page'] < $this->answers['pages']): ?>
                      <a class="pagination__button pagination__button--next button-corner" href="<?=$current_sef."faq/".$this->item->alias."/".getURL($this->answers['page'] + 1, $url) ?>#FaqComments" title="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_NEXT')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_NEXT')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_PAGE_BUTTON_NEXT')?></span></a>
                  <?php endif; ?>
              </nav>
          <?php endif; ?>



<?php
/*
 * Заметка для Владислава
 *
 * */
///////////////////////////////////////////////////////////////----------------НЕ УДАЛЯТЬ И НЕ ИЗМЕНЯТЬ ЭТО НУЖНО ДЛЯ РАБОТЫ ФУНКЦИЙ СЕРВЕРА----------------------////////////
//id="function_addcomment_form"
//class="faq-discussion__addcomment-sent"
//<input type="hidden"
?>

        <section class="faq-discussion__addcomment" aria-labelledby="FaqAddComment">
          <h3 class="visually-hidden" id="FaqAddComment">Добавить коментарий к обсуждении</h3>
          <form class="faq-discussion__addcomment-form" method="post" id="function_addcomment_form">
            <div class="faq-discussion__addcomment-autor-wrapper">
              <p class="faq-discussion__addcomment-img-wrapper">
                <picture>
                    <?php if(empty($answer_avatar)):?>
                      <source srcset="https://placehold.it/90x90/red.wepb 1x, https://placehold.it/180x180/red.wepb 2x" media="(min-width: 1200px)" type="image/webp">
                      <source srcset="https://placehold.it/45x45/red.wepb 1x, https://placehold.it/90x90/red.wepb 2x" type="image/webp">
                      <source srcset="https://placehold.it/90x90/red.img 1x, https://placehold.it/180x180/red.img 2x" media="(min-width: 1200px)">
                      <img class="faq-discussion__addcomment-img" src="https://placehold.it/45x45/red.jpg" srcset="https://placehold.it/90x90/fff.jpg 2x" loading="lazy" alt="Фотография автора" title="Фотография автора">
                    <?php else: ?>
                      <source srcset="<?= $answer_avatar ?>_90x90.webp 1x, <?= $answer_avatar ?>_180x180.webp 2x" media="(min-width: 1200px)" type="image/webp">
                      <source srcset="<?= $answer_avatar ?>_45x45.webp 1x, <?= $answer_avatar ?>_90x90.webp 2x" type="image/webp">
                      <source srcset="<?= $answer_avatar ?>_90x90.webp 1x, <?= $answer_avatar ?>_180x180.webp 2x" media="(min-width: 1200px)" type="image/webp">
                      <img class="comments__autor-image comments__autor-image--open-popup" src="<?= $answer_avatar ?>_90x90.jpg" srcset="<?= $answer_avatar ?>_90x90.jpg 2x" loading="lazy" alt="<?=JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR')?>" title="<?=JText::_('COM_LASERCITY_ALL_SALON_FOTO_AVTOR')?>">
                    <?php endif; ?>
                </picture>
              </p>
              <div class="faq-discussion__addcomment-autor-fact">
                <cite class="faq-discussion__addcomment-autor-name"><?=$answer_name?></cite>
                <p class="faq-discussion__addcomment-autor-rank">
                    <?php
                    switch ($answer_rank){
                        case 0: echo JText::_('COM_LASERCITY_ALL_SALON_NOVICHECK');
                            break;
                        case 1: echo JText::_('COM_LASERCITY_ALL_SALON_BUVALUI');
                            break;
                        case 2: echo JText::_('COM_LASERCITY_ALL_SALON_EXPERT');
                            break;
                    }
                    ?>
                </p>
              </div>
            </div>
            <div class="faq-discussion__addcomment-wrapper">
              <input type="hidden" name="question" value="<?= $this->item->id ?>">
              <textarea name="answer" class="faq-discussion__addcomment-text" aria-label="<?= JText::_('COM_LASERCITY_FAQ_ENTER_ANSVER') ?>" placeholder="<?= JText::_('COM_LASERCITY_FAQ_ENTER_ANSVER') ?>"></textarea>
              <div class="faq-discussion__addcomment-buttons">
                <button class="faq-discussion__addcomment-sent button"><?= JText::_('COM_LASERCITY_FAQ_ANSVER_SEND') ?></button>
                <div class="faq-discussion__addcomment-file button-addfile snbtnup-otvet" id="addcomment_file">
                  <label class="button-addfile__wrapper" for="addcomment-file" tabindex="-1"></label>
                  <p class="button-addfile__text"><?= JText::_('COM_LASERCITY_ALL_FORM_ADDFOTO') ?></p>
                </div>
                <p class="ploadf" data-er="<?= JText::_('COM_LASERCITY_ALL_FORM_ERROR_FOTO_FORMAT') ?>"></p>
              </div>
              <p class="status"></p>
            </div>
          </form>
        </section>
      </section>
      <div class="faq-discussion__aside">
        <div class="faq-discussion__notfoud">
          <h3 class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_TXT_QUESTION') ?></h3>
          <b class="faq-discussion__notfoud-title"><?= JText::_('COM_LASERCITY_FAQ_DO_NOT_OBSUZD') ?></b>
          <button class="faq-discussion__notfoud-button button" data-set-modal-value="discussion"><?= JText::_('COM_LASERCITY_ALL_TXT_QUESTION') ?></button>
          <p class="faq-discussion__notfoud-text"><?= JText::_('COM_LASERCITY_FAQ_SPECIALIST_ANSVER') ?></p>
        </div>
      </div>
    </div>
  </section>
</main>
<aside class="page-aside faq-aside">
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
<style>
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

  .ploadf {
    margin-left: 25px;
  }

  .sndft {
    float: left;
    clear: both;
    width: 100%;
    padding: 5px 0 5px 27px;
    position: relative;
    background: url("/templates/lasercity/img/skrepka.png") no-repeat 3px center;
    white-space: nowrap;
  }

  .delet {
    cursor: pointer;
    margin-left: 10px;
  }

  .delet:hover {
    color: #d4124c;
  }
</style>