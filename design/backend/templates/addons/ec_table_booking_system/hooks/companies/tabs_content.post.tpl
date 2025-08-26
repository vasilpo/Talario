{if !empty($offer_booking) && $offer_booking}
    {if !"ULTIMATE"|fn_allowed_for}
        <div class="hidden" id="content_ec_table_booking_system">

            {script src="js/addons/ec_table_booking_system/datepair-lib/jquery.timepicker.js" class="cm-ajax-force"}
            {style src="addons/ec_table_booking_system/datepair-lib/jquery.timepicker.css"}
            {style src="addons/ec_table_booking_system/jquery-ui.multidatespickers.css"}
            {script src="js/addons/ec_table_booking_system/datepair-lib/jquery.datepair.js" class="cm-ajax-force"}
            {script src="js/addons/ec_table_booking_system/datepair-lib/datepair.js" class="cm-ajax-force"}
            {script src="js/addons/ec_table_booking_system/jquery-ui.multidatespicker.js" class="cm-ajax-force"}
            {script src="js/addons/ec_table_booking_system/backend_date_picker.js"}
            <style type="text/css">
                .open-close-radio {
                    display: inline;
                }
            </style>
        
            {if !empty($company_data.booking_data.booking_type) && $company_data.booking_data.booking_type != 'N' && !empty($smarty.request.company_id)}
                <div class="control-group">
                    <label for="elm_date_from" class="control-label ">{__("ec_table_booking_system.update_product")}:</label>
                    <div class="controls">
                        {include file="addons/ec_table_booking_system/pickers/products/picker.tpl" data_id="update_product" item_ids=$company_data.update_products input_name="company_data[update_products]" type="links" no_container=true picker_view=true cmcancel=false  company_id="{$smarty.request.company_id}" placement="left"}
                    </div>
                </div>
            {/if}
            <div class="control-group">
                <label for="elm_date_from" class="control-label ">{__("ec_table_booking_system.choose_booking")}:</label>
                <div class="controls">
                    <select name="company_data[booking_data][booking_type]" id="elm_booking_type">
                        <option value="N" {if !empty($company_data.booking_data.booking_type) && $company_data.booking_data.booking_type == 'N'}selected="selected"{/if}>{__("no")}</option>
                        <option value="T" {if !empty($company_data.booking_data.booking_type) && $company_data.booking_data.booking_type == 'T'}selected="selected"{/if}>{__("ec_table_booking.table_booking")}</option>
                        <option value="R" {if !empty($company_data.booking_data.booking_type) && $company_data.booking_data.booking_type == 'R'}selected="selected"{/if}>{__("ec_table_booking.rental_booking")}</option>
                    </select>
                </div>
            </div>
            <div id="booking_storing_container" class="{if !empty($company_data.booking_data.booking_type) && $company_data.booking_data.booking_type != 'T'}hidden{/if}">
                <div class="control-group">
                    <label class="control-label" for="ec_table_booking_quantity_selector">{__("ec_table_booking_system.hide_quantity_selector")}:</label>
                    <div class="controls">
                        <label class="checkbox">
                            <input type="hidden" name="company_data[booking_data][quantity_selector]" value="N" />
                            <input type="checkbox" name="company_data[booking_data][quantity_selector]" id="ec_table_booking_quantity_selector" value="Y"{if !empty($company_data.booking_data.quantity_selector) &&  $company_data.booking_data.quantity_selector == "Y"}checked="checked"{/if} />
                        </label>
                    </div>
                </div>
                <div class="bokking_date">
                    <div class="control-group">
                        <label for="elm_date_from" class="control-label cm-required">{__("ec_table_booking_system.date_from")}:</label>
                        <div class="controls">
                            {include file="common/calendar.tpl" date_id="elm_date_from" date_name="company_data[booking_data][from_date]" date_val=$company_data.booking_data.from_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="elm_date_to" class="control-label cm-required">{__("ec_table_booking_system.date_to")}:</label>
                        <div class="controls">
                            {include file="common/calendar.tpl" date_id="elm_date_to" date_name="company_data[booking_data][to_date]" date_val=$company_data.booking_data.to_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label cm-required" for="elm_book_time_bnr"><b>{__("ec_table_booking_system.book_time")}:</b></label>
                    <div class="controls">
                        <input type="text" class="input-micro cm-value-integer" name="company_data[booking_data][slot_time]" id="elm_book_time_bnr" value="{if !empty($company_data.booking_data.slot_time)}{$company_data.booking_data.slot_time}{else}0{/if}" />&nbsp;{__("minutes")}
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label cm-required" for="elm_break_time"><b>{__("ec_table_booking_system.break_time")}:</b></label>
                    <div class="controls">
                        <input type="text" class="input-micro cm-value-integer" name="company_data[booking_data][free_time]"  id="elm_break_time" value="{if !empty($company_data.booking_data.free_time)}{$company_data.booking_data.free_time}{else}0{/if}"/>&nbsp;{__("minutes")}
                    </div>
                </div>
                <p class="muted alert alert-warning center" >{__("ec_table_booking_system.note")} : {__("ec_table_booking_system.note_details")}</p>  
                <div id="elm_table_booking" class="">
                    {include file="addons/ec_table_booking_system/hooks/companies/components/booking_slots_table_booking.tpl"}
                </div>
            </div>
        
            <div id="booking_service_type" class="{if !empty($company_data.booking_data.booking_type) && $company_data.booking_data.booking_type != 'R'}hidden{/if}">
                <div class="bokking_date">
                    <div class="control-group">
                        <label for="elm_date_from_start" class="control-label cm-required">{__("ec_table_booking_system.date_from")}:</label>
                        <div class="controls">
                            {include file="common/calendar.tpl" date_id="elm_date_from_start" date_name="company_data[booking_data][R][from_date]" date_val=$company_data.booking_data.from_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="elm_date_to_end" class="control-label cm-required">{__("ec_table_booking_system.date_to")}:</label>
                        <div class="controls">
                            {include file="common/calendar.tpl" date_id="elm_date_to_end" date_name="company_data[booking_data][R][to_date]" date_val=$company_data.booking_data.to_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label for="elm_minimum_booking_time" class="control-label ">{__("ec_table_booking_system.minimum_booking_time")}:</label>
                    <div class="controls">
                        <input type="text" class="" id="elm_minimum_booking_time" name="company_data[booking_data][R][minimum_booking_time]" value="{$company_data.booking_data.minimum_booking_time|default:0}" /> {__("ec_table_booking.days")}
                    </div>
                </div>
            </div>
        
        
            <div class="control-group">
                <label class="control-label" for="ec_table_booking_show_price_date">{__("ec_table_booking_system.show_price_on_each_date")}:</label>
                <div class="controls">
                    <label class="checkbox">
                        <input type="hidden" name="company_data[booking_data][show_price_date]" value="N" />
                        <input type="checkbox" name="company_data[booking_data][show_price_date]" id="ec_table_booking_show_price_date" value="Y"{if !empty($company_data.booking_data.show_price_date) && $company_data.booking_data.show_price_date == "Y"}checked="checked"{/if} />
                    </label>
                </div>
            </div>
        
        
            <div class="control-group">
                <label class="control-label" for="elm_booking_site_booked_date">{__("ec_table_booking.block_date")}</label>
                <div style="width:17em" class="controls">
                    <input id="derbooking_data" name="company_data[booking_data][blocked_date]" value="" class="hidden"/>
                    <div id="elm_booking_site_booked_date"></div>
                </div>
            </div>
        
            <div class="ec_rental_date_wise_price">
                <label class="control-label">{__("ec_table_booking.price")} : </label>
                {include file="addons/ec_table_booking_system/hooks/companies/components/price_wise_date.tpl"}
            </div>
        </div>
        <style>
            .bokking_date {
                display: grid;
                grid-template-columns: repeat(4,1fr);
            }
            #ui-datepicker-div {
                z-index: 999 !important;
            }
        </style>

        <script>
            {if !empty($company_data.booking_data)}
            var blocked_data = '{$company_data.booking_data.blocked_date}';
            {/if}
                var a = blocked_data.split(",");
                var result = a.map(function (x) { 
                    return x; 
                });    
                if(result == 'NaN' || result == '') {
                    $('#elm_booking_site_booked_date')
                        .multiDatesPicker({
                        dateFormat: "dd/mm/yy",
                        onSelect: function(selectedDate) {
                            var dates = $(this).multiDatesPicker('getDates');
                            //var new_data = JSON.stringify(dates);
                            $('#derbooking_data').val(dates);
                        }
                    });
                }
                else {
                    $('#elm_booking_site_booked_date').multiDatesPicker({
                        dateFormat: "dd/mm/yy",
                        addDates: result,
                        onSelect: function(selectedDate) {
                            var dates = $(this).multiDatesPicker('getDates');
                            console.log(dates);
                            //var new_data = JSON.stringify(dates);
                            $('#derbooking_data').val(dates);
                        }
                    });
                    var dates = $('#elm_booking_site_booked_date').multiDatesPicker('getDates');
                    $('#derbooking_data').val(dates);
                }
        </script>
        <script>
        
            (function (_, $) {
                $('#elm_booking_type').on('change',function(){
                    var booking_type = $(this).val();
                    if (booking_type != 'N') {
                        if (booking_type == 'T') {
                            $('#booking_storing_container').show();
                            $('#booking_service_type').hide();
                        }else if(booking_type == 'R') {
                            $('#booking_storing_container').hide();
                            $('#booking_service_type').show();
                        }
                    }else{
                        $('#booking_storing_container').hide();
                        $('#booking_service_type').hide();
                    }
                });
            }(Tygh, Tygh.$));
        
        </script>
    {/if}
{/if}