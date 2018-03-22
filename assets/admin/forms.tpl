<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
            <span class="title">{lang('Form designer', 'xforms')}</span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">
                <a href="/admin/components/cp/xforms/form" class="btn btn-small pjax btn-success"><i
                            class="icon-plus-sign icon-white"></i>{lang('Create form', 'xforms')}</a>
            </div>
        </div>
    </div>
    {if $forms}
        <table id="cats_table" class="table  table-bordered table-hover table-condensed t-l_a">
            <thead>
            <th class="span1">ID</th>
            <th class="span4">Наименование</th>
            <th class="span2">URL</th>
            <th class="span2">Тема</th>
            <th class="span1">E-mail</th>
            <th class="span1">Действия</th>
            </thead>
            <tbody>
            {foreach $forms as $form}
                <tr>
                    <td>{$form.id}</td>
                    <td class="share_alt">
                        <div class="o_h">
                            <a class="pjax" href="/admin/components/cp/xforms/fields/{$form.id}" data-rel="tooltip"
                               data-placement="top" data-original-title="Редактировать поля">{$form.title}</a>
                        </div>
                    </td>
                    <td>
                        {if $form.direct_url}
                            <a href="{site_url('xforms/show')}/{$form.url}" target="_blank" data-rel="tooltip"
                               data-placement="top" data-original-title="Посмотреть на сайте">{$form.url}</a>
                        {else:}
                            Не показывается на сайте
                        {/if}
                    </td>
                    <td>{$form.subject}</td>
                    <td>{$form.email}</td>
                    <td>
                        <a class="btn btn-small" data-rel="tooltip" data-title="Настройки формы"
                           href="/admin/components/cp/xforms/form/{$form.id}" data-original-title="">
                            <i class="icon-wrench"></i>
                        </a>
                        <button onclick="xforms.deleteForm({$form.id});" class="btn btn-small" data-rel="tooltip"
                                data-title="Удалить форму"><i class="icon-trash"></i></button>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else:}
        <div class="alert alert-info m-t_20">
            <p>Ещё не создано ни одной формы.</p>
        </div>
    {/if}
</section>

<div class="modal delete_form hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Удалить форму</h3>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" onclick="$('.modal.delete_form').modal('hide');">Отмена</a>
        <a href="#" class="btn btn-primary" onClick="xforms.deleteFormConfirm();">Удалить</a>
    </div>
</div>

{literal}
    <script>
        var xforms = new Object({
            deleteForm: function (id) {
                $('.modal.delete_form').modal();
                xforms.id = id;
            },
            deleteFormConfirm: function () {
                $.post('/admin/components/cp/xforms/delete_form', {
                    id: xforms.id
                }, function (data) {
                    $('#mainContent').after(data);
                    $.pjax({
                        url: window.location.pathname,
                        container: '#mainContent',
                        timeout: 1000
                    });
                    showMessage('Готово', 'Форма успешно удалена');
                });
                $('.modal.delete_form').modal('hide');
                return false;
            }
        });
    </script>
{/literal}