<?php /*

 Напоминание для ВЛАДА:
 1) не удалять поля type=hidden
 2) не изенять и не удалять классы,id
 3) перед тек как что-то менять уточняй у программистов

 Если слетит функционал ответственность будет на ТЕБЕ

 */ ?>
<?php defined('_JEXEC') or die;

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

<div class="popup-login">
    <h2 class="popup-login__title"><?=JText::_('COM_LASERCITY_ENTER_ZAGOL')?></h2>
    <form class="popup-login__form send-enter-form" method="post">
        <section class="popup-login__login-bypassword" aria-labelledby="LoginByPassword">
            <h3 class="visually-hidden" id="LoginByPassword"><?=JText::_('COM_LASERCITY_ALL_FORM_ENTER_H2')?></h3>
            <label class="popup-login__label">
                <input class="popup-login__email" autocomplete="off" type="text" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>" name="enter_email" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>">
                <!--<span class="popup-login__input-error">Email должен вмещать один символ @</span>-->
            </label>
            <label class="popup-login__label">
                <input class="popup-login__password" autocomplete="off" type="password" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD')?>" name="enter_password" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD')?>">
                <button class="popup-login__password-button button-password" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?></span></button>
            </label>
            <div class="popup-login__options-wrapper">
                <label class="popup-login__remember label">
                    <input type="checkbox" name="save_me">
                    <?=JText::_('COM_LASERCITY_ALL_FORM_REMEMBER_ME')?>
                </label>
                <a class="popup-login__forgot" data-set-modal-value="forgot" type="button"><?=JText::_('COM_LASERCITY_ALL_FORM_ZABUL_PASSWORD')?></a>
            </div>
        </section>
        <section class="popup-login__login-bysocial" aria-labelledby="LoginBySocialNetwork">
            <h3 class="popup-login__either-title title-lines" id="LoginBySocialNetwork"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_WHO_ILI')?></h3>
            <p class="popup-login__either-text"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_WHO_ENTER')?></p>
            <p class="popup-login__either-buttons">
                <a href="#" class="popup-login__either-button popup-login__either-button--google google-button" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_GO_TO_GOOGLE')?>"><span>Google</span></a>
                <a href="#" class="popup-login__either-button popup-login__either-button--facebook facebook-button" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_GO_TO_FACEBOOK')?>"><span>Facebook</span></a>
            </p>
        </section>
        <button class="popup-login__button button"><?=JText::_('COM_LASERCITY_ALL_FORM_ENTER_TXT')?></button>
    </form>
    <a href="/register<?=$current_sef?>" class="popup-login__register-button button" target="_blank" rel="noreferrer noopener"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_BUTTON')?></a>
    <button class="popup-login__closed button-cross" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?></span></button>
</div>


<?php
/*echo "<br>sef=" . LangHelper::getCurrentSef();
echo "<br>session:my_user_id=" . JFactory::getSession()->get('my_user_id');
echo "<br>session:my_user_name=" . JFactory::getSession()->get('my_user_name');
echo "<br>session:my_user_avatar=" . JFactory::getSession()->get('my_user_avatar');
echo "<br>session:my_user_rank=" . JFactory::getSession()->get('my_user_rank');
echo "<br>session:tip_user=" . JFactory::getSession()->get('tip_user');
echo "<br>session:organization_id=" . JFactory::getSession()->get('organization_id');

echo "<br>cookie:my_user_id=" . JFactory::getApplication()->input->cookie->get('my_user_id');
echo "<br>cookie:tip_user=" . JFactory::getApplication()->input->cookie->get('tip_user');
echo "<br>cookie:organization_id=" . JFactory::getApplication()->input->cookie->get('organization_id');*/
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    $(document).ready(function () {


            var remember = '<?=JFactory::getSession()->get('remember_me')?>';
            if (remember) {
                $('form [name="save_me"]').attr('checked', 'checked');
            }

            $('form [name="save_me"]').on('change', function () {

                save_me = ($('form [name="save_me"]:checked').val() !== undefined) ? $('form [name="save_me"]:checked').val() : '';
                $.ajax({
                    dataType: 'json',
                    url: '/?option=com_lasercity&task=setRemember&save_me=' + save_me,
                    success: function (data) {
                        //console.log(data.success);
                    }
                });
            });


        });
    });
</script>