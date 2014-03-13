$(document).ready(function(){
    var approvedSubs = $('.subscription .subs-approved');

    $('.user-links .login').click(function(){
        $.ajax(
            {
                url: '/login'
            }
        ).success(function(data) {
                refreshLoginForm(data)
            });
        return false;
    })

    function refreshLoginForm(data)
    {
        $('.user-links .forms .login-form').append(data);
        var loginDialog = $('.user-links .forms .login-form form').dialog({
            title: "Авторизироваться",
            close: function(){
                $('.user-links .forms .login-form form').remove();
            }
        });
        $('.login_submit').click(function(){
             return loginSubmit(this)
        });
        function loginSubmit(target)
        {
            var parentForm = $(target).parents('form')[0];
            var action = $(parentForm).attr('action');
            var method = $(parentForm).attr('method');
            $.ajax({
                url: action,
                type: method,
                data:  $(parentForm).serialize()
            }).success(function(data){
                    if ($(data).find('.login-form').length == 0 && $(data).find('.error').length == 0) {
                        $(loginDialog).dialog('close');
                        loginAuthorized(data);
                    } else {
                        $(loginDialog).dialog('close');
                        $('.user-links .forms .login-form form .content').empty();
                        $(loginDialog).dialog('close');
                        refreshLoginForm(data);
                    }
                });

            return false;
        }

        function loginAuthorized(data)
        {
            $('.user-links').empty();
            $('.user-links').append($(data).find('.user-links'));
            showMessage('Вы успешно авторизировались', 'notice', 3000);
        }
    };

    $('.user-links .register').click(function(){
        $.ajax(
            {
                url: '/register'
            }
        ).success(function(data){
                refreshRegisterForm(data);
            });
        return false;
    });

    function refreshRegisterForm (data) {
        $('.user-links .forms .register-form').append(data);
        var registerDialog = $('.user-links .forms .register-form form').dialog({
            title: "Зарегистрироваться",
            close: function(){
                $('.user-links .forms .register-form form').remove();
            }
        });
        $('.registration_submit').click(function(){
            return registerSubmit(this);
        });

        function registerSubmit(target)
        {
            var parentForm = $(target).parents('form')[0];
            var action = $(parentForm).attr('action');
            var method = $(parentForm).attr('method');
            var email = $(parentForm).find('#fos_user_registration_form_email').val();

            $.ajax({
                url: action,
                type: method,
                data:  $(parentForm).serialize()
            }).success(function(data){
                    if ($(data).find('.fos_user_registration_register').length == 0 && $(data).find('.error').length == 0) {
                        $(registerDialog).dialog('close');
                        registerAuthorized(email);
                    } else {
                        $('.user-links .forms .register- form .content').empty();
                        $(registerDialog).dialog('close');
                        refreshRegisterForm(data);
                    }
                });

            return false;
        }

        function registerAuthorized(email)
        {
            showMessage('Спасибо за регистрацию! На почту отправлено письмо ' + email + ' со ссылкой для активации вашей учётной записи.', 'notice', 9000);
        }
    };

    function refreshDigestForm(formsContainer, data, digestDialog) {
        $(formsContainer).append($(data));
        var form = $(formsContainer).find('form');
        var action = $(form).attr('action');
        var method = $(form).attr('method');
        digestDialog = $(formsContainer).find('.digest-form').dialog({
            title: "Подписаться",
            close: function () {
                $(formsContainer).find('.digest-form').empty();
            }
        });

        if ($('#filter_form').length > 0) {
            setFilters($('#filter_form'), form);
        }


        function setFilters(filterForm, digest_form)
        {
            // Address
            if ($('#form_address').val().length > 0) {
                var val = $('#form_address').val();
                if (val.indexOf('м.') != 0) {
                    val = val.slice(0, val.lastIndexOf(' '));
                    console.log(val);
                    $('#create_digest_type_address').attr('value', val);
                } else {
                    val = val.substr(2);
                    if (val.indexOf(' ') == 0) {
                        val = val.substr(1);
                    }
                    var id = 0;
                    $('#create_digest_type_metro option').each(function(){
                        if ($(this).text().length == val.length && $(this).text().indexOf(val) == 0) {
                            document.getElementById('create_digest_type_metro').selectedIndex = id;
                            $(this).attr('selected', 1);
                        }
                        id++;
                    });
                }
            }

            // Date
            if ($('#form_date_from').val().length > 0) {
                var date = $('#form_date_from').val();
                var month = date.substr(0,2);
                var day = date.substr(3,2);
                var year = date.substr(6, 4);
                $('#create_digest_type_dateFrom_day').attr('value', day);
                $('#create_digest_type_dateFrom_month').attr('value', month);
                $('#create_digest_type_dateFrom_year').attr('value', year);
            }
            if ($('#form_date_to').val().length > 0) {
                var date = $('#form_date_to').val();
                var month = date.substr(0,2);
                var day = date.substr(3,2);
                var year = date.substr(6, 4);
                $('#create_digest_type_dateTo_day').attr('value', day);
                $('#create_digest_type_dateTo_month').attr('value', month);
                $('#create_digest_type_dateTo_year').attr('value', year);
            }

            // Time
            if ($('#form_time_from').val().length > 0) {
                var time = $('#form_time_from').val();
                var hours = time.substr(0,2);
                var minutes = time.substr(3,2);
                $('#create_digest_type_startTime_hour').attr('value', hours);
                $('#create_digest_type_startTime_minute').attr('value', minutes);
            }
            if ($('#form_time_to').val().length > 0) {
                var time = $('#form_time_to').val();
                var hours = time.substr(0,2);
                var minutes = time.substr(3,2);
                $('#create_digest_type_endTime_hour').attr('value', hours);
                $('#create_digest_type_endTime_minute').attr('value', minutes);
            }

            // Event types
            var values = [];
            $('.event_type_filter .label-active').each(function(){
                var checkbox = $(this).parent().find('input');
                values[values.length] = $(checkbox).attr('value');
            });
            $('#create_digest_type_eventType option').each(function(){
                var val = $(this).attr('value');
                var enable = false;
                for(var i = 0; i < values.length; ++i) {
                    if (val == values[i]) {
                        enable = true;
                        break;
                    }
                }
                if(enable) {
                    $(this).select();
                    $(this).attr('selected', 1);
                }
            })

            // Dance type
            $('.dance_type_filter .label-active').each(function(){
                var checkbox = $(this).parent().find('input');
                values[values.length] = $(checkbox).attr('value');
            });
            $('#create_digest_type_danceType option').each(function(){
                var val = $(this).attr('value');
                var enable = false;
                for(var i = 0; i < values.length; ++i) {
                    if (val == values[i]) {
                        enable = true;
                        break;
                    }
                }
                if(enable) {
                    $(this).select();
                    $(this).attr('selected', 1);
                }
            })

        }

        $('.digest_submit').click(function (){
            $.ajax({
                url: action,
                type: method,
                data: $(digestDialog).find('form').serialize()
            }).success(function (data) {
                    if ($(data).find('form').length > 0) {
                        $(digestDialog).dialog('close');
                        refreshDigestForm(formsContainer, data, digestDialog);
                    } else {
                        $(digestDialog).dialog('close');
                        $(approvedSubs).fadeIn('slow');
                        window.setTimeout(function(){$(approvedSubs).hide('slow')}, 2000);
                    }
                });

            return false;
        })
    }

    $('.subscribe-btn').click(function(){
        var action = $(this).attr('href');
        var formsContainer = $(this).parent().find('.digest_forms');
        var digestDialog = null;

        $.ajax({
            url: action,
            method: "GET"
        }).success(function(data){
                refreshDigestForm(formsContainer, data, digestDialog);
            });
        return false;
    })

    $('#form_children, #form_abonement').change(function(){
        var label = $(this).parent();
        var act = 0;

        if ($(label).hasClass('active')) {
            act = 1;
        }
        if(act != 1) {
            $(label).addClass('active');
        } else {
            $(label).removeClass('active');
        }
    });

    $('.message-default .close').click(function(){
        $(this).parent().remove();
    })
});

