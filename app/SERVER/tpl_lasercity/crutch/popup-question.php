<?php /*

 Напоминание для ВЛАДА:
 1) не удалять поля type=hidden
 2) не изенять и не удалять классы,id
 3) перед тек как что-то менять уточняй у программистов

 Если слетит функционал ответственность будет на ТЕБЕ

 */ ?>
<div class="popup-question">
  <h3 class="popup-qeustion__title"><?=JText::_('COM_LASERCITY_ALL_TXT_QUESTION')?></h3>
  <form class="popup-qeustion__form" method="post">
    <input name="name" type="text" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_NAME')?>" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_NAME')?>">
    <input name="phone" type="text" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_PHONE')?>" placeholder="+38 (___) ___ - __ - __">
    <input name="email" type="text" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>">
    <input name="affiliate" type="hidden">
    <textarea name="koment" aria-label="<?=JText::_('COM_LASERCITY_FAQ_QUESTION_SUTI')?>" placeholder="<?=JText::_('COM_LASERCITY_FAQ_QUESTION_SUTI')?>"></textarea>
      <div class="status"></div>
    <button class="popup-question__button button"><?=JText::_('COM_LASERCITY_ALL_SEND_ME')?></button>
  </form>
  <p class="popup-question__rules"><?=JText::_('COM_LASERCITY_FAQ_QUESTION_SEND')?> <a href="#"><?=JText::_('COM_LASERCITY_ALL_FORM_RULS_SITE')?></a> <?=JText::_('COM_LASERCITY_ALL_FORM_MY_SITE')?></p>
  <button class="popup-question__closed button-cross" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?></span></button>
</div>