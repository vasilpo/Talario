<style type="text/css">
    .open-close-radio:first-of-type{
        margin-right: 10px;
    }
    .open-close-radio input{
        margin-left: 5px;
    }
</style>    
            {if !empty($company_data.booking_data.days_data)}
                {assign var="days_data" value=unserialize($company_data.booking_data.days_data)}
            {/if}
            <table class="table table-middle" width="100%">
                <thead class="cm-first-sibling">
                    <tr>
                        <th width="20%">{__("ec_table_booking_system.days")}</th>
                        <th width="20%">{__("ec_table_booking_system.status")}</th>
                        <th width="20%">{__("ec_table_booking_system.start_time")}</th>
                        <th width="20%">{__("ec_table_booking_system.end_time")}</th>
                        <th width="20%">{__("ec_table_booking_system.booking_slots")}</th>
                    </tr>
                </thead>
                <tbody>
                    {if !empty($days_data)}
                        <tr class="sunday" id="datepairRange_sunday">
                            <td>
                                {__("ec_table_booking_system.sunday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = $days_data.sunday_status
                                    input_name = "company_data[booking_data][sunday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_sunday_timing" name="company_data[booking_data][sunday_timing_start_time]" value="{$days_data.sunday_timing_start_time}" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_sunday_timing_end" name="company_data[booking_data][sunday_timing_end_time]" value="{$days_data.sunday_timing_end_time}"/>
                            </td>
                            <td>
                                <a  href='{"ec_table_booking_system.get_slots&day=sunday&book_slot=`$company_data.booking_data.slot_time`&break_Slot=`$company_data.booking_data.free_time`&start_time=`$days_data.sunday_timing_start_time`&end_time=`$days_data.sunday_timing_end_time`&company_id=`$company_data.company_id`"|fn_url}' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="monday" id="datepairRange_monday">
                            <td>
                                {__("ec_table_booking_system.monday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = $days_data.monday_status
                                    input_name = "company_data[booking_data][monday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_monday_timing" name="company_data[booking_data][monday_timing_start_time]" value="{$days_data.monday_timing_start_time}" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_monday_timing_end" name="company_data[booking_data][monday_timing_end_time]" value="{$days_data.monday_timing_end_time}"/>
                            </td>
                            <td>
                                <a  href='{"ec_table_booking_system.get_slots&day=monday&book_slot=`$company_data.booking_data.slot_time`&break_Slot=`$company_data.booking_data.free_time`&start_time=`$days_data.monday_timing_start_time`&end_time=`$days_data.monday_timing_end_time`&company_id=`$company_data.company_id`"|fn_url}' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="tuesday" id="datepairRange_tuesday">
                            <td>
                                {__("ec_table_booking_system.tuesday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = $days_data.tuesday_status
                                    input_name = "company_data[booking_data][tuesday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_tuesday_timing" name="company_data[booking_data][tuesday_timing_start_time]" value="{$days_data.tuesday_timing_start_time}" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_tuesday_timing_end" name="company_data[booking_data][tuesday_timing_end_time]" value="{$days_data.tuesday_timing_end_time}"/>
                            </td>
                            <td>
                                <a  href='{"ec_table_booking_system.get_slots&day=tuesday&book_slot=`$company_data.booking_data.slot_time`&break_Slot=`$company_data.booking_data.free_time`&start_time=`$days_data.tuesday_timing_start_time`&end_time=`$days_data.tuesday_timing_end_time`&company_id=`$company_data.company_id`"|fn_url}' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="wednesday" id="datepairRange_wednesday">
                            <td>    
                                {__("ec_table_booking_system.wednesday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = $days_data.wednesday_status
                                    input_name = "company_data[booking_data][wednesday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_wednesday_timing" name="company_data[booking_data][wednesday_timing_start_time]" value="{$days_data.wednesday_timing_start_time}" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_wednesday_timing_end" name="company_data[booking_data][wednesday_timing_end_time]" value="{$days_data.wednesday_timing_end_time}"/>
                            </td>
                            <td>
                                <a  href='{"ec_table_booking_system.get_slots&day=wednesday&book_slot=`$company_data.booking_data.slot_time`&break_Slot=`$company_data.booking_data.free_time`&start_time=`$days_data.wednesday_timing_start_time`&end_time=`$days_data.wednesday_timing_end_time`&company_id=`$company_data.company_id`"|fn_url}' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="thursday" id="datepairRange_thursday">
                            <td>
                                {__("ec_table_booking_system.thursday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = $days_data.thursday_status
                                    input_name = "company_data[booking_data][thursday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_thursday_timing" name="company_data[booking_data][thursday_timing_start_time]" value="{$days_data.thursday_timing_start_time}" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_thursday_timing_end" name="company_data[booking_data][thursday_timing_end_time]" value="{$days_data.thursday_timing_end_time}"/>
                            </td>
                            <td>
                                <a  href='{"ec_table_booking_system.get_slots&day=thursday&book_slot=`$company_data.booking_data.slot_time`&break_Slot=`$company_data.booking_data.free_time`&start_time=`$days_data.thursday_timing_start_time`&end_time=`$days_data.thursday_timing_end_time`&company_id=`$company_data.company_id`"|fn_url}' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="friday" id="datepairRange_friday">
                            <td>
                                {__("ec_table_booking_system.friday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = $days_data.friday_status
                                    input_name = "company_data[booking_data][friday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_friday_timing" name="company_data[booking_data][friday_timing_start_time]" value="{$days_data.friday_timing_start_time}" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_friday_timing_end" name="company_data[booking_data][friday_timing_end_time]" value="{$days_data.friday_timing_end_time}"/>
                            </td>
                            <td>
                                <a  href='{"ec_table_booking_system.get_slots&day=friday&book_slot=`$company_data.booking_data.slot_time`&break_Slot=`$company_data.booking_data.free_time`&start_time=`$days_data.friday_timing_start_time`&end_time=`$days_data.friday_timing_end_time`&company_id=`$company_data.company_id`"|fn_url}' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="saturday" id="datepairRange_saturday">    
                            <td>
                                {__("ec_table_booking_system.saturday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = $days_data.saturday_status
                                    input_name = "company_data[booking_data][saturday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_saturday_timing" name="company_data[booking_data][saturday_timing_start_time]" value="{$days_data.saturday_timing_start_time}" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_saturday_timing_end" name="company_data[booking_data][saturday_timing_end_time]" value="{$days_data.saturday_timing_end_time}"/>
                            </td>
                            <td>
                                <a  href='{"ec_table_booking_system.get_slots&day=saturday&book_slot=`$company_data.booking_data.slot_time`&break_Slot=`$company_data.booking_data.free_time`&start_time=`$days_data.saturday_timing_start_time`&end_time=`$days_data.saturday_timing_end_time`&company_id=`$company_data.company_id`"|fn_url}' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                    {else}
                        <tr class="sunday" id="datepairRange_sunday">
                            <td>
                                {__("ec_table_booking_system.sunday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = ''
                                    input_name = "company_data[booking_data][sunday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_sunday_timing" name="company_data[booking_data][sunday_timing_start_time]" value="" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_sunday_timing_end" name="company_data[booking_data][sunday_timing_end_time]" value=""/>
                            </td>
                            <td>
                                <a  href='' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="monday" id="datepairRange_monday">
                            <td>
                                {__("ec_table_booking_system.monday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = ''
                                    input_name = "company_data[booking_data][monday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_monday_timing" name="company_data[booking_data][monday_timing_start_time]" value="" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_monday_timing_end" name="company_data[booking_data][monday_timing_end_time]" value=""/>
                            </td>
                            <td>
                                <a  href='' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="tuesday" id="datepairRange_tuesday">
                            <td>
                                {__("ec_table_booking_system.tuesday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = ''
                                    input_name = "company_data[booking_data][tuesday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_tuesday_timing" name="company_data[booking_data][tuesday_timing_start_time]" value="" />
                            </td>
                            <td>
                                 <input type="text" class="time end input-small" id="elm_tuesday_timing_end" name="company_data[booking_data][tuesday_timing_end_time]" value=""/>
                            </td>
                            <td>
                                <a  href='' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="wednesday" id="datepairRange_wednesday">
                            <td>    
                                {__("ec_table_booking_system.wednesday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = ''
                                    input_name = "company_data[booking_data][wednesday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_wednesday_timing" name="company_data[booking_data][wednesday_timing_start_time]" value="" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_wednesday_timing_end" name="company_data[booking_data][wednesday_timing_end_time]" value=""/>
                            </td>
                            <td>
                                <a  href='' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="thursday" id="datepairRange_thursday">
                            <td>
                                {__("ec_table_booking_system.thursday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = ''
                                    input_name = "company_data[booking_data][thursday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_thursday_timing" name="company_data[booking_data][thursday_timing_start_time]" value="" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_thursday_timing_end" name="company_data[booking_data][thursday_timing_end_time]" value=""/>
                            </td>
                            <td>
                                <a  href='' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="friday" id="datepairRange_friday">
                            <td>
                                {__("ec_table_booking_system.friday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = ''
                                    input_name = "company_data[booking_data][friday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_friday_timing" name="company_data[booking_data][friday_timing_start_time]" value="" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_friday_timing_end" name="company_data[booking_data][friday_timing_end_time]" value=""/>
                            </td>
                            <td>
                                 <a  href='' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                        <tr class="saturday" id="datepairRange_saturday">    
                            <td>
                                {__("ec_table_booking_system.saturday_timing")}
                            </td>
                            <td>
                                {include file="common/switcher.tpl"
                                    checked = ''
                                    input_name = "company_data[booking_data][saturday_status]"
                                    input_value = "1"
                                }
                            </td>
                            <td>
                                <input type="text" class="time start input-small" id="elm_saturday_timing" name="company_data[booking_data][saturday_timing_start_time]" value="" />
                            </td>
                            <td>
                                <input type="text" class="time end input-small" id="elm_saturday_timing_end" name="company_data[booking_data][saturday_timing_end_time]" value=""/>
                            </td>
                            <td>
                                <a  href='' id="opener_ec_seller_cart" class="cm-dialog-opener cm-post cm-ajax  btn btn-secondary ec-seller-second-sear-icon cm-dialog-destroy-on-close" rel="nofollow" data-ca-target-id="add_slots_amount" data-ca-target-form="location_update_form" data-ca-dialog-title="{__("ec_table_booking_system.booking_slots")}">{__("ec_table_booking_system.booking_slots")}</a>
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>
            {assign var=addons_setting value=fn_get_ec_table_booking_system_settings()}
            {if $addons_setting.time_format == '24'}
                <script>
                    (function (_, $) {
                        $('.open-close-radio').parent().after('<br>');
                        $('#datepairRange_sunday .time').timepicker({
                            'showDuration': true,
                            'timeFormat': 'H:i',
                            'show2400':true,
                            'step': '5'
                        });
                        var datepairRange_sundayEl = document.getElementById('datepairRange_sunday');
                        var timeOnlyDatepair_sunday = new Datepair(datepairRange_sundayEl);
                        $('#datepairRange_monday .time').timepicker({
                            'showDuration': true,
                           'timeFormat': 'H:i',
                            'show2400':true,
                            'step': '5'
                        });
                        var datepairRange_mondayEl = document.getElementById('datepairRange_monday');
                        var timeOnlyDatepair_monday = new Datepair(datepairRange_mondayEl);
                        $('#datepairRange_tuesday .time').timepicker({
                            'showDuration': true,
                           'timeFormat': 'H:i',
                            'show2400':true,
                            'step': '5'
                        });
                        var datepairRange_tuesdayEl = document.getElementById('datepairRange_tuesday');
                        var timeOnlyDatepair_tuesday = new Datepair(datepairRange_tuesdayEl);
                        $('#datepairRange_wednesday .time').timepicker({
                            'showDuration': true,
                           'timeFormat': 'H:i',
                            'show2400':true,
                            'step': '5'
                        });
                        var datepairRange_wednesdayEl = document.getElementById('datepairRange_wednesday');
                        var timeOnlyDatepair_wednesday = new Datepair(datepairRange_wednesdayEl);
                        $('#datepairRange_thursday .time').timepicker({
                            'showDuration': true,
                           'timeFormat': 'H:i',
                            'show2400':true,
                            'step': '5'
                        });
                        var datepairRange_thursdayEl = document.getElementById('datepairRange_thursday');
                        var timeOnlyDatepair_thursday = new Datepair(datepairRange_thursdayEl);
                        $('#datepairRange_friday .time').timepicker({
                            'showDuration': true,
                           'timeFormat': 'H:i',
                            'show2400':true,
                            'step': '5'
                        });
                        var datepairRange_fridayEl = document.getElementById('datepairRange_friday');
                        var timeOnlyDatepair_friday = new Datepair(datepairRange_fridayEl);
                        $('#datepairRange_saturday .time').timepicker({
                            'showDuration': true,
                           'timeFormat': 'H:i',
                            'show2400':true,
                            'step': '5'
                        });
                        var datepairRange_saturdayEl = document.getElementById('datepairRange_saturday');
                        var timeOnlyDatepair_saturday = new Datepair(datepairRange_saturdayEl);
                        $('.day-open').on('click',function(){
                            $(this).parent().siblings('.dateRange').show();
                        });
                        $('.day-close').on('click',function(){
                            $(this).parent().siblings('.dateRange').hide();
                        });
                        $('#datepairRange_apply_all .time').timepicker({
                            'showDuration': true,
                            'timeFormat': 'g:ia',
                            'step': '5'
                        });
                        var datepairRange_apply_allEl = document.getElementById('datepairRange_apply_all');
                        var timeOnlyDatepair_apply_all = new Datepair(datepairRange_apply_allEl);
                        $('#apply_to_alls').on('click',function(){
                            var selected = $("input[type='radio'][name='global_status']:checked");
                            if (selected.val() == 'open') {
                                $("input[value=open]").attr("checked", "checked");
                                $('.day-open').trigger('click');
                            }else{
                                $("input[value=close]").attr("checked", "checked");
                                $('.day-close').trigger('click');
                            }
                            // console.log($('.time.start').val());
                            $('.time.start').val($('#elm_apply_all_timing_table').val());
                            $('.time.end').val($('#elm_apply_all_timing_table_end').val());
                        });
                    }(Tygh, Tygh.$));
                </script>
            {else}
                <script>
                    (function (_, $) {
                        $('.open-close-radio').parent().after('<br>');
                        $('#datepairRange_sunday .time').timepicker({
                            'showDuration': true,
                            'timeFormat': 'g:ia',
                            'step': '5'
                        });
                        var datepairRange_sundayEl = document.getElementById('datepairRange_sunday');
                        var timeOnlyDatepair_sunday = new Datepair(datepairRange_sundayEl);
                        $('#datepairRange_monday .time').timepicker({
                            'showDuration': true,
                            'timeFormat': 'g:ia',
                            'step': '5'
                        });
                        var datepairRange_mondayEl = document.getElementById('datepairRange_monday');
                        var timeOnlyDatepair_monday = new Datepair(datepairRange_mondayEl);
                        $('#datepairRange_tuesday .time').timepicker({
                            'showDuration': true,
                            'timeFormat': 'g:ia',
                            'step': '5'
                        });
                        var datepairRange_tuesdayEl = document.getElementById('datepairRange_tuesday');
                        var timeOnlyDatepair_tuesday = new Datepair(datepairRange_tuesdayEl);
                        $('#datepairRange_wednesday .time').timepicker({
                            'showDuration': true,
                            'timeFormat': 'g:ia',
                            'step': '5'
                        });
                        var datepairRange_wednesdayEl = document.getElementById('datepairRange_wednesday');
                        var timeOnlyDatepair_wednesday = new Datepair(datepairRange_wednesdayEl);
                        $('#datepairRange_thursday .time').timepicker({
                            'showDuration': true,
                            'timeFormat': 'g:ia',
                            'step': '5'
                        });
                        var datepairRange_thursdayEl = document.getElementById('datepairRange_thursday');
                        var timeOnlyDatepair_thursday = new Datepair(datepairRange_thursdayEl);
                        $('#datepairRange_friday .time').timepicker({
                            'showDuration': true,
                            'timeFormat': 'g:ia',
                            'step': '5'
                        });
                        var datepairRange_fridayEl = document.getElementById('datepairRange_friday');
                        var timeOnlyDatepair_friday = new Datepair(datepairRange_fridayEl);
                        $('#datepairRange_saturday .time').timepicker({
                            'showDuration': true,
                            'timeFormat': 'g:ia',
                            'step': '5'
                        });
                        var datepairRange_saturdayEl = document.getElementById('datepairRange_saturday');
                        var timeOnlyDatepair_saturday = new Datepair(datepairRange_saturdayEl);
                        $('.day-open').on('click',function(){
                            $(this).parent().siblings('.dateRange').show();
                        });
                        $('.day-close').on('click',function(){
                            $(this).parent().siblings('.dateRange').hide();
                        });
                        $('#datepairRange_apply_all .time').timepicker({
                            'showDuration': true,
                            'timeFormat': 'g:ia',
                            'step': '5'
                        });
                        var datepairRange_apply_allEl = document.getElementById('datepairRange_apply_all');
                        var timeOnlyDatepair_apply_all = new Datepair(datepairRange_apply_allEl);
                        $('#apply_to_alls').on('click',function(){
                            var selected = $("input[type='radio'][name='global_status']:checked");
                            if (selected.val() == 'open') {
                                $("input[value=open]").attr("checked", "checked");
                                $('.day-open').trigger('click');
                            }else{
                                $("input[value=close]").attr("checked", "checked");
                                $('.day-close').trigger('click');
                            }
                            // console.log($('.time.start').val());
                            $('.time.start').val($('#elm_apply_all_timing_table').val());
                            $('.time.end').val($('#elm_apply_all_timing_table_end').val());
                        });
                    }(Tygh, Tygh.$));
                </script>
            {/if}