{if fn_ec_table_booking_system_check_if_booking_product($product.product_id) && ($product.booking_type == 'T' || $product.booking_type == 'R')}
	{if $details_page}
		{if $quick_view}
			{include file="buttons/button.tpl" but_id="button_cart_`$obj_prefix``$obj_id`" but_text=__("ec_table_booking_system.select_booking") but_href="products.view?product_id=`$product.product_id`" but_role=$opt_but_role but_name="" but_meta="ty-btn__primary ty-btn__big"}
		{else}
			{include file="buttons/add_to_cart.tpl" but_id="button_cart_`$obj_prefix``$obj_id`" but_name="dispatch[checkout.add..`$obj_id`]" but_role=$but_role block_width=$block_width obj_id=$obj_id product=$product but_meta=$add_to_cart_meta but_text=__('ec_table_booking_system.add_booking')}
		{/if}
	{else}
		{include file="buttons/button.tpl" but_id="button_cart_`$obj_prefix``$obj_id`" but_text=__("ec_table_booking_system.select_booking") but_href="products.view?product_id=`$product.product_id`" but_role=$opt_but_role but_name="" but_meta="ty-btn__primary ty-btn__big"}
	{/if}
{/if}