{if !empty($offer_booking) && $offer_booking}
{if !"ULTIMATE"|fn_allowed_for}
    {script src="js/addons/ec_table_booking_system/jquery-ui.multidatespicker.js" class="cm-ajax-force"}
    {style src="addons/ec_table_booking_system/jquery-ui.multidatespickers.css"}
    {include file="common/subheader.tpl" title=__("ec_table_booking_system.booing_company") target="#acc_addon_booking_system"}
    <div id="acc_addon_booking_system" class="collapsed in">
        <div class="control-group">
            <label class="control-label" for="elm_all_booking_site_booked_date">{__("ec_table_booking.block_date")}:</label>
            <div style="width:17em" class="controls">
                <input id="alllderbooking_data" name="company_data[blocked_date]" value="" class="hidden"/>
                <div id="elm_all_booking_site_booked_date"></div>
            </div>
        </div>
    </div>
    <script>
        var blocked_data = '{$company_data.blocked_date}';
            var a = blocked_data.split(",");
            var result = a.map(function (x) { 
                return x; 
            });

            console.log(result);

            if(result == 'NaN' || result == '') {
                $('#elm_all_booking_site_booked_date')
                    .multiDatesPicker({
                    dateFormat: "dd/mm/yy",
                    onSelect: function(selectedDate) {
                        var dates = $(this).multiDatesPicker('getDates');
                        //var new_data = JSON.stringify(dates);
                        $('#alllderbooking_data').val(dates);
                    }
                });
            }
            else {
                $('#elm_all_booking_site_booked_date').multiDatesPicker({
                    dateFormat: "dd/mm/yy",
                    addDates: result,
                    onSelect: function(selectedDate) {
                        var dates = $(this).multiDatesPicker('getDates');
                        console.log(dates);
                        var new_data = JSON.stringify(dates);
                        $('#alllderbooking_data').val(dates);
                    }
                });
            }

    </script>
{/if}
{/if}