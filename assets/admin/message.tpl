<div class="container systen-info">
    <section class="mini-layout">
        <div class="frame_title clearfix">
            <div class="pull-left">
                <span class="help-inline"></span>
                <span class="title">{lang("Messages","xforms")} #{$message.id}</span>
            </div>
            <div class="pull-right">
                <div class="d-i_b">
                    <a href="/admin/components/cp/xforms/messages" class="t-d_n m-r_15"><span class="f-s_14">←</span> <span
                                class="t-d_u">{lang("Back","xforms")}</span></a>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <table class="table  table-bordered table-hover table-condensed">
                <tbody>
                <tr>
                    <td class="span2">{lang("Date of creation","xforms")}</td>
                    <td>
                        {echo date('d-m-Y H:i:s', $message.created)}
                    </td>
                </tr>
                <tr>
                    <td class="span2">
                        {lang("Name form","xforms")}
                    </td>
                    <td>
                        {$message.title}
                    </td>
                </tr>
                <tr>
                    <td class="span2">
                        {lang("Status message","xforms")}
                    </td>
                    <td>
                        {$message.status}
                    </td>
                </tr>
                <tr>
                    <td class="span2">
                        {lang('Filled fields','xforms')}
                    </td>
                    <td>
                        {$message.message}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>