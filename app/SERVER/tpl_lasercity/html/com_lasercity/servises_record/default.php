<?php
$mas_service = json_decode(JFactory::getSession()->get('post_mas_service'),true);
$post_mas_service_dopol = json_decode(JFactory::getSession()->get('post_mas_service_dopol'),true);
if(empty($mas_service)){
    header("Location: /", TRUE, 301);
    exit();
}

$post_affiliate_id = $post_mas_service_dopol['post_affiliate_id'];
$post_stock_id = $post_mas_service_dopol['post_stock_id'];
$post_date_visit = $post_mas_service_dopol['post_date_visit'];
$post_date_end = $post_mas_service_dopol['post_date_end'];

$all_price = 0;
?>
<main>
  <section class="order" aria-labelledby="RegistrationOfYheApplication">
    <h1 class="order__title title-lines" id="RegistrationOfYheApplication"><?=JText::_('COM_LASERCITY_RECORDING_OFORM_H1')?></h1>
    <form id="order_form_by_select" class="order__form recording__form__services" method="post">
      <section class="order__contact" aria-labelledby="ContactDetails">
        <h2 class="order__contact-title" id="ContactDetails"><?=JText::_('COM_LASERCITY_RECORDING_OFORM_DATA')?></h2>
        <p class="order__contact-type type-choices">
          <label class="type-choices__choice type-choices__choice--client" data-textbtn="<?=JText::_('COM_LASERCITY_RECORDING_REG_SEND')?>">
            <input class="type-choices__for" type="radio" name="register_for" value="no_accaut" checked>
            <span><?=JText::_('COM_LASERCITY_RECORDING_OFORM_NEW')?></span>
          </label>
          <label class="type-choices__choice type-choices__choice--partner" data-textbtn="<?=JText::_('COM_LASERCITY_RECORDING_ENTER_SEND')?>">
            <input class="type-choices__for" type="radio" name="register_for" value="yes_accaut">
            <span><?=JText::_('COM_LASERCITY_RECORDING_OFORM_OLD')?></span>
          </label>
        </p>
        <div class="order__contact-inputs order__contact-inputs--new order__contact-inputs--active">
          <label class="order__contact-label">
              <input name="name" value="<?=!empty($post_mas_service_dopol['post_name']) ? $post_mas_service_dopol['post_name'] : '' ?>" type="text" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_NAME')?>">
          </label>
            <label class="order__contact-label">
                <input name="phone" value="<?=!empty($post_mas_service_dopol['post_phone']) ? $post_mas_service_dopol['post_phone'] : '' ?>" type="text">
            </label>
        </div>
          <div class="order__contact-inputs-all">
              <label class="order__contact-label">
                  <input autocomplete="off" type="text" name="email" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>">
              </label>
              <p class="order__contact-label">
                  <label class="order__contact-label"><input autocomplete="off" type="password" name="password" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD')?>"></label>
                  <button class="order__password-button button-password" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?></span></button>
              </p>
          </div>
        <div class="order__contact-inputs order__contact-inputs--regular">
          <!--<label class="order__contact-label">
              <input autocomplete="off" type="text" name="email" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>">
          </label>
          <p class="order__contact-label">
              <label><input autocomplete="off" type="password" name="password" placeholder="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD')?>"></label>
            <button class="order__password-button button-password" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?>">
                <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_PASSWORD_SEE')?></span>
            </button>
          </p>-->
          <p class="order__options-wrapper">
            <label class="order__option-label label">
              <input type="checkbox" name="save_me"><?=JText::_('COM_LASERCITY_ALL_FORM_REMEMBER_ME')?>
            </label>
              <button class="order__option-forgot" data-set-modal-value="forgot" type="button"><?=JText::_('COM_LASERCITY_ALL_FORM_ZABUL_PASSWORD')?></button>
          </p>
        </div>
        <h2 class="order__either-title title-lines"><?=JText::_('COM_LASERCITY_ALL_FORM_REGISTER_WHO_ILI')?></h2>
        <p class="order__either-buttons login-social">
          <a class="login-social__link login-social__link--google google-button" href="#">Google</a>
          <a class="login-social__link login-social__link--facebook facebook-button" href="#">Facebook</a>
        </p>
      </section>
      <section class="order__services" aria-label="otherServices">
        <h2 class="order__services-title" id="otherServices"><?=JText::_('COM_LASERCITY_RECORDING_OFORM_H2')?></h2>
        <div class="order__services-wrapper">
          <ul class="order__services-list conteiner-recording">
            <?php if($mas_service): ?>
                <?php foreach ($mas_service as $service):

                    $all_price += $service['price'];
                    ?>
                    <li class="order__services-item order__services-item--<?=(($service['sex']==0) ? 'woman' : 'man')?>">
                      <?=$service['service_name']?>
                        <input type="hidden" name="apparat_id[]" value="<?=$service['apparat_id']?>">
                        <input type="hidden" name="service_id[]" value="<?=$service['service_id']?>">
                        <input type="hidden" name="prices_id[]" value="<?=$service['prices_id']?>">
                        <input type="hidden" name="duration[]" class="time-element" value="<?=$service['duration']?>">
                        <input type="hidden" name="price[]" class="price-element" value="<?=$service['price']?>">
                        <button class="order__services-item-button button-cross" type="button" title="<?=JText::_('COM_LASERCITY_RECORDING_DELETE_EL')?>" aria-label="<?=JText::_('COM_LASERCITY_RECORDING_DELETE_EL')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_RECORDING_DELETE_EL')?></span></button>
                    </li>
                <?php endforeach;?>
            <?php endif;?>
          </ul>
          <button class="order__services-button-more" type="button"><?=JText::_('COM_LASERCITY_RECORDING_MORE_DETAL')?>...</button>
          <div class="order__services-more box-hidden" >

              <div class="order__services-input-wrapper">
                  <input name="stock" type="hidden" value="<?=$post_stock_id?>">
                  <input name="affiliate" type="hidden" value="<?=$post_affiliate_id?>">
                  <input name="service_txt" id="input_service" class="order__services-input" type="search" autocomplete="off" aria-label="<?=JText::_('COM_LASERCITY_RECORDING_NAME_USLG')?>" placeholder="<?=JText::_('COM_LASERCITY_RECORDING_NAME_USLG')?>">
                  <button class="order__services-input-button" type="button" aria-label="" title=""><span class="visually-hidden"></span></button>
              </div>
              <a class="order__services-more-add button-addprocedure" href="#"><?=JText::_('COM_LASERCITY_RECORDING_ADD_PROCED')?></a>

            <div class="order__wish-info select-date">
              <p class="select-date__wish-day"><?=JText::_('COM_LASERCITY_RECORDING_YOU_DATA')?>
                <span>
                    <!--<output>30.11.2018</output>-->
                    <input type="text" id="send_date_uslug" name="date_visit" value="<?=date('d.m.Y',strtotime($post_date_visit))?>">
                    <button class="select-date__wish-button" type="button" title="" aria-label=""></button>
                </span>
              </p>
              <p class="select-date__wish-time auto-time-do">
                  <?=JText::_('COM_LASERCITY_RECORDING_YOU_TIME')?><br>
                  <?=JText::_('COM_LASERCITY_RECORDING_YOU_OT')?> <span class="select-date__wish-time--from"><label><input maxlength="2" name="hour1" type="text" value="<?=date('H',strtotime($post_date_visit))?>"></label> : <label><input maxlength="2" name="minut1" type="text" value="<?=date('i',strtotime($post_date_visit))?>"></label></span>
                  <?=JText::_('COM_LASERCITY_RECORDING_YOU_DO')?> <span class="select-date__wish-time--until"><label><input readonly="readonly" name="hour2" type="text" value="<?=date('H',strtotime($post_date_end))?>"></label> : <label><input readonly="readonly" name="minut2" type="text" value="<?=date('i',strtotime($post_date_end))?>"></label></span>
              </p>

            </div>
            <label class="order__comment">
                <span class="show-box-next-textarea"><?=JText::_('COM_LASERCITY_RECORDING_KOMENT')?></span>
                <textarea name="koment" type="text" class="box-hidden"><?=!empty($post_mas_service_dopol['post_koment']) ? $post_mas_service_dopol['post_koment'] : '' ?></textarea>
            </label>
          </div>
          <div class="order__services-topay topay">
              <?=JText::_('COM_LASERCITY_RECORDING_PRICE')?>:
            <ul class="topay__money-list">
              <li class="topay__money-item topay__money-item--card" title="<?=JText::_('COM_LASERCITY_RECORDING_BY_CREDI_CARD')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_RECORDING_BY_CREDI_CARD')?></span></li>
              <li class="topay__money-item topay__money-item--cash" title="<?=JText::_('COM_LASERCITY_RECORDING_BY_NALOM')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_RECORDING_BY_NALOM')?></span></li>
            </ul>
            <output class="popup-recording__pay"><?=$all_price?> грн</output>
          </div>
        </div>
        <div class="order__rules accept-rules">
          <label>
            <input type="checkbox" name="ruls">
          </label>
          <p class="accept-rules__rule"><?=sprintf(JText::_('COM_LASERCITY_RECORDING_YES_S'),"#","#")?></p>

            <div class="status"></div>
        </div>
        <button class="order__authorization-button button" data-record_step="step"><?=JText::_('COM_LASERCITY_RECORDING_REG_SEND')?></button>
      </section>
    </form>
  </section>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function () {

            $("#send_date_uslug").MyDtime({language: '<?=LangHelper::getCurrentSef()?>'});

            /*var register_me = '<?=JFactory::getSession()->get('register_me')?>';
            if (register_me) {
                $('.recording__form__services [name="register_me"]').attr('checked', 'checked');
            }

            $('.recording__form__services [name="register_me"]').on('change', function () {

                register_me = ($('.recording__form__services [name="register_me"]:checked').val() !== undefined) ? $('.recording__form__services [name="register_me"]:checked').val() : '';
                $.ajax({
                    dataType: 'json',
                    url: '/?option=com_lasercity&task=setRegisterMe&register_me=' + register_me,
                    success: function (data) {
                        //console.log(data.success);
                    }
                });
            });*/


        });
    });
</script>