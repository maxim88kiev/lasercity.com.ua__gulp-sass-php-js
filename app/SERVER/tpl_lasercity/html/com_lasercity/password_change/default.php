<?php
 $user_id = explode(":",JFactory::getApplication()->input->getString('data'))[0];

$db = JFactory::getDbo();
$query = "SELECT id  
          FROM `#__users` 
          WHERE id='".(int)$user_id."' AND UNIX_TIMESTAMP(lastResetTime)>UNIX_TIMESTAMP(NOW()) ";
$db->setQuery($query);
$result = $db->loadObjectList();

$success_user = '';
foreach ($result as $row) {
    if (!empty($row->id)) {
        $success_user = 'ok';
        break;
    }
}
?>
<section class="password-change" aria-label="changeYourPassword">
    <div class="password-change__title-wrapper title-lines-wrapper">
        <h1 class="password-change__title title-lines" id="changeYourPassword"><?=JText::_('COM_LASERCITY_DONT_KNOW_PASSWORD_SMENA')?></h1>
    </div>
    <div class="password-change__wrapper">
        <b class="password-change__late" <?=(empty($success_user) ? 'style="display:block"' : 'style="display:none"')?>><?=JText::_('COM_LASERCITY_DONT_KNOW_PASSWORD_TIME')?></b>
        <form class="password-change__form" method="post" <?=(empty($success_user) ? 'style="display:none"' : 'style="display:block"')?>>
            <input type="hidden" name="user_id" value="<?=$user_id?>">
            <p class="password-change__input-wrapper">
                <label>
                    <input name="new_password" class="password-change__input-password" type="password" placeholder="<?=JText::_('COM_LASERCITY_DONT_KNOW_PASSWORD_NEW')?>">
                </label>
                <button class="password-change__password-button button-password button-password--active" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?></span></button>
            </p>
            <p class="password-change__input-wrapper">
                <label>
                    <input name="new_password1" type="password" placeholder="<?=JText::_('COM_LASERCITY_DONT_KNOW_PASSWORD_NEW1')?>">
                </label>
            </p>
            <p class="password-change__form-waiting"><?=JText::_('COM_LASERCITY_DONT_KNOW_PASSWORD_BEZOPAS')?></p>
            <div class="status"></div>
            <button class="password-change__form-button button"><?=JText::_('COM_LASERCITY_DONT_KNOW_PASSWORD_SAVE')?></button>
        </form>
    </div>
</section>