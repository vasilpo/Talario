
{script src="js/lib/daterangepicker/moment.min.js"}
{* {script src="js/addons/ec_table_booking_system/new_date_range_picker/dist/jquery.daterangepicker.min.js"} *}
{script src="js/addons/ec_table_booking_system/new_date_range_picker/src/jquery.daterangepicker.js"}
{assign var=special_price  value=fn_ec_get_special_booking_price_by_product_id($product.product_id)}
{assign var=product_price  value=fn_format_price_by_currency($product.price,$currencies.$primary_currency.currency_code,$currencies.$secondary_currency.currency_code)}
{assign var=addons_setting value=fn_get_ec_table_booking_system_settings()}
{assign var=disabled_dates value=$product.product_id|fn_ec_table_booking_system_get_already_booked_slot}
{assign var=blocked_date  value=fn_ec_table_booking_get_blocked_date_product_id($blocked_date)}



<script>

    $.dateRangePickerLanguages['custom'] =
	{
		'selected': 'Choosed:',
		'days': 'Days',
		'apply': 'Close',
		'week-1' : 'Mon',
		'week-2' : 'Tue',
		'week-3' : 'Wed',
		'week-4' : 'Thu',
		'week-5' : 'Fri',
		'week-6' : 'Sat',
		'week-7' : 'Sun',
		'month-name': ['January','February','March','April','May','June','July','August','September','October','November','December'],
		'shortcuts' : 'Shortcuts',
		'past': 'Past',
		'7days' : '7days',
		'14days' : '14days',
		'30days' : '30days',
		'previous' : 'Previous',
		'prev-week' : 'Week',
		'prev-month' : 'Month',
		'prev-quarter' : 'Quarter',
		'prev-year' : 'Year',
		'less-than' : 'Date range should longer than %d days',
		'more-than' : 'Date range should less than %d days',
		'default-more' : 'Please select a date range longer than %d days',
		'default-less' : 'Please select a date range less than %d days',
		'default-range' : 'Please select a date range between %d and %d days',
		'default-default': 'This is costom language'
	};



    var special_price  = {$special_price|json_encode nofilter};
    var product_price = {$product_price};
    var minimum_time = '{$product.minimum_booking_time}';
    var product_id = '{$product.product_id}';
    var week_start = '{$addons_setting.week_start}';
    var show_price_date = '{$product.show_price_date}';
    var currency_symbol = '{$currencies.$secondary_currency.symbol}';

    var current_language = '{$smarty.const.CART_LANGUAGE}';
    if(current_language == 'en') {
        current_language  =  'default';
    }

    var start_date = new Date("{$min_date_format_hyp}");
	var end_date = new Date("{$max_date_format_hyp}T24:00:00Z");

    var current_date = new Date();
    var arrDisabledDates = {};
    var arrreservedDates = {};

    {foreach from=$disabled_dates.formated_dates item="d_dates"}
		dt  = new Date('{$d_dates}');
        $time = new Date(dt.toLocaleDateString('en-US')).getTime();
        arrreservedDates[$time]= new Date('{$d_dates}');
	{/foreach}

	{foreach from=$blocked_date item="d_dates"}
		dt  = new Date('{$d_dates}T00:00:00');
        $time = new Date(dt.toLocaleDateString('en-US')).getTime();
        arrDisabledDates[$time]= new Date('{$d_dates}');
	{/foreach}

    $('#date-range22').dateRangePicker({
        inline:true,
        language:current_language,
        startOfWeek:week_start,
		container: '#date-range22-container',
        alwaysOpen:true,
        minDays:minimum_time,
        singleMonth: true,
        format: "DD/MM/YYYY",
		showDateFilter: function(time, date)
        {        
            var price = product_price;
            var new_date = new Date(time);
            new_date = new Date(new_date.toLocaleDateString('en-US')).getTime();
            $.each(special_price, function (key,val) {
                var from_date = (val.from_date)*1000;
                var to_date = (val.to_date)*1000;
                if(from_date <= new_date && to_date >= new_date) {
                    price = val.price;
                }
            });  
            if(show_price_date == 'Y') {
                return '<div style="padding:0 5px;">\
                            <span style="font-weight:bold">'+date+'</span>\
                            <div style="opacity:0.3;font-size:11px;">'+currency_symbol+price+'</div>\
                        </div>';
            }
            else {
                return '<div style="padding:0 5px;">\
                    <span style="font-weight:bold">'+date+'</span>\
                </div>';
            }
		},
        beforeShowDay: function(dt) {
            var bDisable = arrDisabledDates[new Date(dt.toLocaleDateString('en-US')).getTime()];
            var breserved = arrreservedDates[new Date(dt.toLocaleDateString('en-US')).getTime()];
            if(bDisable) {
                return [false, 'unavailable', ''];
            }
            else if(breserved) {
                return [false, 'reserved', ''];
            }
            else if (!(dt.getTime() >= start_date.getTime() && dt.getTime() <= end_date.getTime() && dt.getTime() >= current_date.getTime())){
                return [false, 'unavailable', ''];
            }
            else{
                return [true, 'free_date', ''];  
            }
        },
	});

    $('#date-range22').bind("datepicker-change",function(event,data){
        var form_data = $(this).closest('form').serialize();
        $.ceAjax('request', fn_url('products.set_price'), {
            result_ids: 'price_update_*,old_price_update_*,line_discount_update_*',
            data:{
                selected_date:data.value,
                product_id:product_id,
                form_data:form_data,
                booking_type:"H",
            },
            method: 'get',
            full_render: true,
            caching: false,
        });
    });
</script>
{if $product.show_price_date != 'Y'}
    <style>
        .date-picker-wrapper .month-wrapper table .day {
            padding: 14px 8px 14px 12px !important;
            border-radius: 2px !important;
        }
        .date-picker-wrapper .month-wrapper {
            width: 350px !important;
        }
        .date-picker-wrapper .month-wrapper table {
            max-width: 360px !important;
        }
    </style>
{/if}

{capture name="styles"}
    .free_date {
        background-color:{$addons_setting.free}
    }
    .unavailable {
        background-color:{$addons_setting.unavailable} !important
    }
    .reserved {
        background-color:{$addons_setting.reserved} !important
    }
{/capture}
{style content=$smarty.capture.styles type="less"}