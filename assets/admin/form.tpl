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
                                        <div class="help-block">(только латинские символы)</div>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="email">E-mail: <span class="must">*</span></label>
                                    <div class="controls">
                                        <div class="o_h">
                                            <input type="text" class="textbox_long" name="email" id="email"
                                                   value="{$form.email}" required/>
                                        </div>
                                        <span class="help-block">E-mail'ы куда приходят сообщения с формы (Можно несколько, через запятую)</span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="subject">Тема письма: <span class="must">*</span></label>
                                    <div class="controls">
                                        <div class="o_h">
                                            <input type="text" class="textbox_long" name="subject" id="subject"
                                                   value="{$form.subject}" required/>
                                        </div>
                                        <span class="help-block">На почту придет письмо с такой темой.</span>
                                    </div>
                                </div>

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