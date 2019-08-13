<?php /*

 Напоминание для ВЛАДА:
 1) не удалять поля type=hidden
 2) не изенять и не удалять классы,id
 3) перед тек как что-то менять уточняй у программистов

 Если слетит функционал ответственность будет на ТЕБЕ

 */ ?>
<div class="faq-discussion-popup">
    <header class="faq-discussion-popup__header">
        <h4 class="faq-discussion-popup__title"><?=JText::_('COM_LASERCITY_FAQ_ASK_PEOPLE')?></h4>
        <b class="faq-discussion-popup__copyright">Laser<span>city</span><span class="logo">UA</span></b>
    </header>
    <form class="faq-discussion-popup__form" method="post">
        <textarea name="title" class="faq-discussion-popup__quesion" aria-label="<?=JText::_('COM_LASERCITY_FAQ_QUESTION_PEOPLE')?>" placeholder="<?=JText::_('COM_LASERCITY_FAQ_QUESTION_PEOPLE')?>"></textarea>
        <textarea name="description" class="faq-discussion-popup__description" aria-label="<?=JText::_('COM_LASERCITY_FAQ_QUESTION_AMDESTEND')?>" placeholder="<?=JText::_('COM_LASERCITY_FAQ_QUESTION_AMDESTEND')?>"></textarea>
        <div class="status"></div>
        <button class="faq-discussion-popup__button button"><?=JText::_('COM_LASERCITY_ALL_SEND_ME')?></button>
    </form>
    <button class="faq-discussion-popup__closed button-cross" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?></span></button>
</div>