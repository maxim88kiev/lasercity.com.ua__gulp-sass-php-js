<?php /*

 Напоминание для ВЛАДА:
 1) не удалять поля type=hidden
 2) не изенять и не удалять классы,id
 3) перед тек как что-то менять уточняй у программистов

 Если слетит функционал ответственность будет на ТЕБЕ

 */ ?>
<?php defined('_JEXEC') or die; ?>

<div class="salon-addcomment salon-addcomment--no-accaunt">
  <header class="salon-addcomment__header">
    <h3 class="salon-addcomment__title"><?=JText::_('COM_LASERCITY_FORM_REVIEW_H3')?></h3>
    <p class="salon-addcomment__address"></p>
  </header>
  <form class="salon-addcomment__form" method="post">
    <p class="salon-addcomment__warning"><?=JText::_('COM_LASERCITY_FORM_REVIEW_P_1')?></p>
    <div class="salon-addcomment__autor-wrapper">
      <div class="salon-addcomment__autor-image-wrapper">
          <?php
          $avatar="";
          if(!empty(JFactory::getSession()->get('my_user_avatar'))){
              $avatar = deleteFormat(JFactory::getSession()->get('my_user_avatar'))."_180x180.jpg";
          }
          /*else{
              $avatar_svg = 'templates/lasercity/img/addphoto--white.svg';
          }*/
          ?>
        <img class="salon-addcomment__autor-image" src="<?=$avatar?>" title="" alt="">
        <div class="salon-addcomment__autor-image-file" id="upload_avatar" <?=(!empty($avatar) ? 'style="background: #ededed url(/'.$avatar.') no-repeat center center;background-size: cover;text-align:center' : '')?>">
            <input type="hidden" name="file_avatar">
          <!--<input class="visually-hidden autor-image__file-input" aria-label="Добавить фотографии к коментарию" id="autor-image__file" type="file">-->
          <label for="autor-image__file" tabindex="-1"></label>
          <p class="salon-addcomment__autor-file-text"><?=JText::_('COM_LASERCITY_ALL_LOAD_AVATAR')?></p>
        </div>
      </div>
      <div class="salon-addcomment__autor-name-wrapper">
        <p class="salon-addcomment__autor-name"></p>
        <ul class="salon-addcomment__autor-noregister-list">
          <li class="salon-addcomment__autor-noregister-item">
            <label>
                <?=JText::_('COM_LASERCITY_ALL_LABEL_NAME')?>:
              <input type="text" name="name" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_NAME')?>" value="<?=(!empty(JFactory::getSession()->get('my_user_name')) ? JFactory::getSession()->get('my_user_name') : '')?>">
            </label>
          </li>
          <li class="salon-addcomment__autor-noregister-item">
            <label>
                <?=JText::_('COM_LASERCITY_ALL_LABEL_EMAIL')?>:
              <input type="text" name="email" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_EMAIL')?>">
            </label>
          </li>
          <li class="salon-addcomment__autor-noregister-item">
            <label>
                <?=JText::_('COM_LASERCITY_ALL_LABEL_PHONE')?>:
              <input type="text" name="phone" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_PHONE')?>">
            </label>
          </li>
        </ul>
      </div>
    </div>
    <div class="salon-addcomment__visit-time-wrapper">
      <p class="salon-addcomment__visit-time-title"><?=JText::_('COM_LASERCITY_FORM_REVIEW_P_2')?>:</p>
      <ul class="salon-addcomment__visit-time-list">
          <li class="salon-addcomment__visit-time-item">
              <label class="salon-addcomment__visit-time-label">
                  Дата
                  <input type="text" id="send_date" name="date_visit">
              </label>
          </li>
        <!--<li class="salon-addcomment__visit-time-item">
          <label class="salon-addcomment__visit-time-label">
            Число
            <input class="salon-addcomment__visit-time-input" type="text" name="Data" aria-label="Введите число когда вы посещали салон">
          </label>
        </li>
        <li class="salon-addcomment__visit-time-item">
          <label class="salon-addcomment__visit-time-label">
            Месяц
            <input class="salon-addcomment__visit-time-input" type="text" name="Month" aria-label="Введите месяц когда вы посещали салон">
          </label>
        </li>
        <li class="salon-addcomment__visit-time-item">
          <label class="salon-addcomment__visit-time-label">
            Год
            <input class="salon-addcomment__visit-time-input" type="text" name="Year" aria-label="Введите год когда вы посещали салон">
          </label>
        </li>
        <li class="salon-addcomment__visit-time-item">
          <label class="salon-addcomment__visit-time-label">
            Время
            <input class="salon-addcomment__visit-time-input salon-addcomment__visit-time-input--time" type="text" name="Time" aria-label="Введите время когда вы посещали салон">
          </label>
        </li>-->
      </ul>
    </div>
    <ul class="salon-addcomment__ratings-list ">
      <li class="salon-addcomment__ratings-item salon-addcomment__ratings-item--place">
        <h4 class="salon-addcomment__rating-title"><?=JText::_('COM_LASERCITY_FORM_REVIEW_H4_1')?></h4>
        <div class="salon-addcomment__rating-group-wrapper">
          <p class="salon-addcomment__rating-group">
            <input class="salon-addcomment__rating-point" type="radio" name="place" value="1" aria-label="Ужасно">
            <input class="salon-addcomment__rating-point" type="radio" name="place" value="2" aria-label="Сносно">
            <input class="salon-addcomment__rating-point" type="radio" name="place" value="3" aria-label="Нормально">
            <input class="salon-addcomment__rating-point" type="radio" name="place" value="4" aria-label="Хорошо" checked>
            <input class="salon-addcomment__rating-point" type="radio" name="place" value="5" aria-label="Отлично">
          </p>
          <output class="salon-addcomment__rating-point-total">4</output>
        </div>
      </li>
      <li class="salon-addcomment__ratings-item salon-addcomment__ratings-item--relationship">
        <h4 class="salon-addcomment__rating-title"><?=JText::_('COM_LASERCITY_FORM_REVIEW_H4_2')?></h4>
        <div class="salon-addcomment__rating-group-wrapper">
          <p class="salon-addcomment__rating-group">
            <input class="salon-addcomment__rating-point" type="radio" name="relationship" value="1" aria-label="Ужасно">
            <input class="salon-addcomment__rating-point" type="radio" name="relationship" value="2" aria-label="Сносно">
            <input class="salon-addcomment__rating-point" type="radio" name="relationship" value="3" aria-label="Нормально">
            <input class="salon-addcomment__rating-point" type="radio" name="relationship" value="4" aria-label="Хорошо">
            <input class="salon-addcomment__rating-point" type="radio" name="relationship" value="5" aria-label="Отлично" checked>
          </p>
          <output class="salon-addcomment__rating-point-total">5</output>
        </div>
      </li>
      <li class="salon-addcomment__ratings-item salon-addcomment__ratings-item--purity">
        <h4 class="salon-addcomment__rating-title"><?=JText::_('COM_LASERCITY_FORM_REVIEW_H4_3')?></h4>
        <div class="salon-addcomment__rating-group-wrapper">
          <p class="salon-addcomment__rating-group">
            <input class="salon-addcomment__rating-point" type="radio" name="purity" value="1" aria-label="Ужасно">
            <input class="salon-addcomment__rating-point" type="radio" name="purity" value="2" aria-label="Сносно">
            <input class="salon-addcomment__rating-point" type="radio" name="purity" value="3" aria-label="Нормально">
            <input class="salon-addcomment__rating-point" type="radio" name="purity" value="4" aria-label="Хорошо" checked>
            <input class="salon-addcomment__rating-point" type="radio" name="purity" value="5" aria-label="Отлично">
          </p>
          <output class="salon-addcomment__rating-point-total">4</output>
        </div>
      </li>
      <li class="salon-addcomment__ratings-item salon-addcomment__ratings-item--quality">
        <h4 class="salon-addcomment__rating-title"><?=JText::_('COM_LASERCITY_FORM_REVIEW_H4_4')?></h4>
        <div class="salon-addcomment__rating-group-wrapper">
          <p class="salon-addcomment__rating-group">
            <input class="salon-addcomment__rating-point" type="radio" name="quality" value="1" aria-label="Ужасно">
            <input class="salon-addcomment__rating-point" type="radio" name="quality" value="2" aria-label="Сносно">
            <input class="salon-addcomment__rating-point" type="radio" name="quality" value="3" aria-label="Нормально" checked>
            <input class="salon-addcomment__rating-point" type="radio" name="quality" value="4" aria-label="Хорошо">
            <input class="salon-addcomment__rating-point" type="radio" name="quality" value="5" aria-label="Отлично">
          </p>
          <output class="salon-addcomment__rating-point-total">3</output>
        </div>
      </li>
      <li class="salon-addcomment__ratings-item salon-addcomment__ratings-item--price">
        <h4 class="salon-addcomment__rating-title"><?=JText::_('COM_LASERCITY_FORM_REVIEW_H4_5')?></h4>
        <div class="salon-addcomment__rating-group-wrapper ">
          <p class="salon-addcomment__rating-group">
            <input class="salon-addcomment__rating-point" type="radio" name="price" value="1" aria-label="Ужасно">
            <input class="salon-addcomment__rating-point" type="radio" name="price" value="2" aria-label="Сносно">
            <input class="salon-addcomment__rating-point" type="radio" name="price" value="3" aria-label="Нормально">
            <input class="salon-addcomment__rating-point" type="radio" name="price" value="4" aria-label="Хорошо">
            <input class="salon-addcomment__rating-point" type="radio" name="price" value="5" aria-label="Отлично" checked>
          </p>
          <output class="salon-addcomment__rating-point-total">5</output>
        </div>
      </li>
    </ul>
    <ul class="salon-addcomment__ratings-list-total">
      <li class="salon-addcomment__ratings-total-item salon-addcomment__ratings-total-item--color">
        <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_RAITING_GOOD')?></span>
      </li>
      <li class="salon-addcomment__ratings-total-item salon-addcomment__ratings-total-item--color">
        <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_RAITING_GOOD')?></span>
      </li>
      <li class="salon-addcomment__ratings-total-item salon-addcomment__ratings-total-item--color">
        <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_RAITING_GOOD')?></span>
      </li>
      <li class="salon-addcomment__ratings-total-item salon-addcomment__ratings-total-item--color">
        <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_RAITING_GOOD')?></span>
      </li>
      <li class="salon-addcomment__ratings-total-item salon-addcomment__ratings-total-item--transparent">
        <span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_RAITING_NO_GOOD')?></span>
      </li>
    </ul>

    <textarea class="salon-addcomment__text" placeholder="<?=JText::_('COM_LASERCITY_FORM_REVIEW_TEXTAREA_1')?>" name="send_comment" aria-label="<?=JText::_('COM_LASERCITY_FORM_REVIEW_TEXTAREA_1')?>"></textarea>
    <div class="salon-addcomment__file button-addfile snbtnup" id="addcomment_file">
      <!--<input class="visually-hidden faq-discussion__addcomment-input button-addfile__input" aria-label="Добавить фотографии к коментариям" id="addcomment-file" type="file">-->
      <label class="button-addfile__wrapper" for="addcomment-file" tabindex="-1"></label>
      <!--<p class="button-addfile__text">Добавить фото</p>-->
        <div class="perr" data-txt="<?=JText::_('COM_LASERCITY_ALL_FORM_ADDFOTO')?>">
            <p class="button-addfile__text"><?=JText::_('COM_LASERCITY_ALL_FORM_ADDFOTO')?></p>
        </div>
    </div>
      <div class="salon-addcomment__file button-addfile snbtnup1">
          <label class="button-addfile__wrapper" for="addcomment-file" tabindex="-1"></label>
          <p class="button-addfile__text"><?=JText::_('COM_LASERCITY_ALL_FORM_ADDFOTO')?></p>
      </div>

      <p class="ploadf" data-er="<?=JText::_('COM_LASERCITY_ALL_FORM_ERROR_FOTO_FORMAT')?>" data-er1="<?=JText::_('COM_LASERCITY_ALL_FORM_ERROR_FOTO_MAX')?>"></p>
    <div class="salon-addcomment__rulles">
      <label class="salon-addcomment__rulles-captch label">
          <?php include ($_SERVER['DOCUMENT_ROOT'].'/templates/lasercity/recaptcha/html.php');?>
        <!--<input type="checkbox">
        Я не робот! вспомнить Google-->
      </label>
      <a class="salon-addcomment__rulles-conf" target="_blank" href="/privacy/"><?=JText::_('COM_LASERCITY_FORM_REVIEW_A_1')?></a>
    </div>

      <div class="status"></div>

    <div class="salon-addcomment__buttons">
      <button class="salon-addcomment__button salon-addcomment__button--cancel button button--gray" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_CANSEL')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_CANSEL')?>"><?=JText::_('COM_LASERCITY_ALL_FORM_CANSEL')?></button>
      <button class="salon-addcomment__button salon-addcomment__button--publish button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_YES_SEND')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_YES_SEND')?>"><?=JText::_('COM_LASERCITY_ALL_FORM_YES_SEND')?></button>
    </div>
    <button class="salon-addcomment__closed button-cross" type="button" title="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>" aria-label="<?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL')?></span></button>
  </form>
