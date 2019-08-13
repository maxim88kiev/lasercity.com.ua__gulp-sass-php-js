<?php defined('_JEXEC') or die;
ContentLoader::addScript('register');
?>
<main>
    <section class="register" aria-labelledby="Register">
        <h1 class="register__title" id="Register"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_ZAGOL')?></h1>
        <section class="register__who" aria-labelledby="RegisterForClientOrPartner">
            <h2 class="register__who-title title-lines" id="RegisterForClientOrPartner"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_WHO_YOU')?></h2>
            <form class="register__who-form send-account-form" method="post">
                <p class="register__who-choices type-choices">
                    <label class="type-choices__choice type-choices__choice--client">
                        <input class="type-choices__for" type="radio" name="register_for" value="user" <?= $this->type == 'client' ? 'checked' : '' ?>>
                        <span><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_KAK_USER')?></span>
                    </label>
                    <label class="type-choices__choice type-choices__choice--partner">
                        <input class="type-choices__for" type="radio" name="register_for" value="organization" <?= $this->type == 'organization' ? 'checked' : '' ?>>
                        <span><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_KAK_ORGANIZATION')?></span>
                    </label>
                </p>
                <p class="register__who-inputs-wrapper">
                    <label>
                        <input name="name" type="text" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_NAME')?>" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_NAME')?>">
                    </label>
                    <label>
                        <input name="email" type="text" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>">
                        <!--<span class="register__who-input-error">Email  должен вмещать один символ @</span>-->
                    </label>
                    <label>
                        <input name="phone" type="text" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_PHONE')?>" placeholder="+38 (___) __-__-__">
                    </label>
                    <label>
                        <input name="password" class="register__who-input register__who-input--password" type="password" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD')?>" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD')?>">
                        <button class="register__who-password-btn button-password" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?>" title="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?>" type="button"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?></span></button>
                    </label>
                </p>
                <section class="register__either" aria-label="RegisterEitherOption">
                    <h3 class="register__either-title title-lines"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_WHO_ILI')?></h3>
                    <p class="register__either-description"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_WHO_ENTER')?></p>
                    <p class="register__either-links login-social">
                        <a class="login-social__link login-social__link--google google-button" href="#">Google</a>
                        <a class="login-social__link login-social__link--facebook facebook-button" href="#">Facebook</a>
                    </p>

                    <div class="register__either-rules accept-rules">
                        <label>
                            <input name="ruls" type="checkbox">
                        </label>
                        <p class="accept-rules__rule"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_WHO_ANGREE')?> <a href="#"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_WHO_OFERTA')?></a> <?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_WHO_I')?> <a href="#"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_WHO_RULS')?></a><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_WHO_GIVE')?></p>
                    </div>
                </section>
                <div class="status for-register"></div>
                <button class="register__sent-button button btn-register"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_BUTTON')?></button>
            </form>
        </section>
    </section>
</main>


<!-- подключить скрипт скрипт -  register.js -->