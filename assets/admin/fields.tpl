<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
            <span class="title">{lang('Поля формы - ', 'xforms')} <b>{$form_name}</b></span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">
                <a href="/admin/components/cp/xforms/" class="t-d_n m-r_15"><span class="f-s_14">←</span> <span
                            class="t-d_u">{lang("Back","admin")}</span></a>
                <button onclick="$('#delete_fields').modal();" type="button"
                        class="btn btn-small btn-danger action_on pages_action pages_delete" disabled="disabled"><i
                            class="icon-trash"></i>Удалить
                </button>
                <a href="/admin/components/cp/xforms/field/{$form_id}" class="btn btn-small pjax btn-success"><i
                            class="icon-plus-sign icon-white"></i>{lang("Create","admin")}</a>
            </div>
        </div>
    </div>
    {if $fields}
        <table id="cats_table" class="table  table-bordered table-hover table-condensed t-l_a">
            <thead>
            <th class="t-a_c span1">
                    <span class="frame_label">
                        <span class="niceCheck">
                            <input type="checkbox">
                        </span>
                    </span>
            </th>
            <th>ID</th>
            <th>Тип</th>
            <th>Имя</th>
            <th>Статус</th>
            </thead>
            <tbody class="sortable save_positions" data-url="/admin/components/cp/xforms/update_positions">
            {foreach $fields as $field}
                <tr>
                    <td class="t-a_c span1">
                            <span class="frame_label">
                                <span style="background-position: -46px 0px;" class="niceCheck">
                                    <input data-id="{$field.id}" name="ids" value="{$field.id}" type="checkbox">
                                </span>
                            </span>
                    </td>
                    <td>{$field.id}</td>
                    <td>{$field.type}</td>
                    <td>
                        <a href="/admin/components/cp/xforms/field/{$form_id}/{$field.id}"
                           data-rel="tooltip"
                           data-title="{lang("Editing","admin")}">{$field.label}</a>
                    </td>
                    <td>
                        <div class="frame_prod-on_off" data-rel="tooltip" data-placement="top"
                             data-original-title="{if $field.visible}{lang("show","admin")}{else:}{lang("don't show", 'admin')}{/if}"
                             onclick="xforms.change_field_visible('{$field.id}');">
                            <span class="prod-on_off {if !$field.visible}disable_tovar{/if}"
                                  style="{if !$field.visible}left: -28px;{/if}"></span>
                        </div>
                    </td>

                </tr>
            {/foreach}
            </tbody>
        </table>
    {else:}
        <div class="alert alert-info m-t_20">
            В форме нет полей.
        </div>
    {/if}
</section>

<div class="modal hide fade products_delete_dialog" id="delete_fields">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>{lang('Удалить поля','xforms')}</h3>
    </div>
    <div class="modal-body">
        {lang('Удалить выбраные поля?', 'xforms')}
    </div>
    <div class="modal-footer">
        <a href="" class="btn" onclick="$('#delete_fields').modal('hide');">Отмена</a>
        <a href="" class="btn btn-primary"
           onclick="xforms.deleteFieldsConfirm();$('.modal').modal('hide');">{lang("Delete","admin")}</a>
    </div>
</div>

{literal}
    <script>
        var xforms = new Object({

            deleteFieldsConfirm: function () {
                var ids = new Array();

                $('input[name=ids]:checked').each(function () {
                    ids.push($(this).val());
                });

                $.post(base_url + 'admin/components/cp/xforms/delete_fields', {
                    id: ids
                }, function (data) {
                    $('#mainContent').after(data);
                    $.pjax({
                        url: window.location.pathname,
                        container: '#mainContent',
                        timeout: 3000
                    });
                });

                $('.modal').modal('hide');
                return false;
            },

            change_field_visible: function (field_id) {
                console.log(field_id);
                $.post(base_url + 'admin/components/cp/xforms/change_visible/' + field_id, {}, function (data) {
                    $('.notifications').append(data);
                })
            }
        });
    </script>
{/literal}