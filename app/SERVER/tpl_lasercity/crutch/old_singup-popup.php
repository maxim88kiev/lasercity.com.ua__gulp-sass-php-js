<?php defined('_JEXEC') or die; ?>

<div class="popup-singup">
  <div class="popup-singup__modal popup-singup__modal--login popup-login popup-singup__modal--active">
    <h3 class="popup-login__title" id="LoginPopup">Вход в личный кабинет</h3>
    <form class="popup-login__form send-enter-form" method="post">
      <input class="popup-login__email" type="text" aria-label="Введите E-mail" name="enter_email" placeholder="E-mail">
      <label class="popup-login__password-label">
        <input class="popup-login__password" type="password" aria-label="Ведите пароль" name="enter_password" placeholder="Пароль">
        <button class="popup-login__password-button" type="button" title="Показать пароль" aria-label="Показать пароль">
          <svg fill="#cccccc" width="18" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 511.999 511.999" aria-hidden="true">
            <path d="M508.745,246.041c-4.574-6.257-113.557-153.206-252.748-153.206S7.818,239.784,3.249,246.035 c-4.332,5.936-4.332,13.987,0,19.923c4.569,6.257,113.557,153.206,252.748,153.206s248.174-146.95,252.748-153.201 C513.083,260.028,513.083,251.971,508.745,246.041z M255.997,385.406c-102.529,0-191.33-97.533-217.617-129.418 c26.253-31.913,114.868-129.395,217.617-129.395c102.524,0,191.319,97.516,217.617,129.418 C447.361,287.923,358.746,385.406,255.997,385.406z"></path>
            <path d="M255.997,154.725c-55.842,0-101.275,45.433-101.275,101.275s45.433,101.275,101.275,101.275 s101.275-45.433,101.275-101.275S311.839,154.725,255.997,154.725z M255.997,323.516c-37.23,0-67.516-30.287-67.516-67.516 s30.287-67.516,67.516-67.516s67.516,30.287,67.516,67.516S293.227,323.516,255.997,323.516z"></path>
          </svg>
          <span class="visually-hidden">Показать пароль</span>
        </button>
      </label>
        <div class="box-fb">
            <a class="google-button" href="#">Продолжить с Google</a>
            <br>
            <a class="facebook-button" href="#">Продолжить с Facebook</a>
        </div>
      <button class="popup-login__button button">Вход</button>
      <div class="popup-login__options-wrapper">
        <label class="popup-login__remember label">
          <input type="checkbox" name="save_me">
          Запомнить меня
        </label>
          <div class="status for-enter"></div>
        <button class="popup-login__forgot">Забыли пароль?</button>
      </div>
    </form>
    <button class="popup-login__register-button button">Зарегистироваться</button>
  </div>
  <div class="popup-singup__modal popup-singup__modal--register popup-register ">
    <h3 class="popup-register__title">Регистрация</h3>
    <form class="popup-register__form send-account-form" method="post">
      <input class="popup-register__for popup-register__for--user" type="radio" name="register_for" value="user" id="register-for-user" checked>
      <input class="popup-register__for popup-register__for--organization" type="radio" name="register_for" value="organization" id="register-for-organization">
      <p class="popup-register__choice">
        <label for="register-for-user">Как пользователь</label>
        <label for="register-for-organization">Как организация</label>
      </p>
      <input class="popup-register__input" type="text" aria-label="Введите ваше ФИО" name="name" placeholder="ФИО">
        <div class="form-groupe box-organization">
            <input type="hidden" name="organization">
            <input id="input_organization" type="text" aria-label="Введите название организации" data-oldvalue="" autocomplete="off" name="organization_sel" placeholder="Название организации">
        </div>
      <input name="phone" class="popup-register__input popup-register__input--organization" type="text" aria-label="Введите номер телефона" placeholder="+38 (000) 00-00-00">
      <input name="email" class="popup-register__input" type="text" aria-label="Введите E-mail" placeholder="E-mail">
      <input name="password" class="popup-register__input" type="password" aria-label="Введите пароль" placeholder="Пароль">
      <input name="repeat_password" class="popup-register__input" type="password" aria-label="Повторите пароль" placeholder="Повторите пароль">

        <input name="registration" type="hidden">

        <div class="status for-register"></div>
      <button class="popup-register__register button btn-register">Зарегистироваться</button>
    </form>
    <button class="popup-register__login-button button">Вход</button>
  </div>
  <button class="popup-singup__closed button-cross" type="button" title="Закрыть модальное окно" aria-label="Закрыть модальное окно"><span class="visually-hidden">Закрыть модальное окно</span></button>
</div>

