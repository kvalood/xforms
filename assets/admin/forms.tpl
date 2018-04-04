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
            <th class="span5">Наименование</th>
            <th class="span4">URL</th>
            <th class="span2">Действия</th>
            </thead>
            <tbody>
            {foreach $forms as $form}
                <tr>
                    <td>{$form.id}</td>
                    <td class="share_alt">
                        <div class="o_h">
                            <a class="pjax" href="/admin/components/cp/xforms/fields/{$form.id}" data-rel="tooltip"
                               data-placement="top" data-original-title="{lang('Editing fields in the form', 'xforms')}">{$form.title}</a>
                        </div>
                    </td>
                    <td>
                        {if $form.direct_url}
                            <a href="{site_url('xforms/show')}/{$form.url}" target="_blank" data-rel="tooltip"
                               data-placement="top" data-original-title="{lang('View on the site', 'xforms')}">{$form.url}</a>
                        {else:}
                            {lang('Does not appear on the site', 'xforms')}
                        {/if}
                    </td>
                    <td>
                        <a class="btn btn-small" data-rel="tooltip" data-title="{lang('Settigns form', 'xforms')}"
                           href="/admin/components/cp/xforms/form/{$form.id}" data-original-title=""><i class="icon-wrench"></i>
                        </a>

                        {if $form.cmsemail_id}
                        <a class="btn btn-small" data-rel="tooltip" data-title="{lang('Edit cmsemail settings for this form', 'xforms')}"
                           href="{site_url('/admin/components/cp/cmsemail/edit')}/{$form.cmsemail_id}" target="_blank"><i class="icon-envelope"></i>
                        </a>
                        {/if}

                        <button onclick="xforms.deleteForm({$form.id});" class="btn btn-small" data-rel="tooltip"
                                data-title="{lang('Delete form', 'xforms')}"><i class="icon-trash"></i></button>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else:}
        <div class="alert alert-info m-t_20">
            <p>{lang('Forms are missing', 'xforms')}</p>
        </div>
    {/if}
</section>

<div class="modal delete_form hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>{lang('Delete form', 'xforms')}</h3>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" onclick="$('.modal.delete_form').modal('hide');">{lang('Cancel', 'xforms')}</a>
        <a href="#" class="btn btn-primary" onClick="xforms.deleteFormConfirm();">{lang('Remove', 'xforms')}</a>
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