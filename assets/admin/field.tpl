<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
            <span class="title">{if $field.id}{lang('Editing fields in the form', 'xforms')}{else:}{lang('Adding fields in the form', 'xforms')}{/if}
                - <b>{echo $CI->load->model('xforms_model')->get_form_name($fid)}</b></span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">

                <a href="/admin/components/cp/xforms/fields/{$fid}" class="{if !$field.id}pjax{/if} t-d_n m-r_15"><span
                            class="f-s_14">←</span> <span class="t-d_u">{lang("Back","xforms")}</span></a>

                <button type="button"
                        class="btn btn-small {if !$field.id}btn-success{else:}btn-primary{/if} action_on formSubmit"
                        data-action="edit" data-form="#save" data-submit>
                    {if $field.id}
                        <i class="icon-ok icon-white"></i>
                        {lang("Save","xforms")}{else:}
                        <i class="icon-plus-sign icon-white"></i>
                        {lang("Create","xforms")}{/if}
                </button>

                <button type="button" class="btn btn-small action_on formSubmit" data-action="exit" data-form="#save">
                    <i class="icon-check"></i>{if $field.id}{lang("Save and go back","xforms")}{else:}{lang("Create and exit","xforms")}{/if}
                </button>
            </div>
        </div>
    </div>
    <div class="tab-pane active" id="xforms">
        <table class="table table-bordered table-hover table-condensed content_big_td">
            <thead>
            <tr>
                <th colspan="6">
                    Параметры
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="6">
                    <div class="inside_padd">
                        <div class="form-horizontal">
                            <form id="save" method="post"
                                  action="{site_url('/admin/components/cp/xforms/field')}/{$fid}{if $field.id}/{$field.id}{/if}">
                                <div class="control-group">
                                    <label class="control-label" for="type">Тип поля: </label>
                                    <div class="controls">
                                        <select name="type" id="type">
                                            <option value="text"{if $field.type=='text'} selected="selected"{/if}>text
                                            </option>
                                            <option value="textarea"{if $field.type=='textarea'} selected="selected"{/if}>
                                                textarea
                                            </option>
                                            <option value="checkbox"{if $field.type=='checkbox'} selected="selected"{/if}>
                                                checkbox
                                            </option>
                                            <option value="select"{if $field.type=='select'} selected="selected"{/if}>
                                                select
                                            </option>
                                            <option value="radio"{if $field.type=='radio'} selected="selected"{/if}>
                                                radio
                                            </option>
                                            <option value="file"{if $field.type=='file'} selected="selected"{/if}>
                                                загрузка файлов
                                            </option>
                                            <option value="" disabled>------</option>
                                            <option value="separator"{if $field.type=='separator'} selected="selected"{/if}>
                                                разделитель
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="name">
                                        Имя поля: <span class="must">*</span>
                                    </label>
                                    <div class="controls">
                                        <div class="o_h">
                                            <input type="text" name="name" id="name" value="{$field.label}" required>
                                        </div>
                                        <div class="help-block">показывается в label</div>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label"></div>
                                    <div class="controls">
											<span class="frame_label no_connection">
												<span class="niceCheck" style="background-position: -46px 0px;">
													<input type="checkbox" name="required"
                                                           value="1" {if $field.require==1} checked="checked"{/if}/>
												</span>
												Обязательно для заполнения</span>
                                    </div>
                                </div>

                                <div class="control-group" style="display:{if $field.type == 'select' || $field.type == 'radio' || $field.type == 'checkbox'}block{else:}none{/if}">
                                    <label class="control-label" for="value">
                                        Значение
                                    </label>
                                    <div class="controls">
                                        <textarea name="value" id="value">{$field.value}</textarea>
                                        <span class="help-block">Аттрибут value, для checkbox, select, radio каждое новое значение указывайте в новой строке. </span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="desc">
                                        Описание
                                    </label>
                                    <div class="controls">
                                        <div class="o_h">
                                            <input type="text" name="desc" id="desc" value="{$field.desc}"/>
                                        </div>
                                        <span class="help-block">Подсказка для поля</span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="validation">Условия проверки:</label>
                                    <div class="controls">
                                        <div class="o_h">
                                            <input name="validation" id="validation" value="{$field.validation}"
                                                   type="text">
                                        </div>
                                        <span class="help-block">Например: valid_email|max_length[255]|min_length[1]|numeric|valid_phone итд.<br/>trim|required|xss_clean Не нужно вставлять.<br/>
                                        </span>
                                    </div>
                                </div>

                                <div class="control-group" style="display:{if $field.type == 'file'}block{else:}none{/if}">
                                    <label class="control-label" for="allowed_types">Расширения файлов:</label>
                                    <div class="controls">
                                        <div class="o_h">
                                            <input name="allowed_types" id="allowed_types" value="{$field.allowed_types}"
                                                   type="text">
                                        </div>
                                        <span class="help-block">Впишите расширения файлов, доступных к загрузке, через  |<br/>Например: gif|jpg|png  или впишите *, если хотите загружать файлы любых форматов</span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="operation">Операции и стили:</label>
                                    <div class="controls">
                                        <div class="o_h">
                                            <textarea id="operation" name="operation" rows="10"
                                                      cols="180">{$field.operation}</textarea>
                                        </div>
                                        <span class="help-block">Возможность добавить к полю новые аттрибуты, классы, стили, события.</span>
                                    </div>
                                </div>


                                <div class="control-group">
                                    <label class="control-label" for="position">
                                        Позиция:
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="position" id="position"
                                               value="{if $field.position!==0}{$field.position}{else:}0{/if}">
                                        <div class="help-block">Позиция поля</div>
                                    </div>
                                </div>

                                <div class="control-group" style="display:{if !$field || ($field.type == 'textarea' || $field.type == 'text')}block{else:}none{/if}">
                                    <label class="control-label" for="maxlength">
                                        Максимум символов:
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="maxlength" id="maxlength" value="{$field.maxlength}">
                                        <div class="help-block">Максимальное количество символов<br/>
                                            Этот параметр для input type="text" и textarea. Не путать с max_length[255]!
                                        </div>
                                    </div>
                                </div>

                                <div class="control-group" style="display:{if $field.type == 'select' || $field.type == 'radio' || $field.type == 'checkbox'}block{else:}none{/if}">
                                    <div class="control-label"></div>
                                    <div class="controls">
											<span class="frame_label no_connection">
												<span class="niceCheck" style="background-position: -46px 0px;">
													<input type="checkbox" name="check" id="check"
                                                           value="1" {if $field.checked==1} checked="checked"{/if}/>
												</span>
												Ометить первое значение checkbox/radio/select</span>
                                    </div>
                                </div>

                                <div class="control-group" style="display:{if !$field || ($field.type == 'textarea' || $field.type == 'text' || $field.type == 'select' || $field.type == 'checkbox')}block{else:}none{/if}">
                                    <div class="control-label"></div>
                                    <div class="controls">
											<span class="frame_label no_connection">
												<span class="niceCheck" style="background-position: -46px 0px;">
													<input type="checkbox" name="disable" id="disable"
                                                           value="1" {if $field.disabled==1} checked="checked"{/if}/>
												</span>
												Отключённое поле (параметр disabled)</span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label"></div>
                                    <div class="controls">
											<span class="frame_label no_connection">
												<span class="niceCheck" style="background-position: -46px 0px;">
													<input type="checkbox" name="visible" id="visible"
                                                           value="1" {if $field.visible==1 || !isset($field)} checked="checked"{/if}>
												</span>
												Видимое поле?</span>
                                    </div>
                                </div>

                                {form_csrf()}
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</section>

{literal}
    <script type="text/javascript">
        $(document).on('change', 'select[name="type"]', function() {

            var selected_val = $(this).val(),
                showers_line = {
                    checkbox: ['value', 'check', 'disable'],
                    select: ['value', 'check', 'disable'],
                    radio: ['check', 'value'],
                    text: ['maxlength', 'disable'],
                    textarea: ['maxlength', 'disable'],
                    file: ['allowed_types']
                },
                optional_fields = ['check', 'value', 'maxlength', 'allowed_types', 'disable'];

            // Вылючаем опциональные поля
            for(var i in optional_fields) {

                var field_line = $('#' + optional_fields[i]).closest('.control-group');

                if($.inArray(optional_fields[i], showers_line[selected_val]) > -1) {
                    $(field_line).css('display', 'block');
                } else {
                    $(field_line).css('display', 'none');
                }
            }
        });
    </script>
{/literal}