<div class="popup-forgot">
    <h2 class="popup-forgot__title"><?=JText::_('COM_LASERCITY_DONT_KNOW_ZAGOL')?></h2>
    <p class="popup-forgot__text"><?=JText::_('COM_LASERCITY_DONT_KNOW_YOU_EMAIL')?></p>
    <form class="popup-forgot__form" >
        <label class="popup-forgot__input-wrapper">
            <input type="email" name="email_forgot" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>">
        </label>
        <button class="popup-forgot__button-back" type="button" data-set-modal-value="singup"><?=JText::_('COM_LASERCITY_DONT_KNOW_RETURN')?></button>
        <button class="popup-forgot__sent button"><?=JText::_('COM_LASERCITY_DONT_KNOW_SEND')?></button>
    </form>
    <b class="popup-forgot__waiting"></b>
    <button class="popup-forgot__close button-cross" type="button" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>" title="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?></span></button>
</div>