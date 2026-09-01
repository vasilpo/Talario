{style src="addons/talario_vendor_cabinet/styles.less"}
{if $runtime.company_id && ($runtime.controller == "ec_table_booking_system" && $runtime.mode == "booked_orders")}
    {include file="addons/talario_vendor_cabinet/components/cabinet_style.tpl"}
{/if}
