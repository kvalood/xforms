{/* Шаблон отображения формы XFORMS как виджет */}

<h1>{$form.title}</h1>

{if $form.desc}
	<div class="xforms_description">{$form.desc}</div>
{/if}

{if $fields}
    <form action="{site_url('xforms/show')}/{$form.url}" method="post" enctype="multipart/form-data" id="{$form.url}" class="xform">

        {form_csrf()}
        <input type="hidden" name="cms_widget_form" value="1"/>
        <input type="hidden" name="form_url" value="{$form.url}"/>

        <ul class="fields g-row g-row_indent-30">
            {foreach $fields as $field}
                <li class="field__item{if $field.type=='separator'} item_separator{/if} {$field.operation}">
                    {if $field.type=='select' || $field.type=='radio' || $field.type == 'separator'}
                        <div class="field__title">{if $field.require==1}<i>*</i>{/if} {$field.label}</div>
                    {else:}
                        <label for="f{$field.id}" class="field__title">
                            {if $field.require==1}<i>*</i>{/if}{$field.label}
                        </label>
                    {/if}

                    {if $field.type=='text'}
                        <input type="text" name="f{$field.id}" id="f{$field.id}"
                               value="{$field.value}"{if $field.maxlength >0} maxlength="{$field.maxlength}"{/if}{if $field.disabled==1} disabled="disabled"{/if} />
                    {elseif $field.type=='textarea'}
                        <textarea name="f{$field.id}" class="message_text"
                                  id="f{$field.id}"{if $field.disabled==1} disabled="disabled"{/if}>{$field.value}</textarea>
                    {elseif $field.type=='checkbox'}
                        <input type="checkbox" name="f{$field.id}" id="f{$field.id}"
                               value="{$field.value}"{if $field.disabled==1} disabled="disabled"{/if} />
                        {$value = explode("\n",$field.value)}
                        <ul class="field__radio">
                            {foreach $value as $key => $val}
                                <li><label><input type="radio" name="f{$field.id}" value="{$key}" id="{$field.id}{$key}"
                                                  {if $key == 0}checked{/if}/> <span>{$val}</span></label></li>
                            {/foreach}
                        </ul>
                    {elseif $field.type=='select'}
                        <select name="f{$field.id}" id="f{$field.id}" {if $field.disabled==1}disabled="disabled"{/if}>
                            {$value = explode("\n",$field.value)}
                            {foreach $value as $val}
                                <option value="{$val}">{$val}</option>
                            {/foreach}
                        </select>
                    {elseif $field.type=='radio'}
                        {$value = explode("\n",$field.value)}
                        <ul class="field__radio">
                            {foreach $value as $key => $val}
                                <li><label><input type="radio" name="f{$field.id}" value="{$key}" id="{$field.id}{$key}"
                                                  {if $key == 0}checked{/if}/> <span>{$val}</span></label></li>
                            {/foreach}
                        </ul>
                    {/if}
                    {if $field.desc}<p class="desc">{$field.desc}</p>{/if}
                </li>
            {/foreach}


            {if $form.captcha}
                <li class="field__item g-col-4 g-col-6_from-m g-col-12_from-s">
                    <div class="field__title">{tlang('Type the characters you see in this image.')}</div>
                    <div class="captcha_image">{$form.captcha_image}</div>
                    <input type="text" name="captcha" autocomplete="off" required>
                </li>
            {/if}

            <li class="submit g-col-12">
                <input type="submit" class="more-link bth" onClick="send_widget_form($(this));return false;"
                       value="Отправить заявку"/>
            </li>

        </ul>

    </form>
{/if}