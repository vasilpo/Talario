{if ($product.booking_type == 'T'  || $product.booking_type == 'R' ) && $details_page && $offer_booking}
    {capture name="add_to_cart_`$obj_id`"} 
        <style>
            .ty-edp-description{
                display: none;
            }
            @media(min-width:767px){
                .ty-product-bigpicture .ty-product-bigpicture__right{
                    margin-left: -420px;
                    width: 400px;
                }
            }
            @media(min-width:767px){
                .ty-product-bigpicture .ty-product-bigpicture__sidebar-bottom{
                    width: 400px;
                    box-sizing: border-box;
                }
            }

            .ut2-pb.sticky_add_to_cart .ut2-pb__button{
                display: none !important;
            }
        </style>

        {assign var=time_from value=$product.from_date}
        {assign var=time_to value=$product.to_date}
        {assign var=min_date value=$product.from_date}
        {assign var="min_date_format" value={$product.from_date|date_format:"%d/%m/%Y"}}
        {assign var="min_date_format_hyp" value={$product.from_date|date_format:"%Y-%m-%d"}}
        {assign var="max_date_format" value={$product.to_date|date_format:"%d/%m/%Y"}}
        {assign var="max_date_format_hyp" value={$product.to_date|date_format:"%Y-%m-%d"}}
        {if $smarty.const.TIME > $min_date}
            {assign var="min_date_format" value=0}
            {assign var="min_date" value=$smarty.const.TIME}
        {/if}
        {assign var=max_date value=$product.to_date}
        {if $smarty.const.TIME > $max_date}
            {assign var="max_date" value=$smarty.const.TIME}
        {/if}

        <div class="cm-reload-{$product.product_id}" id="booking_update_{$product.product_id}">
            {if $product.booking_type == 'T'}
                {assign var=disable_date value=fn_ec_table_booking_system_get_all_open_days($product.days_data)}
                {assign var=addons_setting value=fn_get_ec_table_booking_system_settings()}
                {assign var=special_price  value=fn_ec_get_special_booking_price_by_product_id($product.product_id)}
                <input type="hidden" class="booking_price_data" value='{$special_price|json_encode nofilter}' />
                {if isset($product.company_id) && !empty($product.company_id)}
                    {assign var=blocked_date value=fn_ec_table_booking_get_blocked_date($product.company_id,$product.blocked_date)}
                {else}
                    {assign var=blocked_date   value=$product.blocked_date}
                {/if}
                <div class="ec_datepickerd" style="max-width:340px;margin-top:4px;">
                    <input type="hidden" name="product_data[{$product.product_id}][booking_info][booking_type]" value="{$product.booking_type}">
                    <div class="ec_datepickerd" style="max-width:800px;margin-top:4px;width:100%;">
                        {include file="addons/ec_table_booking_system/views/products/hour_rental_booking.tpl"}
                        <input type="hidden" name="product_data[{$product.product_id}][booking_info][booking_date]" id="date-range22"  value="">
                        <div id="date-range22-container" ></div>
                    </div>



                    <div class="ec_header_text ty-control-group__label">{__("ec_table_booking_system.ec_selectslot_qty_header")}</div>
                    <div id="ec_timeslots_list">
                        <div class="ec_slot_qty_selector_div">
                            <select name="product_data[{$obj_id}][booking_info][booking_slot]" class="" required>
                                <option value="">{__("ec_table_booking_system.ec_select_slot")}</option>
                            </select>
                        </div>
                    <!--ec_timeslots_list--></div>
                    </div>
            {elseif $product.booking_type == 'R'}
                {if isset($product.company_id) && !empty($product.company_id)}
                    {assign var=blocked_date value=fn_ec_table_booking_get_blocked_date($product.company_id,$product.blocked_date)}
                {else}
                    {assign var=blocked_date   value=$product.blocked_date}
                {/if}
                <input type="hidden" name="product_data[{$product.product_id}][booking_info][booking_type]" value="{$product.booking_type}">
                <div class="ec_datepickerd" style="max-width:340px;margin-top:4px;">
                    {include file="addons/ec_table_booking_system/views/products/new_rental_booking.tpl"}
                    <input type="hidden" name="product_data[{$product.product_id}][booking_info][booking_date]" id="date-range22"  value="">
                    <div id="date-range22-container" ></div>
                </div>
            {/if}
        </div>
        {assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
        {$smarty.capture.$add_to_cart nofilter}
    {/capture}
{/if}

