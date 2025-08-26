{if $order_info.products.$key.extra.booking_info.booking_type == "T" || $order_info.products.$key.extra.booking_info.booking_type == "R"}
    <div class="ty-control-group">
        <strong class="ty-control-group__label">{__('ec_table_booking_system.booking_info')}:</strong>
        <span class="ty-control-group__item">
            {if $order_info.products.$key.extra.booking_info.booking_type == "T"}
                {__('ec_table_booking_system.booking_date')}: {$order_info.products.$key.extra.booking_info.booking_date|date_format:"`$settings.Appearance.date_format`"}
                <br>
                {__('ec_table_booking_system.booking_slot')}: {$order_info.products.$key.extra.booking_info.booking_slot}
                <br>
                {__('ec_table_booking_system.booking_table_amount')}: {$order_info.products.$key.extra.booking_info.booking_slot_amount}
            {else}
                {__('ec_table_booking_system.booking_date')}: {$order_info.products.$key.extra.booking_info.from} - {$order_info.products.$key.extra.booking_info.to}
            {/if}
        </span>
    </div>
{/if}
{assign var=book_status value=fn_ec_table_booking_system_check_booking_order_status($order_info.order_id,$order_info.products.$key.product_id)}
{assign var=booking_items_status value=fn_ec_table_booking_system_get_booking_status_params()}
{if $book_status.status == 'A' || $book_status.status == 'D'}
    <div class="ty-control-group">
        <strong class="ty-control-group__label">{__('ec_table_booking_system.booking_status')}:</strong>
        <span class="ty-control-group__item">
            {$booking_items_status.{$book_status.status}}
        </span>
    </div>
{/if}