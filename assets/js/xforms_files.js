// Загрузка файлов
$(function () {

    // Симуляция клика по file input для показа диалога выбора файла
    $(document).on('click', '.drop_here a', function(){
        $(this).parent().find('input').click();
    });

    // Инициализация "jQuery File Upload plugin" для одной или нескольких форм
    $('.field__item.file input[type="file"]').each(function () {
        $(this).fileupload({
            dataType: 'json',
            dropZone: $(this).closest('form').find('.drop_here')
        }).on('fileuploadadd', function (e, data) {

            var tpl = $('<li class="working"><i></i><div></div><span>×</span></li>');

            // Добавим имя файла и его размер
            tpl.find('div').text(data.files[0].name)
                .append('<div>' + formatFileSize(data.files[0].size) + '</div>');

            // Add the HTML to the UL element
            data.context = tpl.appendTo($(this).closest('.field__item').find('.file_list'));

            // Listen for clicks on the cancel icon
            tpl.find('span').click(function(){
                if(tpl.hasClass('working')){
                    jqXHR.abort();
                } else {
                    // добавить удаление файла
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

            if(progress == 100){
                data.context.removeClass('working').addClass('uploaded');
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
        }).on('fileuploadstart', function (e) {
            // загрузка началась, заморозим кнопку
            $(this).closest('form').find('[type="submit"]').prop("disabled", true);
        }).on('fileuploadstop', function (e) {
            // загрузка закончилась, разморозим кнопку
            $(this).closest('form').find('[type="submit"]').prop("disabled", false);
        });
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