$(window).load(function(){
    initializePageHeight();
});

function initializePageHeight() {
    var winHeight = $(window).height();
    var topHeight = $('body .top_bg').height();
    var footerHeight = $('body #footer').height();
    var upBtnHeight = $('#upButton').height();
    if (winHeight > topHeight + footerHeight + upBtnHeight) {
        $('body .top_bg').height(winHeight - footerHeight - upBtnHeight);
    }
}
function getUnique(){
    if (typeof getUnique.messageId == 'undefined') {
        getUnique.messageId = 0;
    } else {
        ++getUnique.messageId;
    }

    return getUnique.messageId;
}

function showMessage(messageText, type, previewDuration) {
    if (!previewDuration) {
        previewDuration = 1000;
    }
    if (!type) {
        type = 'notice';
    }

    var messageId = getUnique();

    $('#messages').append('<div id="message-' + messageId + '" class="message message-' + type + '"><div class="close"></div>' +
        '<div class="message-text">' + messageText + '</div>' +
        '</div>');
    $('#messages #message-' + messageId + ' .close').click(function(){
        closeMessage(messageId);
    });
    window.setTimeout(function(){
        closeMessage(messageId);
    }, previewDuration);
}

function closeMessage(messageId) {
    $('#messages #message-' + messageId).remove();
}