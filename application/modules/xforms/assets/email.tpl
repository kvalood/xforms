<table cellpadding="6" cellspacing="0" style="border-collapse: collapse;">

    {foreach $data as $item}
    <tr>
        {if $item.field.type == 'separator'}
            <td colspan="2" style="background-color:#e5ebf0;">{$item.field.label}</td>
        {else:}
            <td style="padding:6px; width:40%; background-color:#e1eef0; border:1px solid #becedc;font-size: 14px;">
                {$item.field.type}
            </td>
            <td style="padding:6px; width:60%; border:1px solid #becedc;font-size: 14px;">
                {$item.data}
            </td>
        {/if}
    </tr>
    {/foreach}

</table>