<?php
echo "<br>session:my_user_id=".JFactory::getSession()->get('my_user_id');
echo "<br>session:tip_user=".JFactory::getSession()->get('tip_user');
echo "<br>session:organization_id=".JFactory::getSession()->get('organization_id');

echo "<br>cookie:my_user_id=".JFactory::getApplication()->input->cookie->get('my_user_id');
echo "<br>cookie:tip_user=".JFactory::getApplication()->input->cookie->get('tip_user');
echo "<br>cookie:organization_id=".JFactory::getApplication()->input->cookie->get('organization_id');
?>

<style>
    .error{
        border: 1px solid #d4124c;
    }
    .text-danger{
        color:#d4124c;
    }
    .status{
        display: none;
        color:#000;
        padding: 15px;
        background: #09f28c;
        border-radius: 3px;
        margin-top: 10px;
        text-align: center;
    }
    ul.dropdown-menu{
        display: none;
        background: #fff;
        position: absolute;
        left: 0;
        width: 100%;
        overflow: hidden;
        overflow-y: scroll;
        height: 180px;
        border: 1px solid #d7d7d7;
        text-align: left;
        list-style: none;
        padding: 0;
        margin-top: 0;
    }
    ul.dropdown-menu li{
        margin: 10px 0;
        padding: 0 0 0 15px;
    }
    ul.dropdown-menu li a{
        color:#000;
        font-size: 14px;
        text-decoration: none;
    }
    ul.dropdown-menu li a:hover{
        color:#e20864;
    }
    .form-groupe{
        position:relative;
        display: block;
        min-height: 30px;
        margin-top: 5px;
    }
    .form-groupe input{
        width: 100%;
        height: 30px;
        padding: 3px 5px 0 15px;
        font-size: 16px;
    }
    .box-organization{
        display: none;
    }
</style>

