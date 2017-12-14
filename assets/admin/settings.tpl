<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
            <span class="title">{lang("xForms settings", 'xforms')}</span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">
                <a href="{$BASE_URL}admin/components/cp/xforms" class="t-d_n m-r_15 pjax"><span class="f-s_14">←</span>
                    <span class="t-d_u">{lang("Back", 'xforms')}</span></a>
                <button type="button" class="btn btn-small btn-primary action_on formSubmit" data-action="toedit"
                        data-form="#xforms_settings_form" data-submit><i
                            class="icon-ok icon-white"></i>{lang('Save','xforms')}</button>
                <button type="button" class="btn btn-small action_on formSubmit" data-form="#xforms_settings_form"
                        data-action="tomain" data-submit><i class="icon-check"></i>{lang("Save and exit", 'xforms')}
                </button>
            </div>
        </div>
    </div>
    <form method="post" action="{site_url('admin/components/cp/xforms/update_settings')}" class="form-horizontal m-t_10"
          id="xforms_settings_form">
        <table class="table  table-bordered table-hover table-condensed content_big_td">
            <thead>
            <tr>
                <th colspan="6">
                    {lang("Properties", 'xforms')}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="6">
                    <div class="inside_padd span9">
                        <div class="control-group">
                            <div class="control-label"></div>
                            <div class="controls">
                                    <span class="frame_label no_connection">
                                        <span class="niceCheck">
                                            <input type="checkbox" name="save_messages" value="1"
                                                   {if $settings.save_messages}checked="checked"{/if} />
                                        </span>
                                        {lang("Save messages to the admin panel?", 'xforms')}
                                    </span>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        {form_csrf()}
    </form>
</section>