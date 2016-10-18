var field__item = '.field__item',
    error_class = 'error__field',
    captcha_image = '.captcha_image';

function send_widget_form(i) {

    var form = $(i).closest('form');

    form.append('<div class="xforms_loader"></div>');

    $.ajax({
        type: "POST",
        url: form.attr('action'),
        data: form.serialize(), // был глюк с %5b%5d
        success: function (data) {
            var notify = JSON.parse(data);

            // Ошибки формы
            if (notify.errors) {
                var array = [];
                for (var index in notify.errors) {
                    var error = notify.errors[index],
                        field = $('[name^="' + index + '"').closest(field__item);

                    field.addClass(error_class).find('.error').remove();
                    field.append('<p class="error">' + error + '</p>');
                    array.push(index);
                }
                $('html, body').stop().animate({scrollTop: $('[name^="' + array[0] + '"]').parent().offset().top}, 350);

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
$(document).on('click', '.' + error_class + ' input, .' + error_class + ' textarea, .' + error_class + ' select', function () {
    $(this).closest(field__item).removeClass(error_class).find('.error').remove();
});

// Автопрокрутка textarea
autosize(document.querySelectorAll('.message_text'));

// Загрузка файлов
$(function () {

    // Симуляция клика по file input для показа диалога выбора файла
    $(document).on('click', '.drop_here a', function(){
        $(this).parent().find('input').click();
    });

    // Инициализация "jQuery File Upload plugin"
    $('.field__item.file input[type="file"]').fileupload({
        dataType: 'json',
        dropZone: $('.drop_here')
    }).on('fileuploadadd', function (e, data) {

        var tpl = $('<li class="working"><i></i><div></div><span>×</span></li>'),
            submit_button = $(this).closest('form').find('[type="submit"]');

        data.submit_button = submit_button; // Кнопка отправки формы

        // Добавим имя файла и его размер
        tpl.find('div').text(data.files[0].name)
            .append('<div>' + formatFileSize(data.files[0].size) + '</div>');

        // Add the HTML to the UL element
        data.context = tpl.appendTo($(this).closest('.field__item').find('.file_list'));

        // Listen for clicks on the cancel icon
        tpl.find('span').click(function(){

            data.submit_button.prop("disabled", false);  // Заморозим кнопку отправки формы

            if(tpl.hasClass('working')){
                jqXHR.abort();
            }

            tpl.fadeOut(function(){
                tpl.remove();
            });
        });

        // Automatically upload the file once it is added to the queue
        var jqXHR = data.submit();

    }).on('fileuploadprogress', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        data.context.find('i').css('width', progress + '%');

        data.submit_button.prop("disabled", true); // Заморозим кнопку отправки формы

        if(progress == 100){
            data.context.removeClass('working').addClass('uploaded');
            data.submit_button.prop("disabled", false);  // Разморозим кнопку отправки формы
        }
    }).on('fileuploaddone', function(e, data){
        if(data.result.error) {
            data.context.removeClass('uploaded').addClass('error__upload');
            data.context.append('<div class="error__upload_message">' + data.result.error + '</div>');
            data.context.find('i').remove();
            setTimeout(function(){data.context.remove();},5000);
        } else {
            var field_id = $(this).closest('.field__item').attr('data-field-id');
            data.context.append('<input type="hidden" value="' + data.result.url + '" name="f' + field_id + '[url][]"/>');
            data.context.append('<input type="hidden" value="' + data.result.name + '" name="f' + field_id + '[name][]"/>');
        }

    }).on('fileuploadfail', function(e, data){
        data.context.removeClass('uploaded').addClass('error__upload');
        data.context.append('<div class="error__upload_message">Ошибка загрузки файла!</div>');
        data.context.find('i').remove();
        setTimeout(function(){data.context.remove();},5000);
        //' + data.jqXHR.responseText +
    });

    // Helper для формата размера файла
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }

});