(function($) {

    var d = document, w = window;

    $.fn.MyDtime = function (options) {

        options = $.extend({times: false, button: true, step: 1, select: true, setDate: '', language: 'ru', readonly: true}, options);

        var make = function () {
            var el = $(this);
            el.addClass('addDtime');
            var date = new Date();
            var my_times = options.times;
            var my_button = options.button;
            var my_step = options.step;
            var my_select = options.select == false ? 'disabled' : '';
            var my_setDate = options.setDate != '' ? options.setDate : '';
            var my_language = options.language;
            var my_readonly = options.readonly;

            function inWindowLeft(s) {
                if ($(w).width() - $(s).offset().left < $(s).width()) {
                    return $(s).position().left - (parseInt($(s).width() - ($(w).width() - $(s).offset().left)) + el.height());
                }
                else {
                    return false;
                }
            }

			/*function inWindowTop(s){
			 if($(w).height()-$(s).offset().top<$(s).height()){return el.position().top-$(s).outerHeight();}
			 else if($(w).height()-$(s).offset().top>$(s).height()){return false;}
			 else{return false;}
			 }*/

            if (el.is('input')) {
                if(my_readonly==true) {
                    el.attr('readonly', true);
                }
            }
            el.after('<div class="dtime"></div>');
            el.next().on('selectstart', function () {
                return false;
            });

            function init(el, date, my_times, my_button, my_step, my_select, my_setDate, my_language) {
                if(my_language=='ru') {
                    var mas_m = {
                        0: 'Январь',
                        1: 'Февраль',
                        2: 'Март',
                        3: 'Апрель',
                        4: 'Май',
                        5: 'Июнь',
                        6: 'Июль',
                        7: 'Август',
                        8: 'Сентябрь',
                        9: 'Октябрь',
                        10: 'Ноябрь',
                        11: 'Декабрь'
                    };
                    var mas_time = {
                        'h': 'Часы',
                        'i': 'Минуты'
                    };
                }
                else if(my_language=='ua') {
                    var mas_m = {
                        0: 'Січень',
                        1: 'Лютий',
                        2: 'Березень',
                        3: 'Квітень',
                        4: 'Травень',
                        5: 'Червень',
                        6: 'Липень',
                        7: 'Серпень',
                        8: 'Вересень',
                        9: 'Жовтень',
                        10: 'Листопад',
                        11: 'Грудень'
                    };
                    var mas_time = {
                        'h': 'Години',
                        'i': 'Хвилини'
                    };
                }

                var pr_dt, pr_d, pr_t, year, month, day, dayCount, hour, minute, itm, html;

                if ($.trim(el.val()) == "" && my_setDate == '' || $.trim(el.val()) == "0000-00-00 00:00:00") {
                    year = date.getFullYear(), month = date.getMonth(), day = date.getDate();
                    if (my_times == true) {
                        hour = date.getHours();
                        minute = date.getMinutes();
                    }
                }
                else {
                    if (my_setDate != '') {
                        pr_dt = $.trim(my_setDate).split(' ');
                    }
                    else {
                        pr_dt = $.trim(el.val()).split(' ');
                    }
                    pr_d = pr_dt[0].split('-');

                    year = pr_d[0], month = parseInt(pr_d[1].replace(/^0+/, '')) - 1, day = parseInt(pr_d[2].replace(/^0+/, ''));
                    if (my_times == true) {
                        pr_t = pr_dt[1].split(':');
                        hour = parseInt(pr_t[0].replace(/^0+/, ''));
                        minute = parseInt(pr_t[1].replace(/^0+/, ''));
                    }
                }
                dayCount = new Date(year, month + 1, 0).getDate(),
                    mas_d = {1: 'Пн', 2: 'Вт', 3: 'Ср', 4: 'Чт', 5: 'Пт', 6: 'Сб', 0: 'Вс'},
                    n_day = new Date(year, month, 1).getDay() - 1;
                if (n_day == -1) {
                    n_day = 6;
                }

                html = "<div class='d_con_ym'>";
                html += "<div class='g_back'>◄</div>";
                html += "<div class='d_month-not-sel'>"+mas_m[month]+"</div>";
                html += "<div class='d_year-not-sel'>"+year+"</div>";
                html += "<select class='d_year' " + my_select + ">";
                for (var i = date.getFullYear()-5; i < date.getFullYear() + 25; i++) {
                    if (year == i) {
                        html += "<option class='o_year' value='" + i + "' selected>" + i + "</option>";
                    }
                    else {
                        html += "<option class='o_year' value='" + i + "'>" + i + "</option>";
                    }
                }
                html += "</select>";
                html += "<select class='d_month' " + my_select + ">";
                for (var i in mas_m) {
                    if (mas_m[i] == mas_m[month]) {
                        html += "<option class='o_month' value='" + i + "' selected>" + mas_m[i] + "</option>";
                    }
                    else {
                        html += "<option class='o_month' value='" + i + "'>" + mas_m[i] + "</option>";
                    }
                }
                html += "</select>";
                html += "<div class='g_next'>►</div>";
                html += "</div>";

                html += "<table class='cont_ch'>";
                html += "<tr>";
                for (var i in mas_d) {
                    if (i > 0) {
                        html += "<th>" + mas_d[i] + "</th>";
                    }
                    if (i == 6) {
                        html += "<th>" + mas_d[0] + "</th>";
                    }
                }
                html += "</tr>";
                for (var i = 1; i <= (dayCount + n_day); i++) {
                    if (i == 1) {
                        html += "<tr>";
                    }
                    if (i > n_day) {
                        if (year == date.getFullYear() && month == date.getMonth() && (i - n_day) == date.getDate()) {
                            html += "<td><div class='chd n_chd'>" + (i - n_day) + "</div></td>";
                        }
                        else if ((i - n_day) == day && i != date.getDate()) {
                            html += "<td><div class='chd s_chd'>" + (i - n_day) + "</div></td>";
                        }
                        else {
                            html += "<td><div class='chd'>" + (i - n_day) + "</div></td>";
                        }
                    }
                    else {
                        html += "<td></td>";
                    }
                    if (i % 7 == 0) {
                        html += "</tr><tr>";
                    }
                }
                html += "</tr>";
                html += "</table>";

                if (my_times == true) {
                    html += "<div class='dt_time'>";
                    html += "<div class='cn_time'>"+mas_time['h']+"<br>";
                    html += "<select class='d_hour'>";
                    for (var i = 0; i <= 23; i++) {
                        itm = (i < 10 ? '0' : '') + i;
                        if (i == hour) {
                            html += "<option class='o_hour' value='" + itm + "' selected>" + itm + "</option>";
                        }
                        else {
                            html += "<option class='o_hour' value='" + itm + "'>" + itm + "</option>";
                        }
                    }
                    html += "</select>";
                    html += "</div>";
                    html += "<div class='rz_time'>:</div>";
                    html += "<div class='cnn_time'>"+mas_time['i']+"<br>";
                    html += "<select class='d_minute'>";
                    for (var i = 0; i < 60; i += my_step) {
                        itm = (i < 10 ? '0' : '') + i;
                        if (i == minute) {
                            html += "<option class='o_minute' value='" + itm + "' selected>" + itm + "</option>";
                        }
                        else {
                            html += "<option class='o_minute' value='" + itm + "'>" + itm + "</option>";
                        }
                    }
                    html += "</select>";
                    html += "</div><br>";
                    html += "</div>";
                }

                if (my_button == true) {
                    html += "<div class='dt_button'>";
                    //html += "<div class='t_button'>Сегодня</div>";
                    html += "<div class='c_button'>✖</div>";
                    html += "</div>";
                }

                return html;
            }

            el.next().html(init(el, date, my_times, my_button, my_step, my_select, my_setDate, my_language));

            function init_2(el, date, my_times, my_button, my_step) {
                if (my_times == true) {
                    var d_hour = "", d_minute = "";
                    el.next().find(".d_hour").on('change', function () {
                        d_hour = " " + $(this).val() + ":" + el.next().find('.d_minute').val();
                        if ($.trim(el.val()) == "") {
                            el.val(el.next().find(".d_year").val() + "-" + ((parseInt(el.next().find(".d_month").val()) + 1) < 10 ? '0' : '') + (parseInt(el.next().find(".d_month").val()) + 1) + "-" + (el.next().find(".n_chd").text() < 10 ? '0' : '') + el.next().find(".n_chd").text() + d_hour);
                        }
                        else {
                            el.val(el.val().replace(/[0-9]{2}:/i, $(this).val() + ":"));
                        }
                    });
                    el.next().find(".d_minute").on('change', function () {
                        d_minute = " " + el.next().find('.d_hour').val() + ":" + $(this).val();
                        if ($.trim(el.val()) == "") {
                            el.val(el.next().find(".d_year").val() + "-" + ((parseInt(el.next().find(".d_month").val()) + 1) < 10 ? '0' : '') + (parseInt(el.next().find(".d_month").val()) + 1) + "-" + (el.next().find(".n_chd").text() < 10 ? '0' : '') + el.next().find(".n_chd").text() + d_minute);
                        }
                        else {
                            el.val(el.val().replace(/:[0-9]{2}/g, ":" + $(this).val()));
                        }
                    });
                }

                if (my_button == true) {
                    var t_button = "", t_step;
                    el.next().find(".c_button").on('click', function () {
                        el.next().hide();
                        return false;
                    });

                    el.next().find(".t_button").on('click', function () {
                        date = new Date();
                        if (my_step == 15) {
                            t_step = 0;
                        } else {
                            t_step = date.getMinutes();
                        }
                        if (my_times == true) {
                            t_button = " " + (date.getHours() < 10 ? '0' : '') + (date.getHours()) + ":" + (t_step < 10 ? '0' : '') + (t_step);
                        }
                        el.val(date.getFullYear() + "-" + ((parseInt(date.getMonth()) + 1) < 10 ? '0' : '') + (parseInt(date.getMonth()) + 1) + "-" + (date.getDate() < 10 ? '0' : '') + date.getDate() + t_button);
                        el.next().html(init(el, date, my_times, my_button, my_step, my_select, my_setDate, my_language));
                        init_2(el, date, my_times, my_button, my_step);
                        return false;
                    });
                }
                var chd = "";
                el.next().find(".chd").on('click', function () {
                    if (my_times == true) {
                        chd = " " + el.next().find('.d_hour').val() + ":" + el.next().find('.d_minute').val();
                    }
                    el.next().find('.cont_ch').find('.s_chd').removeClass('s_chd');
                    $(this).addClass('s_chd');
                    if (el.is('input')) {
                        el.val(el.next().find(".d_year").val() + "-" + ((parseInt(el.next().find(".d_month").val()) + 1) < 10 ? '0' : '') + (parseInt(el.next().find(".d_month").val()) + 1) + "-" + ($(this).text() < 10 ? '0' : '') + $(this).text() + chd);
                    }
                });

                var d_year = "", d_month = "", d_chisl = "";
                el.next().find(".d_year").on('change', function () {
                    d_chisl = el.next().find(".n_chd").text();
                    if (d_chisl == '') {
                        d_chisl = 1;
                    }
                    if (my_times == true) {
                        d_year = " " + el.next().find('.d_hour').val() + ":" + el.next().find('.d_minute').val();
                    }
                    if ($.trim(el.val()) == "") {
                        el.val($(this).val() + "-" + ((parseInt(el.next().find(".d_month").val()) + 1) < 10 ? '0' : '') + (parseInt(el.next().find(".d_month").val()) + 1) + "-" + (d_chisl < 10 ? '0' : '') + d_chisl + d_year);
                    }
                    else {
                        el.val(el.val().replace(/[0-9]{4}-/g, $(this).val() + "-"));
                    }

                    el.next().html(init(el, date, my_times, my_button, my_step, my_select, my_setDate, my_language));
                    init_2(el, date, my_times, my_button, my_step);
                    return false;
                });

                el.next().find(".d_month").on('change', function () {
                    d_chisl = el.next().find(".n_chd").text();
                    if (d_chisl == '') {
                        d_chisl = 1;
                    }
                    if (my_times == true) {
                        d_month = " " + el.next().find('.d_hour').val() + ":" + el.next().find('.d_minute').val();
                    }
                    if ($.trim(el.val()) == "") {
                        el.val(el.next().find('.d_year').val() + "-" + ((parseInt($(this).val()) + 1) < 10 ? '0' : '') + (parseInt($(this).val()) + 1) + "-" + (d_chisl < 10 ? '0' : '') + d_chisl + d_month);
                    }
                    else {
                        el.val(el.val().replace(/-[0-9]{2}-/g, "-" + ((parseInt($(this).val()) + 1) < 10 ? '0' : '') + (parseInt($(this).val()) + 1) + "-"));
                    }

                    el.next().html(init(el, date, my_times, my_button, my_step, my_select, my_setDate, my_language));
                    init_2(el, date, my_times, my_button, my_step);
                    return false;
                });

                var g_m, g_g, g_next = "", g_back = "";
                el.next().find(".g_next").on('click', function () {
                    g_m = parseInt(el.next().find(".d_month").val()) + 2;
                    g_g = parseInt(el.next().find(".d_year").val());

                    if (g_g == el.next().find(".o_year:last").val() && g_m > 12) {
                        g_g = el.next().find(".o_year:first").val();
                        g_m = 1;
                        el.next().find(".o_year:contains('" + g_g + "')").first().attr("selected", true);
                    }
                    else if (g_m > 12) {
                        g_m = 1;
                        el.next().find(".o_year:contains('" + (g_g + 1) + "')").first().attr("selected", true);
                    }

                    if (my_times == true) {
                        g_next = " " + el.next().find('.d_hour').val() + ":" + el.next().find('.d_minute').val();
                    }
                    if ($.trim(el.val()) == "") {
                        el.val(g_g + "-" + (g_m < 10 ? '0' : '') + g_m + "-" + (el.next().find(".n_chd").text() < 10 ? '0' : '') + el.next().find(".n_chd").text() + g_next);
                    }
                    else {
                        el.val(el.val().replace(/-[0-9]{2}-/g, "-" + (g_m < 10 ? '0' : '') + g_m + "-"));
                    }

                    el.next().find(".d_year").change();
                    return false;
                });

                el.next().find(".g_back").on('click', function () {
                    g_m = parseInt(el.next().find(".d_month").val()) + 1 - 1;
                    g_g = parseInt(el.next().find(".d_year").val());

                    if (g_g == el.next().find(".o_year:first").val() && g_m < 1) {
                        g_g = el.next().find(".o_year:last").val();
                        g_m = 12;
                        el.next().find(".o_year:contains('" + g_g + "')").first().attr("selected", true);
                    }
                    else if (g_m < 1) {
                        g_m = 12;
                        el.next().find(".o_year:contains('" + (g_g - 1) + "')").first().attr("selected", true);
                    }

                    if (my_times == true) {
                        g_back = " " + el.next().find('.d_hour').val() + ":" + el.next().find('.d_minute').val();
                    }
                    if ($.trim(el.val()) == "") {
                        el.val(g_g + "-" + (g_m < 10 ? '0' : '') + g_m + "-" + (el.next().find(".n_chd").text() < 10 ? '0' : '') + el.next().find(".n_chd").text() + g_back);
                    }
                    else {
                        el.val(el.val().replace(/-[0-9]{2}-/g, "-" + (g_m < 10 ? '0' : '') + g_m + "-"));
                    }

                    el.next().find(".d_year").change();
                    return false;
                });
            }

            init_2(el, date, my_times, my_button, my_step);

            /*$(d).on('click', function (e) {
                if ($(e.target).closest(".addDtime").length || $(e.target).closest(".dtime").length ||
                    $(e.target).closest(".g_back").length || $(e.target).closest(".g_next").length ||
                    $(e.target).closest(".t_button").length)
                    return;
                if (el.is('input')) {
                    $('.dtime').hide();
                }

                e.stopPropagation();
                return false;
            });*/

            el.on('focus click', function () {
                if ($(this).is('input')) {
                    $('.dtime').hide();
                }
                el.next().show().css({left: el.position().left, top: el.position().top + el.height()});
                if (inWindowLeft(".dtime") != false) {
                    $(".dtime").css({left: inWindowLeft(".dtime")});
                }
                //if(inWindowTop(".dtime")!=false){$(".dtime").css({top:inWindowTop(".dtime")});}
            }).on('keyup', function (e) {
                switch (e.keyCode) {
                    case 27:
                        if ($(this).is('input')) {
                            $('.dtime').hide();
                        }
                        break;
                }
            });

            if (el.is('div')) {
                el.next().css({'position': 'relative', 'float': 'left'}).show();
            }

        };

        return this.each(make);

    };
})(jQuery);