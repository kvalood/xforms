<form method="get" action="/admin/components/cp/xforms/messages" id="messagesListFilter" class="listFilterForm">
    <section class="mini-layout">
        <div class="frame_title clearfix">
            <div class="pull-left">
                <span class="help-inline"></span>
                <span class="title">Сообщения - xForms</span>
            </div>
            <div class="pull-right">
                <div class="d-i_b">
                    <a href="/admin/components/cp/xforms/" class="t-d_n m-r_15"><span class="f-s_14">←</span> <span
                                class="t-d_u">{lang("Back","xforms")}</span></a>

                    <!--
                    TODO: Feature;
                    <button onclick="$('#delete_messages').modal();" type="button"
                            class="btn btn-small btn-danger action_on pages_action pages_delete" disabled="disabled"><i
                                class="icon-trash"></i>Удалить
                    </button> -->
                </div>
            </div>
        </div>
        {if $messages}
            <table class="table  table-bordered table-hover table-condensed t-l_a">
                <thead>
                    <tr>
                        <th class="t-a_c span1">
                            <span class="frame_label">
                                <span class="niceCheck" style="background-position: -46px 0px; ">
                                    <input type="checkbox">
                                </span>
                            </span>
                        </th>
                        <th>ID</th>
                        <th>Дата создания</th>
                        <th>Форма</th>
                        <!--
                    TODO: Feature;
                        <th>Статус</th>
                        -->
                    </tr> 
                </thead>

                <tbody class="save_positions">
                {foreach $messages as $message}
                    <tr>
                        <td class="t-a_c span1">
                            <span class="frame_label">
                                <span style="background-position: -46px 0px;" class="niceCheck">
                                    <input data-id="{$message.id}" name="ids" value="{$message.id}" type="checkbox">
                                </span>
                            </span>
                        </td>
                        <td>{$message.id}</td>
                        <td>
                            <a href="/admin/components/cp/xforms/message/{$message.id}"
                               data-rel="tooltip"
                               data-title="{lang("Show message","xforms")}">{echo date('d-m-Y H:i', $message.created)}</a>
                        </td>
                        <td>{$message.title}</td>
                        <!--
                    TODO: Feature;
                        <td>
                            <div class="frame_prod-on_off" data-rel="tooltip" data-placement="top"
                                 data-original-title="{if $message.status}{lang("show","xforms")}{else:}{lang("don't show", 'xforms')}{/if}"
                                 onclick="xforms.change_field_visible('{$message.id}');">
                                <span class="prod-on_off {if !$message.status}disable_tovar{/if}"
                                      style="{if !$message.status}left: -28px;{/if}"></span>
                            </div>
                        </td>
                        -->
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {else:}
            <div class="alert alert-info m-t_20">
                {lang("No messages", 'xforms')}
            </div>
        {/if}
    </section>

    <!--
    TODO: Feature;
    <div class="modal hide fade products_delete_dialog" id="delete_fields">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>{lang('Remove fields','xforms')}</h3>
        </div>
        <div class="modal-body">
            {lang('Remove the selected field?', 'xforms')}
        </div>
        <div class="modal-footer">
            <a href="" class="btn" onclick="$('#delete_fields').modal('hide');">Отмена</a>
            <a href="" class="btn btn-primary"
               onclick="xforms.deleteFieldsConfirm();$('.modal').modal('hide');">{lang("Delete","xforms")}</a>
        </div>
    </div>
    -->
</form>