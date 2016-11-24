var field__item = '.field__item',
    error_class = 'error__field',
    captcha_image = '.captcha_image';

function send_widget_form(i) {

    var form = $(i).closest('form');

    if (typeof xforms_loader == 'function') {
        xforms_loader(form);
    } else {
        form.append('<div class="xforms_loader"></div>');
    }

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
                    var error = notify.errors[index],
                        field = $('[name="' + index + '[]"').length ? $('[name="' + index + '[]"').closest(field__item) : $('[name="' + index + '"').closest(field__item);

                    field.addClass(error_class).find('.error').remove();
                    field.append('<p class="error">' + error + '</p>');
                    array.push(index);
                }

                // Обновляем капчу, в случае ошибки.
                if (notify.captcha_image) {
                    form.find(captcha_image).html(notify.captcha_image).closest(field__item).find('input').val('');
                }

                // Вызываем пользовательскую функцию
                if (typeof xforms_errors == 'function') {
                    xforms_errors(form, array);
                } else {
                    $('html, body').stop().animate({scrollTop: $('[name^="' + array[0] + '"]').parent().offset().top}, 350);
                    form.find('.xforms_loader').remove();
                }
            }

            // Успешная отпрвка формы
            if (notify.success) {
                if (typeof xforms.success == 'function') {
                    xforms.success(form, notify.success);
                } else {
                    form.find('input[type="submit"]').remove().parent().html(notify.success);
                    form.find('.xforms_loader').remove();
                }
            }
        }
    });

    return false;
}

// Удаляем информацию об ошибке
$(document).on('click', '.' + error_class + ' input, .' + error_class + ' textarea, .' + error_class + ' select', function () {
    $(this).closest(field__item).removeClass(error_class).find('.error').remove();
});