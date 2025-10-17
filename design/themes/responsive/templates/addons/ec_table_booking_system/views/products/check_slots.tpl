<div id="ec_timeslots_list">
    <input type="hidden" name="product_data[{$obj_id}][booking_info][booking_date]" value="{$selected_date}">
    <div class="ec_slot_qty_selector_div">
        {$ec_qty = 0}
        <select name="product_data[{$obj_id}][booking_info][booking_slot]" required class="ec_select_slots_list" data-ca-selected-date="{$selected_date}" data-ca-productid="{$obj_id}">
            <option value="">{__("ec_table_booking_system.ec_select_slot")}</option>
            {foreach from=$available_time_slots item=available_time_slot key=key}
                {if $available_time_slot.amount > 0}
                    {$val = "`$available_time_slot.0` - `$available_time_slot.1`"}
                    <option value="{$val}" {if $val  == $selected_time}selected="selected"{/if}>{$available_time_slot.0} - {$available_time_slot.1}</option>
                    {if $val  == $selected_time}
                        {$ec_qty = $available_time_slot.amount}
                    {/if}
                {/if}
            {/foreach}
        </select>
        {if $ec_qty > 0}
        <div class="ec_qty">
            <div id="ec_select_slot_qty">
                {if $check_quantity_selector == 'Y'}
                    <input type="hidden" name="product_data[{$obj_id}][booking_info][booking_slot_amount]" value="1" />
                {else}
                    <select name="product_data[{$obj_id}][booking_info][booking_slot_amount]" id="qty_count_{$obj_prefix}{$obj_id}" required>
                        <option value="">{__("ec_table_booking_system.ec_select_qty")}</option>
                        {for $qty=1 to $ec_qty}
                            <option value="{$qty}">{$qty}</option>
                        {/for}
                    </select>
                {/if}
            <!--ec_select_slot_qty--></div>
        </div>
        {/if}
    </div>
<!--ec_timeslots_list--></div>
