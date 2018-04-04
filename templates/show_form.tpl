{/* Шаблон отображения формы XFORMS как страница и виджет */}

<div class="xforms_wrapper">

    <div class="xforms_title">
        {if $widget}
            <h2>{$form.title}</h2>
        {else:}
            <h1>{$form.title}</h1>
        {/if}
    </div>

    {if $form.desc}
        <div class="xforms_description">{$form.desc}</div>
    {/if}

    {if $fields}
        <form action="{site_url('xforms/show')}/{$form.url}" method="post" enctype="multipart/form-data" id="{$form.url}" class="xform">

            {form_csrf()}

            {if $widget}<input type="hidden" name="is_widget" value="1" />{/if}
            <input type="hidden" name="form_url" value="{$form.url}" />

            <div class="fields row">
                {foreach $fields as $field}
                    <div class="field__item{if $field.type=='separator'} item_separator{/if} {$field.operation}">

                        {if $field.type=='checkbox' || $field.type=='radio' || $field.type=='select'}
                            {$checkbox_value = array_diff(explode("\n",$field.value), array(''))}
                        {/if}

                        {if (($field.type=='select' || $field.type=='radio') AND count($checkbox_value) >= 1) || $field.type == 'separator' || count($checkbox_value) >= 1}
                            <div class="field__title{if $field.require==1} require_field{/if}">{$field.label}</div>
                        {else:}
                            {if (!$checkbox_value AND $field.type=='checkbox') || $field.type=='text' || $field.type=='textarea' || $field.type=='file'}
                                <label for="f{$field.id}" class="field__title{if $field.require==1} require_field{/if}">
                                    {$field.label}
                                </label>
                            {/if}
                        {/if}

                        {if $field.type=='text'}
                            <input type="text" name="f{$field.id}" id="f{$field.id}" value="{$field.value}"{if $field.maxlength >0} maxlength="{$field.maxlength}"{/if}{if $field.disabled==1} disabled="disabled"{/if}  />
                        {elseif $field.type=='textarea'}
                            <textarea name="f{$field.id}" class="message_text"  id="f{$field.id}"{if $field.maxlength >0} maxlength="{$field.maxlength}"{/if}{if $field.disabled==1} disabled="disabled"{/if}>{$field.value}</textarea>
                        {elseif $field.type=='checkbox'}
                            {if count($checkbox_value) >= 1}
                                <ul class="field__checkbox">
                                    {foreach $checkbox_value as $key => $val}
                                        <li><label><input type="checkbox" name="f{$field.id}[]" value="{$key}" {if $field.checked && $key==0}checked{/if}/> <span>{$val}</span></label></li>
                                    {/foreach}
                                </ul>
                            {else:}
                                <input type="checkbox" name="f{$field.id}" id="f{$field.id}" value="{if empty($checkbox_value)}1{else:}{$field.value}{/if}"{if $field.disabled==1 AND !$field.require} disabled="disabled"{/if} {if $field.checked}checked{/if} />
                            {/if}
                        {elseif $field.type=='select' AND $checkbox_value}
                            <select name="f{$field.id}" id="f{$field.id}" {if $field.disabled==1}disabled="disabled"{/if}>
                                {if !$field.checked}
                                    <option value="">Выберите значение</option>
                                {/if}
                                {foreach $checkbox_value as $key => $val}
                                    <option value="{$key}" {if $field.checked && $key==0}selected{/if}>{$val}</option>
                                {/foreach}
                            </select>
                        {elseif $field.type=='radio'}
                            {if count($checkbox_value) >= 1}
                                <ul class="field__radio">
                                    {foreach $checkbox_value as $key => $val}
                                        <li><label><input type="radio" name="f{$field.id}" value="{$key}" id="{$field.id}{$key}" {if $field.checked && $key==0}checked{/if}/> <span>{$val}</span></label></li>
                                    {/foreach}
                                </ul>
                            {/if}
                        {elseif $field.type=='file'}
                            <div class="field__item file" data-field-id="{$field.id}">
                                <div class="drop_here">
                                    Перетащите сюда или
                                    <a>Выберите файл</a>
                                    <input type="file" name="f{$field.id}[]" data-url="{site_url('xforms')}/upload/{$field.id}" multiple>
                                </div>
                                <ul class="file_list"></ul>
                            </div>
                        {/if}
                        {if $field.desc}<p class="desc">{$field.desc}</p>{/if}
                    </div>
                {/foreach}

                {if $form.captcha}
                    <div class="field__item captcha_field col s12">
                        <div class="field__title">{lang('Type the characters you see in this image.', 'xforms')}</div>
                        <div class="captcha_image">{$form.captcha_image}</div>
                        <input type="text" name="captcha" autocomplete="off" required />
                    </div>
                {/if}

                <div class="submit col s12">
                    <input type="submit" class="more-link bth" onClick="send_widget_form($(this));return false;" value="{lang('Send order', 'xforms')}" />
                </div>

            </div>

        </form>
    {/if}
</div>