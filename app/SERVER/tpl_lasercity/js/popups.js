document.addEventListener('DOMContentLoaded', function() {
    $(document).ready(function () {

        $.ajaxSetup({
            cache: false,
            type: "POST"
        });

        $(document).on('click','.addDtime',function () {
            $(this).next('.text-danger').remove();
        });

        $('.select-date__wish-button').on('click',function () {
            $(this).closest('form').find('.addDtime').click();
        });

        /*$("input[name=phone]").mask('+38 (000) 000-00-00', {
            placeholder: ""
        });*/

        /*$(".salon-addcomment__form [name=PhoneNumber]").mask('+38 (000) 000-00-00', {
            placeholder: ""
        });*/

        function addError(el, text) {
            $(el).after('<span class="text-danger">' + text + '</span>');
        }

        //задать вопрос
        $(document).on('click','.salon__contact-question,.salons__callback-question,.branches-list__question', function () {
            $(".popup-qeustion__form [name=affiliate]").val($(this).data('affiliate'));
        });

        $('.popup-question__button.button').on('click', function () {
            $('.status').hide();
            $('.text-danger').remove();
            $('.popup-qeustion__form .error').removeClass('error');

            $.ajax({
                data: $('.popup-qeustion__form').serialize(),
                url: '/?option=com_lasercity&task=setQuestions',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('.popup-qeustion__form input[type=text]').val('');
                        $('.popup-qeustion__form textarea').val('');
                        $('.popup-qeustion__form .status').html(json.success).show();
                    }
                    $('.text-danger').remove();
                    $('.popup-qeustion__form .error').removeClass('error');

                    if (json.error) {
                        $.each(json.error, function (key, value) {
                            $('.popup-qeustion__form [name=' + key + ']').addClass('error');
                        });
                        if (json.error['name']) {
                            addError($('.popup-qeustion__form [name=name]'), json.error['name']);
                        }
                        if (json.error['phone']) {
                            addError($('.popup-qeustion__form [name=phone]'), json.error['phone']);
                        }
                        if (json.error['email']) {
                            addError($('.popup-qeustion__form [name=email]'), json.error['email']);
                        }
                        /*if (json.error['affiliate']) {
                         addError($('.popup-qeustion__form [name=affiliate]'), json.error['affiliate']);
                         }*/
                        if (json.error['koment']) {
                            addError($('.popup-qeustion__form [name=koment]'), json.error['koment']);
                        }
                        $('.popup-qeustion__form .error:first').trigger('focus');
                    }
                }
            });

            return false;
        });

        //подписка
        function addSuccess(el, text) {
            $(el).after('<div class="text-success">' + text + '</div>');
        }

        $('.mailing__form,.faq-discussion-popup__form,.faq-discussion__addcomment-sent').submit(function () {
            return false;
        });

        $('.mailing__button').on('click', function () {
            $('.text-danger,.text-success').remove();
            $('.mailing__form .error').removeClass('error');

            $.ajax({
                data: $('.mailing__form').serialize(),
                url: '/?option=com_lasercity&task=setSubscription',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('.mailing__form input[type=text]').val('');
                        addSuccess($('.mailing__form [name=mailing]'), json.success);
                    }

                    if (json.error) {
                        $.each(json.error, function (key, value) {
                            $('.mailing__form [name=' + key + ']').addClass('error');
                        });
                        if (json.error['mailing']) {
                            addError($('.mailing__form [name=mailing]'), json.error['mailing']);
                        }

                        $('.mailing__form .error:first').trigger('focus');
                    }
                }
            });

            return false;
        });

        //ответ
        $('.faq-discussion__addcomment-sent').on('click', function () {
            $('.text-danger').remove();
            $('#function_addcomment_form .error').removeClass('error');

            $.ajax({
                data: $('#function_addcomment_form').serialize(),
                url: '/?option=com_lasercity&task=setAnswer',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('#function_addcomment_form input[type=text]').val('');
                        $('#function_addcomment_form textarea').val('');
                        $('#function_addcomment_form .status').text(json.success).show();
                        $('#function_addcomment_form .ploadf').html('');
                        $('#addcomment_file').show();
                    }

                    if (json.error) {
                        $.each(json.error, function (key, value) {
                            $('#function_addcomment_form [name=' + key + ']').addClass('error');
                        });
                        if (json.error['answer']) {
                            addError($('#function_addcomment_form [name=answer]'), json.error['answer']);
                        }

                        $('#function_addcomment_form .error:first').trigger('focus');
                    }
                }
            });

            return false;
        });

        //автокомпил
        (function ($) {
            $.fn.autocomplete = function (option) {
                return this.each(function () {
                    this.timer = null;
                    this.items = new Array();

                    $.extend(this, option);

                    $(this).attr('autocomplete', 'off');

                    // Focus
                    $(this).on('focus', function () {
                        this.request();
                    });

                    // Blur
                    $(this).on('blur', function () {
                        setTimeout(function (object) {
                            object.hide();
                        }, 200, this);
                    });

                    // Keydown
                    $(this).on('keydown', function (event) {
                        switch (event.keyCode) {
                            case 27: // escape
                                this.hide();
                                break;
                            default:
                                this.request();
                                break;
                        }
                    });

                    // Click
                    this.click = function (event) {
                        event.preventDefault();

                        value = $(event.target).parent().attr('data-value');

                        if (value && this.items[value]) {
                            this.select(this.items[value]);
                        }
                        //console.log(this.items[value]);
                    }

                    // Show
                    this.show = function () {
                        var pos = $(this).position();

                        $(this).siblings('ul.dropdown-menu').css({
                            top: pos.top + $(this).outerHeight()+2,
                            left: pos.left,
                            width: $(this).outerWidth(),
                            position: 'absolute',
                            background: '#fff',
                            //'box-shadow': 'unset',
                            'max-height': '247px',
                            'margin-top': '1px',
                            'border': '1px solid #a2a2a2',
                            'border-radius': '6px',
                            'margin': '0',
                            'list-style': 'none',
                            padding: '0 0 5px',
                            overflow: 'hidden',
                            'overflow-y': 'auto',
                            'box-shadow': '0 10px 25px #444',
                            'border-bottom-right-radius': '10px',
                            'border-bottom-left-radius': '10px'
                        });

                        $(this).siblings('ul.dropdown-menu').show();
                    }

                    // Hide
                    this.hide = function () {
                        $(this).siblings('ul.dropdown-menu').hide();
                    }

                    // Request
                    this.request = function () {
                        clearTimeout(this.timer);

                        this.timer = setTimeout(function (object) {
                            object.source($(object).val(), $.proxy(object.response, object));
                        }, 200, this);
                    }

                    // Response
                    this.response = function (json) {
                        html = '';

                        if (json.length) {
                            for (i = 0; i < json.length; i++) {
                                this.items[json[i]['value']] = json[i];
                            }

                            for (i = 0; i < json.length; i++) {
                                if (!json[i]['category']) {
                                    json[i]['option'] = json[i]['option']!==undefined ? json[i]['option'] : '';

                                    json[i]['persones'] = json[i]['persones']!==undefined ? json[i]['persones'] : '';

                                    $class = '';

                                    if(json[i]['persones']==0){
                                        $class = 'autocomplete__item--woman';
                                    }
                                    else if(json[i]['persones']==1){
                                        $class = 'autocomplete__item--man';
                                    }

                                    if($class!='') {
                                        html += '<li class="autocomplete__item ' + $class + '" data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a><span>' + json[i]['option'] + '</span></li>';
                                    }
                                    else {
                                        html += '<li class="autocomplete__item" data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a><span>' + json[i]['option'] + '</span></li>';
                                    }
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
                                html += '<li class="dropdown-header autocomplete__item">' + category[i]['name'] + '</li>';

                                for (j = 0; j < category[i]['item'].length; j++) {

                                    json[i]['option'] = json[i]['option']!==undefined ? json[i]['option'] : '';
                                    html += '<li class="autocomplete__item" data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a><span>' + json[i]['option'] + '</span></li>';
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
                    $(this).siblings('ul.dropdown-menu').delegate('span', 'click', $.proxy(this.click, this));

                });
            }
        })(window.jQuery);

        /*$(document).on('click','ul.dropdown-menu span',function(){
            $(this).prev('a').click();
        });*/

        //смена входа/регистрации
        /*$('input[name="register_for"]').change(function () {
            $('.status').hide();
            $('.send-account-form .error').removeClass('error');

            if ($(this).val() == 'organization') {
                $('.box-organization').show();
            } else {
                $('.box-organization').hide();
            }
        });*/


        $('.popup-login__register-button.button,.lasercity__register--organization,.lasercity__register--customers').on('click', function () {
            $('.text-danger').remove();
            $('.send-account-form .error').removeClass('error');
        });

        /*$('.send-account-form [name=name],.send-account-form [name=organization_sel],.send-account-form [name=email],.send-account-form [name=phone],.send-account-form [name=password],.send-account-form [name=repeat_password]').on('change', function () {
            //$('.text-danger').remove();
            //$('.send-account-form .error').removeClass('error');
            $('.btn-register').click();
        });*/

        $('.btn-register').on('click', function () {
            $('.status').hide();
            $('.text-danger').remove();
            //$('.send-account-form .error').removeClass('error');
            $('.send-account-form .register__who-label.register__who-label--error').removeClass('register__who-label').removeClass('register__who-label--error');

            $.ajax({
                data: $('.send-account-form').serialize(),
                url: '/?option=com_lasercity&task=setRegistration',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        if (json.uri) {
                            self.location.href = json.uri;
                        }
                        else{
                            $('.send-account-form input[type=text]').val('');
                            $('.send-account-form input[type=password]').val('');
                            $('.status.for-register').html(json.success).show();
                        }
                    }
                    $('.text-danger').remove();
                    //$('.send-account-form .error').removeClass('error');
                    $('.send-account-form .register__who-label.register__who-label--error').removeClass('register__who-label').removeClass('register__who-label--error');

                    if (json.error) {
                        $.each(json.error, function (key, value) {
                            //$('.send-account-form [name=' + key + ']').addClass('error');
                            $('.send-account-form [name=' + key + ']').closest('label').addClass('register__who-label').addClass('register__who-label--error');
                        });
                        if (json.error['name']) {
                            addError($('.send-account-form [name=name]'), json.error['name']);
                        }
                        /*if (json.error['organization_sel']) {
                            addError($('.send-account-form [name=organization_sel]'), json.error['organization_sel']);
                        }*/
                        if (json.error['email']) {
                            addError($('.send-account-form [name=email]'), json.error['email']);
                        }
                        if (json.error['phone']) {
                            addError($('.send-account-form [name=phone]'), json.error['phone']);
                        }
                        if (json.error['password']) {
                            addError($('.send-account-form [name=password]'), json.error['password']);
                        }
                        if (json.error['ruls']) {
                            addError($('.send-account-form .accept-rules__rule'), json.error['ruls']);
                        }
                        /*if (json.error['repeat_password']) {
                            addError($('.send-account-form [name=repeat_password]'), json.error['repeat_password']);
                        }*/
                        $('.send-account-form .text-danger').addClass('register__who-input-error').show();
                        //$('.send-account-form .error:first').trigger('focus');
                    }
                }
            });

            return false;
        });

        //вход
        $(document).on('click', '.user-list__link', function () {
            $('.send-enter-form input:first').trigger('focus');
        });
        $(document).on('click', '.popup-login__button', function () {
            $('.status').hide();
            $('.text-danger').remove();
            //$('.send-enter-form .popup-login__input-error').removeClass('popup-login__input-error');
            $('.send-enter-form .popup-login__label--error').removeClass('popup-login__label--error');

            $.ajax({
                data: $('.send-enter-form').serialize(),
                url: '/?option=com_lasercity&task=setEnter',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('.send-enter-form input[type=text]').val('');
                        $('.send-enter-form input[type=password]').val('');
                        $('.popup-login__closed').click();
                        if (json.uri) {
                            self.location.href = json.uri;
                        }
                    }
                    $('.text-danger').remove();
                    //$('.send-enter-form .popup-login__input-error').removeClass('popup-login__input-error');
                    $('.send-enter-form .popup-login__label--error').removeClass('popup-login__label--error');

                    if (json.error) {
                        $.each(json.error, function (key, value) {
                            //$('.send-enter-form [name=' + key + ']').addClass('popup-login__input-error');
                            $('.send-enter-form [name=' + key + ']').closest('label').addClass('popup-login__label--error');
                        });
                        if (json.error['enter_password']) {
                            addError($('.send-enter-form [name=enter_password]'), json.error['enter_password']);
                        }
                        $('.send-enter-form .popup-login__label--error:first input').trigger('focus');
                        $('.send-enter-form .text-danger').addClass('popup-login__input-error').show();
                    }
                }
            });

            return false;
        });

        //загрузка фото
        if ($('#upload_avatar').length) {
            $(function () {
                var btnUpload = $('#upload_avatar');
                new AjaxUpload(btnUpload, {
                    action: '/?option=com_lasercity&task=loadAvatar',
                    //Имя  загружаемого файла
                    name: 'uploadfile',
                    responseType: 'json',
                    title: '',
                    append: 'upload_avatar',
                    cache: false,
                    onSubmit: function (file, ext) {
                        $('.text-danger').remove();
                        $('.salon-addcomment__form .error').removeClass('error');

                        if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                            // Проверяет правильность расширения файла
                            //$('.stock-salon__button.button').click();
                            addError($('#upload_avatar'), 'только jpg, png, jpeg, gif файлы могут быть загружены');
                            return false;
                        }
                    },
                    onComplete: function (file, data) {
                        //$('.stock-salon__button.button').click();
                        if (data.src) {
                            $('input[name="file_avatar"]').val(data.avatar);
                            $('#upload_avatar').css('background', "url(" + data.src + ")");
                        }
                        if (data.error) {
                            addError($('#upload_avatar'), data.error);
                        }
                    }
                });
            });
        }

        if ($('.snbtnup').length) {
            $(function () {
                var btnUpload = $('.snbtnup'), strok;
                new AjaxUpload(btnUpload, {
                    action: '/?option=com_lasercity&task=loadFiles&count=' + $(".sndft").length,
                    //Имя  загружаемого файла
                    name: 'uploadfiles',
                    responseType: 'json',
                    title: '',
                    append: 'addcomment_file',
                    cache: false,
                    onSubmit: function (file, ext) {
                        $('.text-danger').remove();
                        $('.salon-addcomment__form .error').removeClass('error');

                        if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                            // Проверяет правильность расширения файла
                            //$('.stock-salon__button.button').click();
                            addError($('.snbtnup'), $('.ploadf').attr('data-er'));
                            return false;
                        }
                        else if ($(".sndft").length >= 5) {
                            //$('.stock-salon__button.button').click();
                            addError($('.snbtnup1'), $('.ploadf').attr('data-er1'));
                            return false;
                        }
                    },
                    onComplete: function (file, data, ext) {

                        $('.stock-salon__button.button').click();

                        if ($(".sndft").length >= 4) {
                            $(".snbtnup").hide();
                            $(".snbtnup1").show();
                        }
                        else {
                            $(".snbtnup1").hide();
                            $(".snbtnup").show();
                        }

                        if (!data.error) {
                            if (file.length > 40) {
                                strok = file.substr(0, 40) + '.' + ext;
                            } else {
                                strok = file;
                            }
                            $(".ploadf").append("<span class='sndft'>" + strok + "<span class='delet' data-rel='" + data.rel + "'>✖</span></span><input type='hidden' name='load_file[]' value='" + data.rel + "'>");
                        }

                        if (data.error) {
                            $(".snbtnup1").hide();
                            $(".snbtnup").show();
                            addError($('.snbtnup'), data.error);
                        }
                    }
                });
            });
        }

        if ($('.snbtnup-otvet').length) {
            $(function () {
                var btnUpload = $('.snbtnup-otvet'), strok;
                new AjaxUpload(btnUpload, {
                    action: '/?option=com_lasercity&task=loadFiles&count=' + $(".sndft").length,
                    //Имя  загружаемого файла
                    name: 'uploadfiles',
                    responseType: 'json',
                    title: '',
                    append: 'addcomment_file',
                    cache: false,
                    onSubmit: function (file, ext) {

                        if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                            // Проверяет правильность расширения файла
                            //$('.stock-salon__button.button').click();
                            addError($('.snbtnup-otvet'), $('.ploadf').attr('data-er'));
                            return false;
                        }
                    },
                    onComplete: function (file, data, ext) {

                        if (!data.error) {
                            if (file.length > 40) {
                                strok = file.substr(0, 40) + '.' + ext;
                            } else {
                                strok = file;
                            }
                            $(".snbtnup-otvet").hide();
                            $(".ploadf").append("<span class='sndft'>" + strok + "<span class='delet' data-rel='" + data.rel + "'>✖</span></span><input type='hidden' name='load_file' value='" + data.rel + "'>");
                        }

                    }
                });
            });
        }

        var deletFile;
        $(document).on('click', '.delet', function () {
            deletFile = $(this);

            $.ajax({
                dataType: 'json',
                url: '/?option=com_lasercity&task=deleteFile&patch=' + deletFile.attr('data-rel') + '&count=' + $(".sndft").length,
                success: function (data) {

                    if (data.count <= 5) {
                        $('.text-danger').remove();
                        $('.salon-addcomment__form .error').removeClass('error');

                        deletFile.closest('.sndft').remove();
                        $(".snbtnup1").hide();
                        $(".snbtnup").show();
                        $(".snbtnup-otvet").show();
                    }
                }
            });
        });


        $(document).on('click', '.snbtnup1', function () {
            $('.text-danger').remove();
            $('.salon-addcomment__form .error').removeClass('error');

            addError($('.snbtnup1'), $('.ploadf').attr('data-er1'));
        });

        //показать больше филиалов
        $(document).on('click','.salons__salon-show-all-wrapper',function(){

            if($(this).closest('.salons__salon').find('li.box-hidden').length) {
                $(this).closest('.salons__salon').find('li.box-hidden').addClass('box-active').removeClass('box-hidden');
            }
            else {
                $(this).closest('.salons__salon').find('li.box-active').addClass('box-hidden').removeClass('box-active');
            }
        });

        //показать больше филиалов с акциями
        $('.stock__organizers-more').on('click',function(){
            if($('li.box-hidden').length) {
                $('li.box-hidden').addClass('box-active').removeClass('box-hidden');
            }
            else {
                $('li.box-active').addClass('box-hidden').removeClass('box-active');
            }
        });


        //изменить пароль
        $('.password-change__form-button').on('click', function () {
            $('.status').hide();
            $('.text-danger').remove();
            $('.password-change__form .error').removeClass('error');
            $('.password-change__form .password-change__input-wrapper--error').removeClass('password-change__input-wrapper--error');

            $.ajax({
                data: $('.password-change__form').serialize(),
                url: '/?option=com_lasercity&task=saveNewPass',
                dataType: 'json',
                success: function (json) {

                    $('.text-danger').remove();
                    $('.password-change__form .error').removeClass('error');
                    $('.password-change__form .password-change__input-wrapper--error').removeClass('password-change__input-wrapper--error');

                    if (json.success) {
                        $('[name=new_password],[name=new_password1]').val('');
                        $('.status').html(json.success).show();
                    }

                    if (json.error) {
                        $.each(json.error, function (key, value) {
                            $('.password-change__form [name=' + key + ']').addClass('error');
                            $('.password-change__form [name=' + key + ']').closest('label').addClass('password-change__input-wrapper--error');
                        });

                        if (json.error['new_password']) {
                            addError($('.password-change__form [name=new_password]'), json.error['new_password']);
                        }
                        if (json.error['new_password1']) {
                            addError($('.password-change__form [name=new_password1]'), json.error['new_password1']);
                        }

                        $('.password-change__form .error:first').trigger('focus');
                    }
                }
            });

            return false;
        });

        $('.record-salon__button--trash').on('click',function () {
            $('.record-salon__held-warning').show();
            return false;
        });
        $('.record-salon__warning-button--no').on('click',function () {
            $('.record-salon__held-warning').hide();
            return false;
        });

        $('.record-salon__button--cancel').on('click',function () {
            self.location.href = window.location.href;
        });

        $(document).on('click','.record-salon__procedure-button', function () {
            $(this).closest('li').remove();
            totalSeviceRecord();
            totalTimeAllServise();
            /*$.ajax({
                url: '/?option=com_lasercity&task=deleteProcedure&del='+$(this).data('delete'),
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        del.closest('li').remove();
                        if(!$('.record-salon__procedures li').length){
                            del.closest('form').html("<p class='records-salon__warning'>"+json.success+"</p>");
                        }
                    }
                }
            });
            return false;*/
        });

        $('.record-salon__button--completed').on('click',function(){
            $this = $(this);
            $.ajax({
                url: '/?option=com_lasercity&task=completedProcedure&record_id='+$this.data('record_id')+'&affiliate='+$('input[name="affiliate"]').val(),
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $this.hide();
                        $('.record-salon__button--abolished').hide();
                    }
                }
            });
            return false;
        });

        $('.record-salon__button--abolished').on('click',function(){
            $this = $(this);
            $.ajax({
                url: '/?option=com_lasercity&task=abolishedProcedure&record_id='+$this.data('record_id')+'&affiliate='+$('input[name="affiliate"]').val(),
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $this.hide();
                        $('.record-salon__button--completed').hide();
                    }
                }
            });
            return false;
        });

        //обновлнение состоявшихся заявок по времени
        if ($('.counter-by-held-salon').length || $('.counter-by-held-client').length) {
            function loadNewHeld() {
                $.ajax({
                    data: $('input'),
                    url: '/?option=com_lasercity&task=loadNewHeld',
                    dataType: 'json',
                    success: function (json) {
                        if (json.success) {
                            $('.new-applications').text(json.new);
                            $('.agreed-applications').text(json.agreed);
                        }
                    }
                });
                return false;
            }

            window.setInterval(loadNewHeld, 60000);//60 секунд
        }

        $('.record-salon__button--accept').on('click', function () {
            $('.status').hide();
            $('.text-danger').remove();
            $('.recording__form__services .error').removeClass('error');

            $.ajax({
                data: $('.recording__form__services').serialize(),
                url: '/?option=com_lasercity&task=servisesRecordBySalon',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        self.location.href = json.uri;
                    }
                    $('.text-danger').remove();
                    $('.recording__form__services .error').removeClass('error');

                    if (json.error) {
                        $.each(json.error, function (key, value) {
                            $('.recording__form__services [name=' + key + ']').addClass('error');
                        });
                        if (json.error['service_txt']) {
                            addError($('.recording__form__services [name=service_txt]'), json.error['service_txt']);
                            $('.button-addprocedure').prev().show();
                            $('.recording__form__services [name=service_txt]').focus();
                        }
                        if (json.error['date_visit']) {
                            addError($('.recording__form__services .select-date__wish-day'), json.error['date_visit']);
                        }
                        if (json.error['hour1']) {
                            addError($('.recording__form__services .select-date__wish-time'), json.error['hour1']);
                        }

                        $('.recording__form__services .error:first').trigger('focus');
                    }
                }
            });

            return false;
        });



        //запись на услугу
        $('.popup-recording__send-button,.order__authorization-button').on('click', function () {
            $('.status').hide();
            $('.text-danger').remove();
            $('.recording__form__services .error').removeClass('error');
            $('.recording__form__services .order__contact-label--error').removeClass('order__contact-label--error');

            if($('#order_form_by_select').length) {
                url_ajx = 'setRecordServisUser';
            }
            else{
                url_ajx = 'servisesRecord';
            }

            $.ajax({
                data: $('.recording__form__services').serialize(),
                url: '/?option=com_lasercity&task='+url_ajx,
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        if(json.uri){
                            self.location.href = json.uri;
                        }
                        else{
                            $('.status').html(json.success).show();
                            $('.conteiner-recording li').remove();
                            $('[name="koment"],[name="date_visit"],[name="hour1"],[name="hour2"],[name="minut1"],[name="minut2"]').val('');
                            totalSeviceRecord();
                            totalTimeAllServise();
                        }
                    }
                    $('.text-danger').remove();
                    $('.recording__form__services .error').removeClass('error');
                    $('.recording__form__services .order__contact-label--error').removeClass('order__contact-label--error');

                    if (json.error) {
                        $.each(json.error, function (key, value) {
                            $('.recording__form__services [name=' + key + ']').addClass('error');
                            if($('#order_form_by_select').length) {
                                $('.recording__form__services [name=' + key + ']').closest('label').addClass('order__contact-label--error');
                            }
                        });
                        if (json.error['service_txt']) {
                            addError($('.recording__form__services [name=service_txt]'), json.error['service_txt']);
                            $('.button-addprocedure').prev().show();
                            $('.recording__form__services [name=service_txt]').focus();
                        }
                        if (json.error['name']) {
                            addError($('.recording__form__services [name=name]'), json.error['name']);
                        }
                        if (json.error['phone']) {
                            addError($('.recording__form__services [name=phone]'), json.error['phone']);
                        }
                        if (json.error['email']) {
                            addError($('.recording__form__services [name=email]'), json.error['email']);
                        }
                        if (json.error['password']) {
                            addError($('.recording__form__services [name=password]'), json.error['password']);
                        }
                        if (json.error['date_visit']) {
                            addError($('.recording__form__services .select-date__wish-day'), json.error['date_visit']);
                            if($('#order_form_by_select').length){
                                $('.order__services-more').show();
                            }
                        }
                        if (json.error['hour1']) {
                            addError($('.recording__form__services .select-date__wish-time'), json.error['hour1']);
                            if($('#order_form_by_select').length){
                                $('.order__services-more').show();
                            }
                        }
                        if (json.error['ruls']) {
                            addError($('.recording__form__services .accept-rules__rule'), json.error['ruls']);
                        }

                        $('.recording__form__services .error:first').trigger('focus');
                    }
                }
            });

            return false;
        });

        $(document).on('click','.branches-list__button',function () {//console.log($.trim($(this).closest('.salons__main-wrapper').find('.salon-address__street').text()));
            $(".recording__form__services [name=affiliate]").val($(this).data('affiliate'));
            $(".recording__form__services [name=stock]").val($(this).data('stock'));
            $('.recording__form__services .popup-recording__name').text($.trim($(this).closest('.branches-list__branch').find('.branches-list__title').text()));
            $('.recording__form__services .popup-recording__street').text($.trim($(this).closest('.branches-list__branch').find('.salon-address__street').text()));
            if($(this).closest('.branches-list__branch').data('main_image')) {
                $('.recording__form__services .popup-recording__img').attr('src', $(this).closest('.branches-list__branch').data('main_image') + '?' + Math.random());
            }

            $('.text-danger').remove();
            $('.recording__form__services .error').removeClass('error');
            $('.status').hide();
        });

        $(document).on('click','.salons__signup-button',function () {//console.log($.trim($(this).closest('.salons__main-wrapper').find('.salon-address__street').text()));
            $(".recording__form__services [name=affiliate]").val($(this).data('affiliate'));
            $('.recording__form__services .popup-recording__name').text($.trim($(this).closest('.salons__information').find('.salons__title-description .salons__name a').text()));
            $('.recording__form__services .popup-recording__street').text($.trim($(this).closest('.salons__main-wrapper').find('.salon-address__street').text()));
            if($(this).closest('.salons__main-wrapper').find('img.salons__img').attr('src')) {
                $('.recording__form__services .popup-recording__img').attr('src', $(this).closest('.salons__main-wrapper').find('img.salons__img').attr('src') + '?' + Math.random());
            }

            $('.text-danger').remove();
            $('.recording__form__services .error').removeClass('error');
            $('.status').hide();
        });

        $(document).on('click','.salon__contact-sing,.salon-prices__order-button',function () {
            $(".recording__form__services [name=affiliate]").val($(this).data('affiliate'));
            $('.recording__form__services .popup-recording__name').text($.trim($('body h1').text()));
            $('.recording__form__services .popup-recording__street').text($.trim($('.salon-address__street').text()));
            /*if($('img.salon__contact-photo').attr('src')) {
                $('.recording__form__services .popup-recording__img').attr('src', $('img.salon__contact-photo').attr('src') + '?' + Math.random());
            }*/

            $('.text-danger').remove();
            $('.recording__form__services .error').removeClass('error');
            $('.status').hide();

            if($(this).data('price')) {
                sex = ($(this).data('sex') == 'women') ? 'popup-recording__selected-item--women' : 'popup-recording__selected-item--men';
                sex_id = ($(this).data('sex') == 'women') ? 0 : 1;

                $('.conteiner-recording').append('<li class="popup-recording__selected-item ' + sex + '">'
                    + '<input type="hidden" name="sex_id[]" value="' + sex_id + '">'
                    + '<input type="hidden" name="apparat_id[]" value="' + $(this).data('aparat_id') + '">'
                    + '<input type="hidden" name="service_id[]" value="' + $(this).data('service_id') + '">'
                    + '<input type="hidden" name="prices_id[]" value="' + $(this).data('prices_id') + '">'
                    + '<input type="hidden" name="duration[]" value="' + $(this).data('duration') + '">'
                    + '<input type="hidden" name="price[]" class="price-element" value="' + $(this).data('price') + '">'
                    + '<b>' + $(this).data('name') + '</b>'
                    + '<span>' + $(this).data('aparat') + '</span>'
                    + '<button class="popup-recording__selected-del button-cross" type="button" aria-label="delete" title="delete">'
                    + '<span class="visually-hidden"></span>'
                    + '</button>'
                    + '</li>');

                totalSeviceRecord();
                totalTimeAllServise();
            }

        });


        function totalSeviceRecord() {
            price = 0;
            $('.recording__form__services .price-element').each(function () {
                price += parseInt($(this).val().match(/\d+/));
            });
            $('.popup-recording__pay').text(price+' грн');
        }

        function totalSeviceDuration() {
            min = 0;
            $('.recording__form__services .time-element').each(function () {
                min += parseInt($(this).val().match(/\d+/));
            });
            return min;
        }

        function addPrev0(digit) {
            digit = parseInt(digit);
            digit = digit<10 ? '0'+digit : digit;
            return digit;
        }

        $(".auto-time-do input[name=\"hour1\"],.auto-time-do input[name=\"minut1\"]").bind("keyup keypress", function (e) {
            pr_key = (typeof e.charCode === 'undefined' ? e.keyCode : e.charCode);
            if (e.ctrlKey || e.altKey || pr_key < 32)
                return true;

            pr_key = String.fromCharCode(pr_key);
            return /\d/.test(pr_key);
        });

        $('.auto-time-do input[name="hour1"],.auto-time-do input[name="minut1"]').on('keyup',function () {
            totalTimeAllServise();
        });

        function totalTimeAllServise() {
            if (!$('.auto-time-do input[name="hour1"]').is('empty') && $.trim($('.auto-time-do input[name="hour1"]').val())!=00) {

                var theAdd = new Date();

                if($.trim($('input[name="minut1"]').val())==''){
                    $('input[name="minut1"]').val('00');
                }
                else if(parseInt($.trim($('input[name="minut1"]').val()))>60){
                    $('input[name="minut1"]').val('60');
                }
                hours = addPrev0($('input[name="hour1"]').val());
                minut = addPrev0($('input[name="minut1"]').val());

                // Set Hours, minutes, secons and miliseconds
                theAdd.setHours(hours, minut, 00, 000);

                theAdd.setMinutes(theAdd.getMinutes() + totalSeviceDuration());

                //console.log(totalSeviceDuration());

                $('.auto-time-do input[name="hour2"]').attr('readonly',false);
                $('.auto-time-do input[name="hour2"]').val(addPrev0(theAdd.getHours())).attr('readonly',true);
                $('.auto-time-do input[name="minut2"]').attr('readonly',false);
                $('.auto-time-do input[name="minut2"]').val(addPrev0(theAdd.getMinutes())).attr('readonly',true);
            }
            return true;
        }

        totalTimeAllServise();

        $(document).on('click','.popup-recording__selected-del,.order__services-item-button',function () {
            $(this).closest('li').remove();
            totalSeviceRecord();
            totalTimeAllServise();
        });

        $("#input_service").autocomplete({
            'source': function (request, response) {
                if ($.trim($('#input_service').val().length) > 0) {
                    $.ajax({
                        url: '/?option=com_lasercity&task=getServices&service=' + encodeURIComponent(request)+'&affiliate='+$('.recording__form__services input[name=\'affiliate\']').val(),
                        dataType: 'json',
                        success: function (json) {
                            /*if ($('#input_service').data('oldvalue') != request) {
                                $('input[name=\'service\']').val('');
                            }*/
                            response($.map(json, function (item) {
                                return {
                                    apparat: item['apparat'],
                                    label: item['service'],
                                    value: item['value'],
                                    option: item['option'],
                                    price: item['price'],
                                    price_v: item['price_v'],
                                    duration: item['duration'],
                                    persones: item['persones'],
                                    service_id: item['service_id'],
                                    prices_id: item['prices_id']
                                }
                            }));
                        }
                    });
                }
            },
            'select': function (item) {
                //item['label'] -- текст ссылки
                //item['value'] -- значение в data value
//console.log(item);
                sex_id = item['persones'];


                if($('#record_form_by_select').length){
                    liclass = 'record-salon__procedure';
                    man = 'item-salon__procedure--man';
                    woman = 'item-salon__procedure--woman';
                    aparatclass = 'class="visually-hidden"';
                    button = 'record-salon__procedure-button';
                    usluga = '<span class="record-salon__procedure-time">'+item['duration']+'</span>'
                            +'<span class="record-salon__procedure-zona">'+item['label']+'</span>'
                            +'<span class="record-salon__procedure-price">'+item['price_v']+'</span>'
                            +'<span class="record-salon__procedure-apparatus">'+item['option']+'</span>';
                }
                else if($('#order_form_by_select').length){
                    liclass = 'order__services-item';
                    man = 'order__services-item--man';
                    woman = 'order__services-item--woman';
                    aparatclass = 'class="visually-hidden"';
                    button = 'order__services-item-button';
                    usluga = item['label'];
                }
                else{
                    liclass = 'popup-recording__selected-item';
                    man = 'popup-recording__selected-item--men';
                    woman = 'popup-recording__selected-item--women';
                    aparatclass = '';
                    button = 'popup-recording__selected-del';
                    usluga = '<b>'+item['label']+'</b>';
                }

                item['persones'] = (item['persones']==0) ? woman : man;

                $('.conteiner-recording').append('<li class="'+liclass+' '+item['persones']+'">'
                    +'<input type="hidden" name="sex_id[]" value="' + sex_id + '">'
                    +'<input type="hidden" name="apparat_id[]" value="'+item['apparat']+'">'
                    +'<input type="hidden" name="service_id[]" value="'+item['service_id']+'">'
                    +'<input type="hidden" name="prices_id[]" value="'+item['prices_id']+'">'
                    +'<input type="hidden" name="duration[]" class="time-element" value="'+item['duration']+'">'
                    +'<input type="hidden" name="price[]" class="price-element" value="'+item['price']+'">'
                    + usluga
                    +'<span '+aparatclass+'>'+item['option']+'</span>'
                    +'<button class="'+button+' button-cross" type="button" aria-label="delete" title="delete">'
                    +'<span class="visually-hidden"></span>'
                    +'</button>'
                    +'</li>');

                totalSeviceRecord();
                totalTimeAllServise();
                $('#input_service').val('').focus();
                $('#input_service').removeClass('error');
                $('#input_service').next('.text-danger').remove();

                $('#input_service').closest('div').hide();
                //$('#input_service').val(item['label']);
                //$('#input_service').data('oldvalue', item['label']);
                //$('#input_service').removeClass('error');
                //$('#input_service').next('.text-danger').remove();
                //$('input[name=\'service\']').val(item['value']);

                //$('#input_apparat').removeAttr('readonly');
                //$('#input_apparat').val('');
                //$('#input_apparat').data('oldvalue', item['value']);
                //$('input[name=\'apparat\']').val('');
                //$('input[name=\'price\']').val(0);
                //$('.popup-recording__pay').text('0 грн');

                /*$.ajax({
                    url: '/?option=com_lasercity&task=getApparat&service='+item['value']+'&apparat='+item['apparat'],
                    dataType: 'json',
                    success: function (json) {
                        if ($('#input_apparat').data('oldvalue') != item['value']) {
                            $('input[name=\'apparat\']').val('');
                        }
                        $.map(json, function (item) {
                            //$('#input_apparat').removeAttr('readonly');
                            //$('#input_apparat').val(item['value']);
                            //$('#input_apparat').data('oldvalue', item['value']);
                            $('#input_apparat').removeClass('error');
                            $('#input_apparat').next('.text-danger').remove();
                            $('input[name=\'apparat\']').val(item['id']);
                            $('input[name=\'price\']').val(item['price']);
                            $('.popup-recording__pay').text(item['price']);

                            $('#input_apparat').attr('readonly','readonly');
                        });
                    }
                });*/
            }
        });

        $('.type-choices__choice').on('click',function () {
            e = $('.order__contact-inputs');
            e.hide();
            e.eq($(this).index()).show();
            $('.order__authorization-button').text($(this).data('textbtn'));

            $('.type-choices__choice [name="register_for"]').removeAttr('checked');
            $(this).find('[name="register_for"]').attr('checked','checked');
        });

        $('.order__services-button-more').on('click',function () {
            $(this).next().show();
            $(this).hide();
            /*e = $(this).next();
            if(e.css('display')=='none'){
                e.show();
            }
            else{
                e.hide();
            }*/
        });

        $('.show-box-next-textarea').on('click',function () {
            e = $(this).next();
            if(e.css('display')=='none'){
                e.show();
                e.focus();
            }
            else{
                e.hide();
            }
        });

        $('.button-addprocedure').on('click',function () {
            e = $(this).prev();
            if(e.css('display')=='none'){
                e.show();
            }
            else if($('.conteiner-recording li').length){
                e.hide();
            }
            e.find('input').focus();

            return false;
        });

        //галерея
        $('.slider__item img').on('click',function () {
            $('.gallery').show();
        });
        $('.stock-salon__button,.salon__contact-question,.salon__contact-sing,.salon-prices__order-button,.salon__report-owner,.salon__report-error').on('click',function () {
            $('.gallery').hide();
        });

        //задать вопрос
        $('.faq__notfound-button').on('click', function () {
            $('.status').hide();
            $('.text-danger').remove();
            $('.faq-discussion-popup__form .error').removeClass('error');
        });
        $('.faq-discussion-popup__button').on('click', function () {
            $('.status').hide();
            $('.text-danger').remove();
            $('.faq-discussion-popup__form .error').removeClass('error');

            $.ajax({
                data: $('.faq-discussion-popup__form').serialize(),
                url: '/?option=com_lasercity&task=setFAQ',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('.faq-discussion-popup__form input[type=text]').val('');
                        $('.faq-discussion-popup__form textarea').val('');
                        $('.faq-discussion-popup__form .status').html(json.success).show();
                    }
                    $('.text-danger').remove();
                    $('.faq-discussion-popup__form .error').removeClass('error');

                    if (json.error) {
                        $.each(json.error, function (key, value) {
                            $('.faq-discussion-popup__form [name=' + key + ']').addClass('error');
                        });
                        if (json.error['title']) {
                            addError($('.faq-discussion-popup__form [name=title]'), json.error['title']);
                        }
                        if (json.error['description']) {
                            addError($('.faq-discussion-popup__form [name=description]'), json.error['description']);
                        }

                        $('.faq-discussion-popup__form .error:first').trigger('focus');
                    }
                }
            });

            return false;
        });

        $('.main-navigation__search-from,.send-enter-form').submit(function () {
            return false;
        });


        if($('.google-button').length){
            $.ajax({
                url: '/?option=com_lasercity&task=initGoogle',
                dataType: 'json',
                success: function (json) {
                    if (json.link_button) {
                        $(".google-button").attr('href', json.link_button);
                    }
                }
            });
        }
        if($('.facebook-button').length){
            $.ajax({
                url: '/?option=com_lasercity&task=initFacebook',
                dataType: 'json',
                success: function (json) {
                    if (json.link_button) {
                        $(".facebook-button").attr('href', json.link_button);
                    }
                }
            });
        }


        //записываем клики по номеру телефона
        $(document).on('click','.phonebook__popup-show-number',function () {
            $.ajax({
                url: '/?option=com_lasercity&task=setAffiliatePhone&affiliate=' + $(this).data('affiliate'),
                dataType: 'json',
                success: function (json) {

                }
            });
        });

        //записываем клики по лайкам
        $('.comments__feedback-like').on('click',function () {
            review = $(this).find('output');

            if($(this).data('review')!==undefined){
                uri = '/?option=com_lasercity&task=setLikeReview&review=' + $(this).data('review');
            }
            else if($(this).data('answer')!==undefined){
                uri = '/?option=com_lasercity&task=setLikeAnswer&answer=' + $(this).data('answer');
            }

            $.ajax({
                url: uri,
                dataType: 'json',
                success: function (json) {
                    if(json){
                        review.text(parseInt(review.text())+1);
                    }
                }
            });
        });

        $('[data-set-modal-value="addcomment"]').on('click',function () {
            $.ajax({
                url: '/?option=com_lasercity&task=getLastVisitUser',
                dataType: 'json',
                success: function (json) {
                    if(json){
                        $('.salon-addcomment__form #send_date').removeAttr('readonly')
                            .val(json.date)
                            .attr('readonly','readonly');
                    }
                }
            });
        });

        /*
        * по шаговая регистрация
        * */
        //записываем телефон
        $(document).on('click','.register-steps__button-continue:eq(0)',function () {

            step = $(this).closest('li');
            label = step.find('.register-steps__label');

            $.ajax({
                data: $('#register_by_step').serialize(),
                url: '/?option=com_lasercity&task=setRegisterByStep0',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('.text-danger').remove();
                        $('#register_by_step .error').removeClass('error');
                        step.addClass('visually-hidden');
                        step.next('li').removeClass('visually-hidden');
                        label.removeClass('register-steps__label--error');
                    }

                    if (json.error) {
                        if (json.error['phone']) {
                            $('#register_by_step [name=phone]').addClass('error');
                            addError($('#register_by_step [name=phone]'), json.error['phone']);
                            label.addClass('register-steps__label--error');
                        }

                        $('#register_by_step .error:first').trigger('focus');
                    }
                }
            });
        });

        //изменение пароля
        $('.popup-forgot__sent').on('click',function () {

            $('.text-danger').remove();
            $('.popup-forgot__form >*').removeClass('popup-forgot__input-wrapper--error');
            $('.popup-forgot__waiting').text('').hide();

            $.ajax({
                data: $('.popup-forgot__form').serialize(),
                url: '/?option=com_lasercity&task=changePass',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('.text-danger').remove();
                        $('.popup-forgot__form >*').removeClass('popup-forgot__input-wrapper--error');
                        $('.popup-forgot__waiting').text(json.success).css({'display':'block'});
                    }

                    if (json.error) {
                        if (json.error['email_forgot']) {
                            $('.popup-forgot__form .popup-forgot__input-wrapper').addClass('popup-forgot__input-wrapper--error');
                            addError($('.popup-forgot__form [name=email_forgot]'), json.error['email_forgot']);
                        }

                        $('.popup-forgot__form .popup-forgot__input-wrapper--error:first').trigger('focus');
                    }
                }
            });
            return false;
        });

        //выбор организации
        $('input[name=\'organization_sel\']').autocomplete({

            'source': function (request, response) {

                $(this).closest('li').find('.text-danger').remove();
                $(this).closest('li').find('.register-steps__label').removeClass('register-steps__label--error');

                if ($.trim($('input[name=\'organization_sel\']').val().length) > 0) {
                    $.ajax({
                        url: '/?option=com_lasercity&task=getOrganization&organization=' + encodeURIComponent(request),
                        dataType: 'json',
                        success: function (json) {
                            if ($('input[name=\'organization_sel\']').data('oldvalue') != request) {
                                $('input[name=\'organization\']').val('');
                            }
                            response($.map(json, function (item) {
                                return {
                                    label: item['value'],
                                    value: item['id'],
                                    option: item['option']
                                }
                            }));
                        }
                    });
                }
            },
            'select': function (item) {
                //item['label'] -- текст ссылки
                //item['value'] -- значение в data value
                $('input[name=\'organization_sel\']').val(item['label']);
                $('input[name=\'organization_sel\']').data('oldvalue', item['label']);
                $('input[name=\'organization_sel\']').removeClass('error');
                $('input[name=\'organization_sel\']').next('.text-danger').remove();
                $('input[name=\'organization\']').val(item['value']);
            }

        });

        //записываем организацию
        $(document).on('click','.register-steps__button-continue:eq(1)',function () {

            step = $(this).closest('li');
            label = step.find('.register-steps__label');
            $('.text-danger').remove();

            $.ajax({
                data: $('#register_by_step').serialize(),
                url: '/?option=com_lasercity&task=setRegisterByStep1',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('.text-danger').remove();
                        $('#register_by_step .error').removeClass('error');
                        step.addClass('visually-hidden');
                        step.next('li').removeClass('visually-hidden');
                        label.removeClass('register-steps__label--error');
                    }

                    if (json.error) {
                        if (json.error['organization_sel']) {
                            $('#register_by_step [name=organization_sel]').addClass('error');
                            addError($('#register_by_step [name=organization_sel]'), json.error['organization_sel']);
                            label.addClass('register-steps__label--error');
                        }

                        //$('#register_by_step .error:first').trigger('focus');
                    }
                }
            });
        });

        //таймер
        function loadAfterTime() {

            $('#box_load_after_time').show();
            $('#load_after_time').text(30);
            $('.register-steps__confirm-again').attr('disabled','disabled');

            var seconds = $('#load_after_time').text(),int;
            int = setInterval(function() { // запускаем интервал
                if (seconds > 0) {
                    seconds--; // вычитаем 1
                    $('#load_after_time').text(seconds); // выводим получившееся значение в блок
                } else {
                    clearInterval(int); // очищаем интервал, чтобы он не продолжал работу при seconds = 0
                    $('.register-steps__confirm-again').removeAttr('disabled');
                    $('#box_load_after_time').hide();
                }
            }, 1000);
        }

        //записываем телефон организации
        var trai_again = 0;
        $(document).on('click','.register-steps__button-continue:eq(2)',function () {

            step = $(this).closest('li');
            label = step.find('.register-steps__label');
            $('.text-danger').remove();

            $.ajax({
                data: $('#register_by_step').serialize(),
                url: '/?option=com_lasercity&task=setRegisterByStep2',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        if(trai_again == 0) {
                            $('.text-danger').remove();
                            $('#register_by_step .error').removeClass('error');
                            step.addClass('visually-hidden');
                            step.next('li').removeClass('visually-hidden');
                            label.removeClass('register-steps__label--error');
                            loadAfterTime();
                        }
                        trai_again = 0;
                    }

                    if (json.error) {
                        if (json.error['phone']) {
                            $('#register_by_step [name=phone1]').addClass('error');
                            addError($('#register_by_step [name=phone1]'), json.error['phone']);
                            label.addClass('register-steps__label--error');
                        }

                        $('#register_by_step .error:first').trigger('focus');
                    }
                }
            });
        });

        //отправить код повторно
        $('.register-steps__confirm-again').on('click',function () {
            $('.register-steps__button-continue:eq(2)').click();
            trai_again = 1;

            loadAfterTime();

            return false;
        });

        //ввод четырьох значного кода
        $(document).on('click','.register-steps__button-continue:eq(3)',function () {

            step = $(this).closest('li');
            label = step.find('.register-steps__label');
            $('.text-danger').remove();

            $.ajax({
                data: $('#register_by_step').serialize(),
                url: '/?option=com_lasercity&task=setRegisterByStep3',
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('.text-danger').remove();
                        $('#register_by_step .error').removeClass('error');
                        $('#register_by_step .status').html(json.success).show();
                        /*step.addClass('visually-hidden');
                        step.next('li').removeClass('visually-hidden');
                        label.removeClass('register-steps__label--error');*/
                    }

                    if (json.error) {
                        if (json.error['code']) {
                            $('#register_by_step [name=code]').addClass('error');
                            addError($('#register_by_step [name=code]'), json.error['code']);
                            label.addClass('register-steps__label--error');
                        }

                        $('#register_by_step .error:first').trigger('focus');
                    }
                }
            });
        });

        //возвращаемся на шаг назад
        $(document).on('click','.register-steps__button-back',function () {
            step = $(this).closest('li');
            step.addClass('visually-hidden');
            step.prev('li').removeClass('visually-hidden');
        });

        $('.delete-record,.record-salon__warning-button--yes').on('click',function(){
            delit = $(this).closest('li');
            $.ajax({
                url: '/?option=com_lasercity&task=deleteRecords&record='+$(this).data('delete'),
                dataType: 'json',
                success: function (json) {
                    if(json.success){
                        delit.remove();
                        $('.record-client__warning').text(json.success);
                        $('.in-client-services>*:not(".record-client__warning")').remove();
                    }
                }
            });
            return false;
        });

        /*$("#input_apparat").autocomplete({
            'source': function (request, response) {
                if ($.trim($('#input_apparat').val().length) > 0) {
                    $.ajax({
                        url: '/?option=com_lasercity&task=getApparats&apparat=' + encodeURIComponent(request)+'&service='+$('input[name=\'service\']').val(),
                        dataType: 'json',
                        success: function (json) {
                            if ($('#input_apparat').data('oldvalue') != request) {
                                $('input[name=\'apparat\']').val('');
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
            'select': function (item) {
                //item['label'] -- текст ссылки
                //item['value'] -- значение в data value
                $('#input_apparat').val(item['label']);
                $('#input_apparat').data('oldvalue', item['label']);
                $('#input_apparat').removeClass('error');
                $('#input_apparat').next('.text-danger').remove();
                $('input[name=\'apparat\']').val(item['value']);
            }
        });*/


    });
});