<script src="/templates/lasercity/js/jquery-3.3.1.min.js" type="text/javascript"></script>
<script src="/templates/lasercity/js/jquery.mask.min.js" type="text/javascript"></script>
<script>

    jQuery(function($) {

        (function($) {
            $.fn.autocomplete = function(option) {
                return this.each(function() {
                    this.timer = null;
                    this.items = new Array();

                    $.extend(this, option);

                    $(this).attr('autocomplete', 'off');

                    // Focus
                    $(this).on('focus', function() {
                        this.request();
                    });

                    // Blur
                    $(this).on('blur', function() {
                        setTimeout(function(object) {
                            object.hide();
                        }, 200, this);
                    });

                    // Keydown
                    $(this).on('keydown', function(event) {
                        switch(event.keyCode) {
                            case 27: // escape
                                this.hide();
                                break;
                            default:
                                this.request();
                                break;
                        }
                    });

                    // Click
                    this.click = function(event) {
                        event.preventDefault();

                        value = $(event.target).parent().attr('data-value');

                        if (value && this.items[value]) {
                            this.select(this.items[value]);
                        }
                    }

                    // Show
                    this.show = function() {
                        var pos = $(this).position();

                        $(this).siblings('ul.dropdown-menu').css({
                            top: pos.top + $(this).outerHeight(),
                            left: pos.left
                        });

                        $(this).siblings('ul.dropdown-menu').show();
                    }

                    // Hide
                    this.hide = function() {
                        $(this).siblings('ul.dropdown-menu').hide();
                    }

                    // Request
                    this.request = function() {
                        clearTimeout(this.timer);

                        this.timer = setTimeout(function(object) {
                            object.source($(object).val(), $.proxy(object.response, object));
                        }, 200, this);
                    }

                    // Response
                    this.response = function(json) {
                        html = '';

                        if (json.length) {
                            for (i = 0; i < json.length; i++) {
                                this.items[json[i]['value']] = json[i];
                            }

                            for (i = 0; i < json.length; i++) {
                                if (!json[i]['category']) {
                                    html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
                                }
                            }

                            // Get all the ones with a categories
                            var category = new Array();

                            for (i = 0; i < json.length; i++) {
                                if (json[i]['category']) {
                                    if (!category[json[i]['category']]) {
                                        category[json[i]['category']] = new Array();
                                        category[json[i]['category']]['name'] = json[i]['category'];
                                        category[json[i]['category']]['item'] = new Array();
                                    }

                                    category[json[i]['category']]['item'].push(json[i]);
                                }
                            }

                            for (i in category) {
                                html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

                                for (j = 0; j < category[i]['item'].length; j++) {
                                    html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
                                }
                            }
                        }

                        if (html) {
                            this.show();
                        } else {
                            this.hide();
                        }

                        $(this).siblings('ul.dropdown-menu').html(html);
                    }

                    $(this).after('<ul class="dropdown-menu"></ul>');
                    $(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

                });
            }
        })(window.jQuery);

        $.ajaxSetup({
            cache: false,
            type: "POST"
        });


        $("#input_organization").autocomplete({
            'source': function(request, response) {
                if($.trim($('input[name=\'organization_sel\']').val().length)>0) {
                    $.ajax({
                        url: '/?option=com_lasercity&task=getOrganization&organization='+encodeURIComponent(request),
                        dataType: 'json',
                        success: function (json) {
                            if($('input[name=\'organization_sel\']').data('oldvalue')!=request){
                                $('input[name=\'organization\']').val('');
                            }
                            response($.map(json, function (item) {
                                return {
                                    label: item['value'],
                                    value: item['id']
                                }
                            }));
                        }
                    });
                }
            },
            'select': function(item) {
                //item['label'] -- текст ссылки
                //item['value'] -- значение в data value
                $('input[name=\'organization_sel\']').val(item['label']);
                $('input[name=\'organization_sel\']').data('oldvalue',item['label']);
                $('input[name=\'organization_sel\']').removeClass('error');
                $('input[name=\'organization_sel\']').next('.text-danger').remove();
                $('input[name=\'organization\']').val(item['value']);
            }
        });

        $('input[name="register_for"]').change(function(){
            $('.status').hide();
            $('.text-danger').remove();
            $('.send-account-form .error').removeClass('error');

            if($(this).val()=='organization'){
                $('.box-organization').show();
            }
            else{
                $('.box-organization').hide();
            }
        });

        function addError(el, text) {
            $(el).after('<div class="text-danger">' + text + '</div>');
        }

        $('.popup-register__login-button').on('click',function(){
            $('.status').hide();
            $('.text-danger').remove();
            $('.send-account-form .error').removeClass('error');
        });

        $('.btn-register').on('click',function() {
            $('.status').hide();
            $('.text-danger').remove();
            $('.send-account-form .error').removeClass('error');

            $.ajax({
                data: $('.send-account-form').serialize(),
                url: '/?option=com_lasercity&task=setRegistration',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('.send-account-form input[type=text]').val('');
                        $('.send-account-form input[type=password]').val('');
                        $('.status.for-register').html(json.success).show();
                    }

                    if (json.error) {
                        $.each(json.error, function (key, value) {
                            $('.send-account-form [name=' + key + ']').addClass('error');
                        });
                        if (json.error['name']) {
                            addError($('.send-account-form [name=name]'), json.error['name']);
                        }
                        if (json.error['organization_sel']) {
                            addError($('.send-account-form [name=organization_sel]'), json.error['organization_sel']);
                        }
                        if (json.error['email']) {
                            addError($('.send-account-form [name=email]'), json.error['email']);
                        }
                        if (json.error['phone']) {
                            addError($('.send-account-form [name=phone]'), json.error['phone']);
                        }
                        if (json.error['password']) {
                            addError($('.send-account-form [name=password]'), json.error['password']);
                        }
                        if (json.error['repeat_password']) {
                            addError($('.send-account-form [name=repeat_password]'), json.error['repeat_password']);
                        }
                        $('.send-account-form .error:first').trigger('focus');
                    }
                }
            });

            return false;
        });

        /*$(".send-account-form [name=phone]").mask('+38 (000) 000-00-00', {
            placeholder: ""
        });*/


        $.ajax({
            url: '/?option=com_lasercity&task=initGoogle',
            dataType: 'json',
            success: function (json) {
                if (json.link_button) {
                    $(".google-button").attr('href',json.link_button);
                }
            }
        });
        $.ajax({
            url: '/?option=com_lasercity&task=initFacebook',
            dataType: 'json',
            success: function (json) {
                if (json.link_button) {
                    $(".facebook-button").attr('href',json.link_button);
                }
            }
        });

        $('.popup-login__button').on('click',function() {
            $('.status').hide();
            $('.text-danger').remove();
            $('.send-enter-form .error').removeClass('error');

            $.ajax({
                data: $('.send-enter-form').serialize(),
                url: '/?option=com_lasercity&task=setEnter',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('.send-enter-form input[type=text]').val('');
                        $('.send-enter-form input[type=password]').val('');
                        $('.popup-singup__closed.button-cross').click();
                    }

                    if (json.error) {
                        $.each(json.error, function (key, value) {
                            $('.send-enter-form [name=' + key + ']').addClass('error');
                        });
                        if (json.error['enter_password']) {
                            addError($('.send-enter-form [name=enter_password]'), json.error['enter_password']);
                        }
                        $('.send-enter-form .error:first').trigger('focus');
                    }
                }
            });

            return false;
        });


    });
</script>