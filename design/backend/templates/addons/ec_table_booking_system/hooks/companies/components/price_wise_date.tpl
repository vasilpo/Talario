{if !empty($company_data.booking_data.price_wise)}
{assign var=price_wise  value=($company_data.booking_data.price_wise)}
{/if}
<div class="form-horizontal">
	<table class="table table-middle" width="100%">
		<thead class="cm-first-sibling">
			<tr>
				<th width="20%">{__("start_day")}</th>
				<th width="20%">{__("booking_reservation_amount")}({$currencies.$primary_currency.symbol nofilter})</th>
				<th width="15%">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			{if $price_wise}
				{foreach from=$price_wise  key="key" item="item"}
			        {assign var=id value=rand()}
					<tr class="cm-row-item" id="box_book_slot{$id}">
						<td>
                            {include file="addons/ec_table_booking_system/hooks/products/components/daterange_picker.tpl"
                            id="elm_price{$id}"
                            extra_class=""
                            data_url=""
                            result_ids=""
                            start_date=$item.from_date|default:$smarty.const.TIME
                            end_date=$item.to_date|default:$smarty.const.TIME
                            enable_data_event=true
                            start_date_value_name="company_data[booking_data][R][price_wise][{$id}][from_date]"
                            end_date_value_name="company_data[booking_data][R][price_wise][{$id}][to_date]"}
						</td>
						<td>
							<input type="text" class="input-small" name="company_data[booking_data][R][price_wise][{$id}][price]" value="{$item.price}" />
						</td>
						<td class="right">
							{include file="addons/ec_table_booking_system/buttons/multiple_buttons.tpl" item_id="book_slot$id" only_delete="Y"}
						</td>
					</tr>
				{/foreach}
			{/if}
			{assign var=id value=rand()}
			<tr class="cm-row-item" id="box_book_slot{$id}">
				<td>
                    {include file="addons/ec_table_booking_system/hooks/products/components/daterange_picker.tpl"
                        id="elm_price{$id}"
                        extra_class=""
                        data_url=""
                        result_ids=""
                        start_date=$leave_data.from_date|default:$smarty.const.TIME
                        end_date=$leave_data.to_date|default:$smarty.const.TIME
                        enable_data_event=true
                        start_date_value_name="company_data[booking_data][R][price_wise][{$id}][from_date]"
                        end_date_value_name="company_data[booking_data][R][price_wise][{$id}][to_date]"}
				</td>
				<td>
					<input type="text" class="input-small" name="company_data[booking_data][R][price_wise][{$id}][price]" value="" />
				</td>
				<td class="right">
					{include file="addons/ec_table_booking_system/buttons/multiple_buttons.tpl" item_id="book_slot$id"}
				</td>
			</tr>
		</tbody>
	</table>
</div>