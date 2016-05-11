<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
            <span class="title">Список отправленных сообщений</span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">
                <a href="/admin/components/cp/xforms/" class="t-d_n m-r_15"><span class="f-s_14">←</span> <span class="t-d_u">Венуться</span></a>
            </div>
        </div>
    </div>
    {if $message}
        <table id="cats_table" class="table table-striped table-bordered table-hover table-condensed content_big_td">
            <thead>
            <th>ID</th>
            <th>Автор</th>
            <th>Сообщение</th>
            <th>Файл</th>
            <th>Дата</th>
            </thead>
            <tbody>
                {foreach $message as $msg}
                    <tr>
                        <td>{$msg.id}</td>
                        <td>
                            {$msg.author}
                        </td>
                        <td>
                           {$msg.msg}
                        </td>
                        <td>{$msg.file}</td>
                        <td>{date('d.m.Y H:i',$msg.date)}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else:}
        <div class="alert alert-info m-t_20">
            <p>Нет сообщений</p>
        </div>
    {/if}
</section>