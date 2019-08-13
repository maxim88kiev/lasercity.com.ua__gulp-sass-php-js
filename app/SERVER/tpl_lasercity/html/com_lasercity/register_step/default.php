<?php /*

 Напоминание для ВЛАДА:
 1) не удалять поля type=hidden
 2) не изенять и не удалять классы,id
 3) перед тек как что-то менять уточняй у программистов

 Если слетит функционал ответственность будет на ТЕБЕ

 */
$post_phone = !empty(JFactory::getSession()->get('post_phone')) ? JFactory::getSession()->get('post_phone') : '';
?>
<main>
    <section class="register-steps" aria-label="registerSteps">
        <h1 class="visually-hidden" id="registerSteps"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_H1') ?></h1>
        <form method="post" id="register_by_step">
            <ol class="register-steps__sections">
                <li class="register-steps__section register-steps__section--phone <?=(!empty($post_phone) ? 'visually-hidden' : '')?>">
                    <div class="register-steps__title-wrapper title-lines-wrapper">
                        <h2 class="register-steps__title title-lines"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_H2_1') ?></h2>
                    </div>
                    <div class="register-steps__section-wrapper">
                        <p class="register-steps__text"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_P_1') ?></p>
                        <label class="register-steps__label">
                            <input name="phone" class="register-steps__input-phone" type="text" placeholder="+38 (0__) ___-__-__" value="<?=$post_phone?>">
                        </label>
                        <a class="register-steps__button-continue button" href="#" aria-label="<?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_A_1') ?>"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_A_1') ?></a>
                    </div>
                </li>
                <li class="register-steps__section register-steps__section--organization <?=(empty($post_phone) ? 'visually-hidden' : '')?>">
                    <div class="register-steps__title-wrapper title-lines-wrapper">
                        <span><a class="register-steps__button-back" href="#" aria-label="<?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_RETURN') ?>" title="<?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_RETURN') ?>"></a></span>
                        <h2 class="register-steps__title title-lines"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_H2_2') ?></h2>
                    </div>
                    <div class="register-steps__section-wrapper">
                        <p class="register-steps__text"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_P_2') ?></p>
                        <div class="register-steps__input-organization-wrapper">
                            <label class="register-steps__label">
                                <div class="register-steps__organization-autocomplete" style="align-self: center;">
                                    <input name="organization_sel" class="register-steps__input-organization" type="text" placeholder="<?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_INPUT_1') ?>">
                                    <input name="organization" type="hidden">
                                </div>
                            </label>
                            <!--<div class="register-steps__organization-autocomplete autocomplete" style="display: block">
                                <ul class="autocomplete__list">
                                    <li class="autocomplete__item">
                                        <a href="#">Люменис на м Дарница</a>
                                        <span>Создать организацию с таким названием</span>
                                    </li>
                                    <li class="autocomplete__item">
                                        <a href="#">Люменис на м Дарница</a>
                                        <span>Сеть</span>
                                    </li>
                                    <li class="autocomplete__item">
                                        <a href="#">Люменис на м Дарница</a>
                                        <span>Сеть</span>
                                    </li>
                                    <li class="autocomplete__item">
                                        <a href="#">Люменис на м Дарница</a>
                                        <span>Сеть</span>
                                    </li>
                                    <li class="autocomplete__item">
                                        <a href="#">Люменис на м Дарница</a>
                                        <span>Сеть</span>
                                    </li>
                                    <li class="autocomplete__item">
                                        <a href="#">Люменис на м Дарница</a>
                                        <span>Сеть</span>
                                    </li>
                                </ul>
                            </div>-->
                        </div>
                        <a class="register-steps__button-continue button" href="#" aria-label="<?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_A_1') ?>"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_A_1') ?></a>
                    </div>
                </li>
                <li class="register-steps__section register-steps__section--organization-phone visually-hidden">
                    <div class="register-steps__title-wrapper title-lines-wrapper">
                        <span><a class="register-steps__button-back" href="#" aria-label="<?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_RETURN') ?>" title="<?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_RETURN') ?>"></a></span>
                        <h2 class="register-steps__title title-lines"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_H2_3') ?></h2>
                    </div>
                    <div class="register-steps__section-wrapper">
                        <p class="register-steps__text"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_P_3') ?></p>
                        <p class="register-steps__text"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_P_4') ?></p>
                        <label class="register-steps__label">
                            <input name="phone1" class="register-steps__input-organization-phone" type="text" placeholder="+38 (0__) ___-__-__ ">
                        </label>
                        <a class="register-steps__button-continue button" href="#" aria-label="<?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_A_1') ?>"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_A_1') ?></a>
                    </div>
                </li>
                <li class="register-steps__section register-steps__section--confirm visually-hidden">
                    <div class="register-steps__title-wrapper title-lines-wrapper">
                        <span><a class="register-steps__button-back" href="#" aria-label="<?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_RETURN') ?>" title="<?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_RETURN') ?>"></a></span>
                        <h2 class="register-steps__title title-lines"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_H2_4') ?></h2>
                    </div>
                    <div class="register-steps__section-wrapper">
                        <p class="register-steps__text"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_P_5') ?> <span id="send_phone_org"></span></p>
                        <label class="register-steps__label">
                            <input name="code" class="register-steps__input-confirm" type="text" placeholder="XXXX">
                        </label>
                        <button class="register-steps__confirm-again"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_BTN_YET') ?></button>
                        <span id="box_load_after_time"> <?= sprintf(JText::_('COM_LASERCITY_REGISTER_BY_STEP_AFTER_TIME'),'<span id="load_after_time">0</span>') ?></span>
                        <a class="register-steps__button-continue button" href="#" aria-label="<?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_A_1') ?>"><?= JText::_('COM_LASERCITY_REGISTER_BY_STEP_A_1') ?></a>
                    </div>
                </li>
                <li class="register-steps__section register-steps__section--confirm status">

                </li>
            </ol>
        </form>
    </section>
</main>