</div>

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

    .error {
      border: 1px solid #d4124c;
    }

    .text-danger {
      color: #d4124c;
      width: 100%;
      font-size: 13px;
    }
    .snbtnup1{
      display: none;
    }
    .snbtnup{
      cursor: pointer;
    }
    .sndft{
      float: left;
      clear: both;
      width: 100%;
      padding: 5px 0 5px 27px;
      position: relative;
      background: url("/templates/lasercity/img/skrepka.png") no-repeat 3px center;
      white-space: nowrap;
    }
    .delet{
      cursor: pointer;
      margin-left: 10px;
    }
    .delet:hover{
      color:#d4124c;
    }


    /*календарь*/

</style>

<script>

    document.addEventListener('DOMContentLoaded', function() {

        $(document).ready(function () {

            $("#send_date").MyDtime({times: true, language: '<?=LangHelper::getCurrentSef()?>'});

            function addError(el, text) {
                $(el).after('<div class="text-danger">' + text + '</div>');
            }

            $('.salon-addcomment__form [name=name],.salon-addcomment__form [name=phone],.salon-addcomment__form [name=email]').on('focusout', function () {
                $('.salon-addcomment__button.salon-addcomment__button--publish').click();
            });

            $('.salon-addcomment__address').text($('body h1').text());

            $('.salon-addcomment__button.salon-addcomment__button--publish').on('click', function () {
                $('.status').hide();
                $('.text-danger').remove();
                $('.salon-addcomment__form .error').removeClass('error');

                $.ajax({
                    data: $('.salon-addcomment__form').serialize(),
                    url: '/?option=com_lasercity&task=addReview&affiliate=<?=JRequest::getInt('id')?>',
                    dataType: 'json',
                    success: function (json) {
                        if (json.success) {
                            $('.salon-addcomment__form input[type=text]:not([type=radio])').val('');
                            $('.salon-addcomment__form textarea').val('');
                            $('#upload_avatar').css('background', "#ededed url(/templates/lasercity/img/addphoto--white.svg) no-repeat 50% 20px");
                            $(".ploadf").html("");
                            $('.status').html(json.success).show();

                            $(".snbtnup1").hide();
                            $(".snbtnup").show();
                        }

                        if (json.error) {
                            $.each(json.error, function (key, value) {
                                $('.salon-addcomment__form [name=' + key + ']').addClass('error');
                            });
                            if (json.error['name']) {
                                addError($('.salon-addcomment__form [name=name]').parent(), json.error['name']);
                            }
                            if (json.error['phone']) {
                                addError($('.salon-addcomment__form [name=phone]').parent(), json.error['phone']);
                            }
                            if (json.error['email']) {
                                addError($('.salon-addcomment__form [name=email]').parent(), json.error['email']);
                            }
                            if (json.error['captcha']) {
                                addError($('.salon-addcomment__form [name=captcha]').parent(), json.error['captcha']);
                                $('.salon-addcomment__form [name=captcha]').removeClass('error');
                            }
                            $('.salon-addcomment__form .error:first').trigger('focus');
                        }
                    }
                });
                return false;
            });

        });
    });
</script>

