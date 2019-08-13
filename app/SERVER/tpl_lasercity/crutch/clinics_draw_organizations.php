<?php defined('_JEXEC') or die;

//узнаем ссылку на язык
$default_sef = "";
$current_sef = LangHelper::getCurrentSef();
$language_default = JComponentHelper::getParams('com_languages')->get('site');
$db = JFactory::getDbo();
$db->setQuery("SELECT sef FROM `#__languages` WHERE lang_code = '".$language_default."' ");
$res = $db->loadObjectList();
foreach ($res as $row) {
    if (!empty($row->sef)) {$default_sef = $row->sef; break;}
}
$current_sef = str_replace($default_sef,"",$current_sef);
$current_sef = empty($current_sef) ? "/" : "/".$current_sef."/";
/*echo '<pre>';
print_r($this->items);
echo '</pre>';*/ ?>
<?php foreach ($organizations as $organization): ?>
    <li class="salons__salon salons__salon--network salons__salon--gray">
        <div class="salons__main-wrapper">
            <p class="salons__overview-wrapper">
                <picture>
                    <?php
                    $image = 'https://placehold.it/';
                    if (mb_strlen($organization->logo)) {
                        $image = '/' . $organization->logo;
                    }
                    $image = deleteFormat($image);
                    ?>
                    <source data-srcset="<?= $image ?>_262x198.webp 1x, <?= $image ?>_524x396.webp 2x"
                            media="(min-width: 1200px)" type="image/webp">
                    <source data-srcset="<?= $image ?>_90x68.webp 1x, <?= $image ?>_180x136.webp 2x" type="image/webp">
                    <source data-srcset="<?= $image ?>_262x198.jpg 1x, <?= $image ?>_524x396.jpg 2x"
                            media="(min-width: 1200px)">
                    <img class="salons__img lazyload" data-src="<?= $image ?>_90x68.jpg"
                         data-srcset="<?= $image ?>_180x136.jpg 2x" loading="lazy"
                         title="<?= JText::_('COM_LASERCITY_IMG_TITLE_1_HOME') ?> <?= isset($organization->title) ? $organization->title : $organization->alias ?>"
                         alt="<?= JText::_('COM_LASERCITY_IMG_TITLE_1_HOME') ?> <?= isset($organization->title) ? $organization->title : $organization->alias ?>">
                </picture>
            </p>
            <div class="salons__information">
                <div class="salons__information-wrapper">
                    <div class="salons__title-description">
                        <h3 class="salons__name"><a href="<?=$current_sef."clinics/".$organization->alias?>"><?= $organization->title ?></a></h3>
                        <p class="salons__about"><?= $organization->type ?></p>
                        <p class="salons__aparat"><?=JText::_('COM_LASERCITY_ALL_SALON_LOAD_APARAT')?>: <?= implode(', ', $organization->apparatus) ?></p>
                    </div>
                    <div class="salons__advantages">
                        <a class="salons__rating rating-salon" href="<?=$current_sef."clinics/{$organization->alias}/#SalonRation"?>">
                            <div class="rating-salon__point-wrapper">
                                <span class="rating-salon__point">
                                    <?php
                                    $raiting_array = ContentLoader::getRaiting($organization->id,'organization_id');
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
                                <?=$raiting_array['count_raiting']?> <?=ContentLoader::replaceSuffix($raiting_array['count_raiting'],'review')?>
                            </p>
                        </a>
                        <ul class="salons__services-list services-list">
                            <?php foreach ($organization->comforts as $comfort): ?>
                                <li class="services-list-item services-list-item--<?= $comfort['icon'] ?>"
                                    title="<?= $comfort['title'] ?>">
                                    <span><?= $comfort['title'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="salons__description">
                        <p class="salons__description-text"><?= strip_tags($organization->description) ?></p>
                        <button class="salons__description-button" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SALON_OPEN_DESCRIPTION')?>"
                                aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_OPEN_DESCRIPTION')?>">...
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ul class="salons__branches branches-list">
            <?php $i=-1; foreach ($organization->affiliates as $affiliate): $i++;
                $link = "{$current_sef}clinics/{$affiliate->alias}"; ?>
                <li class="branches-list__branch<?=($i>2) ? ' box-hidden' : ''?>" data-main_image="/<?=$affiliate->main_image?>">
                    <div class="branches-list__information">
                        <div class="branches-list__contacts">
                            <h4 class="branches-list__title"><a href="<?= $link ?>"><?= $affiliate->title ?></a></h4>
                            <div class="branches-list__address salon-address">
                                <p class="salon-address__underground">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                         fill="<?= $affiliate->metro_line ?>"
                                         viewBox="337.5 232.3 125 85.9" aria-hidden="true">
                                        <polygon
                                                points="453.9,306.2 424.7,232.3 400,275.5 375.4,232.3 346.1,306.2 337.5,306.2 337.5,317.4 381.7,317.4 381.7,306.2 375.1,306.2 381.5,287.8 400,318.2 418.5,287.8 424.9,306.2 418.3,306.2 418.3,317.4 462.5,317.4 462.5,306.2 "></polygon>
                                    </svg>
                                    <?= $affiliate->metro ?>
                                </p>
                                <p class="salon-address__street"><?= $affiliate->location ?></p>
                            </div>
                            <div class="branches-list__schedule">
                                <div class="branches-list__phones phonebook">
                                    <div class="phonebook__wrapper">
                                        <ul class="phonebook__list">
                                            <?php foreach ($affiliate->phones as $phone): ?>
                                                <li class="phonebook__item">
                                                    <a><?= $phone ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <?php if (count($affiliate->phones) > 1): ?>
                                            <button class="phonebook__button button-corner" type="button" title="<?=JText::_('COM_LASERCITY_AFFILIALES_SEE_PHONES')?>" aria-label="<?=JText::_('COM_LASERCITY_AFFILIALES_SEE_PHONES')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_AFFILIALES_SEE_PHONES')?></span></button>
                                        <?php endif; ?>
                                        <div class="phonebook__popup">
                                            <p class="phonebook__popup-text"><?=JText::_('COM_LASERCITY_STOCKS_SVIASI')?></p>
                                            <a class="phonebook__popup-link" href="#"><?=JText::_('COM_LASERCITY_STOCKS_RAITING')?></a>
                                            <ul class="phonebook__list">
                                                <?php foreach ($affiliate->phones as $phone): ?>
                                                    <li class="phonebook__popup-item">
                                                        <a href="tel:<?=trim(preg_replace('/[^0-9]/', '',$phone))?>"><?= $phone ?></a>
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
                                                    <?= $schedule['days'] ?> <span><?= $schedule['time'] ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <button class="workday__button-close button-cross" type="button" title="<?=JText::_('COM_LASERCITY_ALL_CLOSE_GRAPHIK_JOB')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_CLOSE_GRAPHIK_JOB')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_CLOSE_GRAPHIK_JOB')?></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="branches-list__aside">
                            <a class="branches-list__rating rating-salon" href="<?= $link ?>#SalonRation">
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
                            <a class="branches-list__map button-onmap" title="<?=JText::_('COM_LASERCITY_ALL_TXT_MAP_GOOGLE')?>"
                               aria-label="<?=JText::_('COM_LASERCITY_ALL_TXT_MAP_GOOGLE')?>" href="<?= $affiliate->coordinate ?>"><?=JText::_('COM_LASERCITY_ALL_TXT_MAP_GOOGLE')?></a>
                            <button class="branches-list__question" data-set-modal-value="question" data-affiliate="<?=$affiliate->id?>"><?=JText::_('COM_LASERCITY_ALL_TXT_QUESTION')?>
                            </button>
                        </div>
                    </div>
                    <button class="branches-list__button button" data-set-modal-value="recording" data-affiliate="<?=$affiliate->id?>"><?=JText::_('COM_LASERCITY_ALL_TXT_WRITERING')?></button>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="salons__salon-show-all-wrapper">
            <?=JText::_('COM_LASERCITY_ALL_SALON_LOAD_S')?>(
            <output><?=@count($organization->affiliates)?></output>
            )
            <button class="salons__salon-show-button button-corner" type="button" title="<?=JText::_('COM_LASERCITY_ALL_SALON_LOAD_YET')?>"
                    aria-label="<?=JText::_('COM_LASERCITY_ALL_SALON_LOAD_YET')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_SALON_LOAD_YET')?></span>
            </button>
        </p>
    </li>
<?php endforeach; ?>
<style>
    .box-hidden{
        display: none;
    }
</style>
