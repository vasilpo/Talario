{if $cart.products.$key.extra.booking_info.booking_type == "T" || $cart.products.$key.extra.booking_info.booking_type == "R"}
	<div class="ty-control-group">
		<strong class="ty-control-group__label">{__('ec_table_booking_system.booking_info')}:</strong>
		<span class="ty-control-group__item">
			{if $cart.products.$key.extra.booking_info.booking_type == "T"}
                {__('ec_table_booking_system.booking_date')}: {$cart.products.$key.extra.booking_info.booking_date|date_format:"`$settings.Appearance.date_format`"}
                <br>
                {__('ec_table_booking_system.booking_slot')}: {$cart.products.$key.extra.booking_info.booking_slot}
                <br>
                {__('ec_table_booking_system.booking_table_amount')}: {$cart.products.$key.extra.booking_info.booking_slot_amount}
            {else}
                {__('ec_table_booking_system.booking_date')}: {$cart.products.$key.extra.booking_info.from} - {$cart.products.$key.extra.booking_info.to}
            {/if}
		</span>
	</div>
{/if}