<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
			<span class="title">
                {if $form.id}
                    {lang('Edit form', 'xforms')}
                {else:}
                    {lang('Create form', 'xforms')}
                {/if}
            </span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">
                <a href="/admin/components/cp/xforms/" class="{if !$form.id}pjax{/if} t-d_n m-r_15"><span
                            class="f-s_14">←</span> <span class="t-d_u">{lang("Back","xforms")}</span></a>

                <button type="button"
                        class="btn btn-small {if !$form.id}btn-success{else:}btn-primary{/if} action_on formSubmit"
                        data-action="edit" data-form="#save" data-submit>
                    {if $form.id}
                        <i class="icon-ok icon-white"></i>
                        {lang("Save","xforms")}{else:}
                        <i class="icon-plus-sign icon-white"></i>
                        {lang("Create","xforms")}{/if}
                </button>

                <button type="button" class="btn btn-small action_on formSubmit" data-action="close" data-form="#save">
                    <i class="icon-check"></i>{if $form.id}{lang("Save and go back","xforms")}{else:}{lang("Create and exit","xforms")}{/if}
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
                                  action="{site_url('admin/components/cp/xforms/form')}{if $form.id}/{$form.id}{else:}{/if}">

                                <div class="control-group">
                                    <label class="control-label" for="page_title">Название <span
                                                class="must">*</span></label>
                                    <div class="controls">
                                        <div class="o_h">
                                            <input type="text" class="textbox_long" name="page_title" id="page_title_u"
                                                   value="{$form.title}" required>
                                        </div>
                                        <span class="help-block">Выводиться в заголовке и title</span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="page_url">
                                        URL: <span class="must">*</span>
                                    </label>
                                    <div class="controls">
                                        <button onclick="translite_title('#page_title_u', '#page_url');" type="button"
                                                class="btn m-l_10" style="float:right;"><i class="icon-refresh"></i>&nbsp;&nbsp;Автозаполнение
                                        </button>
                                        <div class="o_h">
                                            <input type="text" name="page_url" value="{$form.url}" id="page_url"
                                                   required>
                                        </div>
                                        <div class="help-block">только латинские символы</div>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="controls">
                                        <span class="frame_label no_connection m-t_5 m-b_10">
                                            <span class="niceCheck b_n">
                                                <input type="checkbox" value="1"
                                                       name="direct_url"{if $form.direct_url} checked{/if}>
                                            </span>
                                            Разрешить прямой доступ через URL?
                                            <span class="help-block">
                                                Если опция включена, на форму можно зайти по прямому URL вида - /xforms/show/FORM_URL<br/>
                                                Если отключена, форма будет доступна только при обращении из виджета
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                {if !$form.id}
                                <div class="control-group">
                                    <label class="control-label" for="email">Настройка email: <span class="must">*</span></label>

                                    <div class="controls">
                                        <div class="o_h">
                                            <input type="text" class="textbox_long" name="email" id="email"
                                                   value="" required/>
                                        </div>
                                        <span class="help-block">E-mail'ы куда приходят сообщения с формы (Можно несколько, через запятую)</span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="subject">Тема письма: <span class="must">*</span></label>
                                    <div class="controls">
                                        <div class="o_h">
                                            <input type="text" class="textbox_long" name="subject" id="subject"
                                                   value="" required/>
                                        </div>
                                        <span class="help-block">На почту придет письмо с такой темой.</span>
                                    </div>
                                </div>
                                {else:}
                                    <div class="control-group">
                                        <label class="control-label" for="email">Настройка email: <span class="must">*</span></label>

                                        <div class="controls">
                                            <a href="{site_url('admin/components/cp/cmsemail/edit')}/{$cmsemail['id']}" target="_blank">через модуль cmsemail</a>
                                        </div>
                                    </div>
                                {/if}

                                <div class="control-group">
                                    <div class="controls">
                                        <span class="frame_label no_connection m-t_5 m-b_10">
                                            <span class="niceCheck b_n">
                                                <input type="checkbox" value="1"
                                                       name="captcha"{if $form.captcha} checked{/if}>
                                            </span>
                                            Исспользовать защитный код при отправке? (Каптча)
                                        </span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="action_files">
                                        Прикрепленные файлы к форме:
                                    </label>
                                    <div class="controls">
                                        <select name="action_files" id="action_files">
                                            <option value="1" {if $form.action_files == 1}selected="selected"{/if}>Сохранять на сервере</option>
                                            <option value="2" {if $form.action_files == 2}selected="selected"{/if}>Только прикреплять к письму</option>
                                            <option value="3" {if $form.action_files == 3}selected="selected"{/if}>Прикреплять к письму и сохранять на сервере</option>
                                        </select>
                                    </div>
                                </div>


                            {if $form.id}
                                <div class="control-group">
                                    <label class="control-label" for="user_message_active">
                                        Укажите поле формы с email'ом отправителя:
                                    </label>
                                    <div class="controls">

                                        <select name="user_message_active" id="user_message_active">
                                            <option value="0" {if !$form.user_message_active}selected="selected"{/if}>Не отправлять клиенту письмо</option>

                                            {foreach $fields as $field}
                                                <option value="{$field.id}" {if $form.user_message_active == $field.id}selected="selected"{/if}>{$field.label} ({$field.id})</option>
                                            {/foreach}
                                        </select>
                                        <span class="help-block">
                                        Если отправителю тоже нужно прислать письмо, после отправки.<br/>
                                        Из какого поля формы будем брать email отправителя, что бы отправить ему письмо?
                                        </span>
                                    </div>
                                </div>
                            {/if}

                                <div class="control-group">
                                    <div class="control-label">
                                        Описание формы
                                    </div>
                                    <div class="controls">
                                        <textarea id="desc" class="elRTE" name="desc" rows="10"
                                                  cols="180">{$form.desc}</textarea>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label">
                                        Сообщение об успешной отправке
                                    </div>
                                    <div class="controls">
                                        <textarea id="good" class="elRTE" name="good" rows="10" cols="180">
                                            {if $form.id}
                                                {$form.success}
                                            {else:}
                                                {lang('Your request has been successfully sent!', 'xforms')}
                                            {/if}
                                        </textarea>
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