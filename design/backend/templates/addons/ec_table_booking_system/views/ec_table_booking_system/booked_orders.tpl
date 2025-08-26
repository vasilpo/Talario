{capture name="mainbox"}
	<form action="{""|fn_url}" method="post" name="manage_booking_form" id="elm_booking">
		<input type="hidden" name="fake" value="1" />
		{include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}
		{assign var="return_current_url" value=$config.current_url|escape:url}
		{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
		{assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
		{assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
		{assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}
		{if $booking_info}
			<div class="table-responsive-wrapper">
				<table class="table table-middle table-responsive">
					<thead>
						<tr>
							<th>{__("ec_table_booking_system.order")}</th>
							<th>{__("ec_table_booking_system.customer_name")}</th>
							<th>{__("ec_table_booking_system.email")}</th>
							<th>{__("ec_table_booking_system.date")}</th>
							<th>{__("ec_table_booking_system.time_to_from")}</th>
							<th>{__("ec_table_booking_system.amount")}</th>
							<th class="right nowrap">{__("ec_table_booking_system.status")}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$booking_info item=booking_data}
							<tr class="cm-row-status-{$booking_data.status|lower}">
								<td data-th='{__("ec_table_booking_system.order")}'>
									{if fn_check_permissions('orders', 'update', 'admin')}
										<a href="{"orders.details?order_id=`$booking_data.order_id`"|fn_url}" class="underlined">{__("ec_table_booking_system.order")} #{$booking_data.order_id}</a>
									{else}
										#{$booking_data.order_id}
									{/if}
								</td>
								<td data-th="{__('ec_table_booking_system.customer_name')}">
									{if $booking_data.user_id && fn_check_permissions('profiles', 'update', 'admin')}
										<a href="{"profiles.update?user_id=`$booking_data.customer_information.user_id`"|fn_url}">
									{/if}
										{$booking_data.firstname}   {$booking_data.lastname}
									{if $booking_data.user_id && fn_check_permissions('profiles', 'update', 'admin')}
										</a>
									{/if}
								</td>
								<td data-th='{__("ec_table_booking_system.email")}'>
									{if $booking_data.email}<a href="mailto:{$booking_data.email|escape:url}">{$booking_data.email}</a> {/if}
								<td data-th='{__("ec_table_booking_system.date")}'>
									{$booking_data.booking_info.booking_date|date_format:"`$settings.Appearance.date_format`"}
								</td>
								<td data-th='{__("ec_table_booking_system.time_to_from")}'>
									{if $booking_data.booking_info.booking_type == 'R'}
										{$booking_data.booking_info.from} - {$booking_data.booking_info.to}
									{else}
										{$booking_data.booking_info.booking_slot}
									{/if}
								</td>
								<td data-th='{__("ec_table_booking_system.amount")}'>
									{if !empty($booking_data.booking_info.booking_slot_amount)}
										{$booking_data.booking_info.booking_slot_amount}
									{else}
										----
									{/if}
								</td>
								<td class="right nowrap" data-th='{__("ec_table_booking_system.status")}'>
									{if $booking_data.status == 'A' || $booking_data.status == 'D'}
										{include file="common/select_popup.tpl" id=$booking_data.id status=$booking_data.status object_id_name="id" hidden=true table="ec_table_booking_system_booking_info" extra="&order_id=`$booking_data.order_id`" items_status=$booking_items_status update_controller="ec_table_booking_system" hide_for_vendor=false}
									{/if}
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>				
        {else}
            <p class="no-items">{__("ec_table_booking_system.no_items")}</p>
        {/if}
		<div class="clearfix">
		    {include file="common/pagination.tpl" div_id=$smarty.request.content_id}
		</div>
	</form>
{/capture}
{capture name="sidebar"}
	{include file="addons/ec_table_booking_system/views/ec_table_booking_system/search_booked_order.tpl" dispatch="ec_table_booking_system.booked_orders"}
{/capture}
{capture name="adv_buttons"}
{/capture}
{include file="common/mainbox.tpl" title=__("ec_table_booking_system.booked_orders") content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra tools=$smarty.capture.tools        adv_buttons=$smarty.capture.adv_buttons  sidebar=$smarty.capture.sidebar buttons=$smarty.capture.buttons}