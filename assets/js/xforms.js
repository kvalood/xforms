var field__item = '.field__item',
    error_class = 'error_input',
    captcha_image = '.captcha_image';

function send_widget_form(i) {

    var form = $(i).closest('form');

    form.append('<div class="xforms_loader"></div>');

    $.ajax({
        type: "POST",
        url: form.attr('action'),
        data: form.serialize(),
        success: function (data) {
            var notify = JSON.parse(data);

            // Ошибки формы
            if (notify.errors) {
                var array = [];
                for (var index in notify.errors) {
                    var attr = notify.errors[index],
                        prnt = $('[name="' + index + '"]').closest(field__item);

                    prnt.addClass(error_class).find('.error').remove();
                    prnt.append('<p class="error">' + attr + '</p>');
                    array.push(index);
                }
                $('html, body').stop().animate({scrollTop: $('[name="' + array[0] + '"]').parent().offset().top}, 350);

                // Обновляем капчу, в случае ошибки.
                if (notify.captcha_image) {
                    form.find(captcha_image).html(notify.captcha_image).closest(field__item).find('input').val('');
                }
            }

            // Успешная отпрвка формы
            if (notify.success) {
                $('html, body').stop().animate({scrollTop: form.offset().top}, 350);
                form.remove();
                notie.alert(1, notify.success);
            }

            form.find('.xforms_loader').remove();
        }
    });

    return false;
}

// Удаляем информацию об ошибке
$(document).on('click', '.' + error_class + ' input, .' + error_class + ' textarea', function () {
    $(this).closest(field__item).removeClass(error_class).find('.error').remove();
});

// Автопрокрутка textarea
autosize(document.querySelectorAll('.message_text'));