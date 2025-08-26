{assign var=book_status value=fn_ec_table_booking_system_check_booking_order_status($order_info.order_id,$oi.product_id)}
<style type="text/css">
.dropleft .dropdown-menu {
    left: 0px;
    right: auto;
}
ul, ol {
    padding: 0;
    margin: 0px 0 0px 25px;
}
</style>
{assign var=booking_items_status value=fn_ec_table_booking_system_get_booking_status_params()}
{if "MULTIVENDOR"|fn_allowed_for}
    {assign var="notify_vendor" value=true}
{else}
    {assign var="notify_vendor" value=false}
{/if}
{if $book_status.status == 'A' || $book_status.status == 'D'}
    <div class="ty-control-group">
        <strong class="ty-control-group__label">{__('ec_table_booking_system.booking_status')}:</strong>
        <span class="ty-control-group__item">
            {include file="common/select_popup.tpl" id=$book_status.id status=$book_status.status object_id_name="id" table="ec_table_booking_system_booking_info" items_status=$booking_items_status extra="&order_id=`$order_info.order_id`" notify=true notify_department=true notify_vendor=$notify_vendor update_controller="ec_table_booking_system"}
        </span>
    </div>
{/if}
{if $oi.extra.booking_info.booking_type == "T" || $oi.extra.booking_info.booking_type == "R"}
    <div class="ty-control-group">
        <strong class="ty-control-group__label">{__('ec_table_booking_system.booking_info')}:</strong>
        <span class="ty-control-group__item">
            {if $oi.extra.booking_info.booking_type == "T"}
                <br>
                {__('ec_table_booking_system.booking_date')}: {$oi.extra.booking_info.booking_date|date_format:"`$settings.Appearance.date_format`"}
                <br>
                {__('ec_table_booking_system.booking_slot')}: {$oi.extra.booking_info.booking_slot}
                <br>
                {__('ec_table_booking_system.booking_table_amount')}: {$oi.extra.booking_info.booking_slot_amount}
            {else}
                {__('ec_table_booking_system.booking_date')}: {$oi.extra.booking_info.from} - {$oi.extra.booking_info.to}
            {/if}
        </span>
    </div>
{/if}