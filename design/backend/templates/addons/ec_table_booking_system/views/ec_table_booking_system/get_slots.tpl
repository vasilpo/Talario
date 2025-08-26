<div class="hidden" title="{__("add_location")}" id="add_slots_amount">
    <form id='form' action="{""|fn_url}" method="post" name="add_slots_form_data" class="form-horizontal form-edit cm-disable-empty-files" enctype="multipart/form-data">
        <input type="hidden" name="day" id="booking_day" value="{$day}" />
        {if !empty($product_id)}
        <input type="hidden" name="product_id" id="product_id" value="{$product_id}" />
        {else}
        <input type="hidden" name="company_id" id="company_id" value="{$company_id}" />
        {/if}

        <div class="apply_quantity_all">
            <div class="control-group">
                <label class="control-label">{__("ec_booking_quantity")} :</label>
                <div class="controls">
                    <input type="text" class="input-mini cm-value-integer ec_booking_input_apply" name="apply_all"  value="">
                    
                    <a id="ec_booking_apply_all" class="btn btn-primary">{__("ec_booking.apply_all")}</a>
                </div>
            </div>
        </div>
        <div class="division_box_container">
            <table class="table table-middle">
                <thead class="cm-first-sibling">
                    <tr>
                        <th width="20%">{__("ec_table_booking_system.start_day")}</th>
                        <th width="20%">{__("ec_table_booking_system.end_day")}</th>
                        <th width="20%">{__("ec_table_booking_system.booking_amount")}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $available_slots}
                        {foreach from=$available_slots item="item" key="key"}
                            <tr>
                                <td>
                                    {$item.0}
                                </td>
                                <td>
                                    {$item.1}
                                </td>
                                <td>
                                    <input type="hidden" name="booking_data[{$key}][start_time]" value="{$item.0}"/>
                                    <input type="hidden" name="booking_data[{$key}][end_time]" value="{$item.1}"/>
                                    <input type="number" min="0" name="booking_data[{$key}][amount]" class="ec_specific_quantity" value="{if isset($saved_data[$key])}{$saved_data[$key]['amount']}{else}0{/if}"/>
                                </td>
                            </tr>
                        {/foreach}
                    {/if}
                </tbody>
            </table>
        </div>
        <div class="buttons-container">
            <a data-ca-dispatch="dispatch[ec_table_booking_system.update_slots_amount]"  data-ca-target-form="add_slots_form_data"  class="btn btn-primary  cm-ajax cm-submit cm-dialog-closer">{__('save')}</a>
        </div>
    </form>
<script>
    (function (_, $) {
        $(document).on('click','#ec_booking_apply_all',function(){
            var quantity = $('.ec_booking_input_apply').val();
            $('.ec_specific_quantity').val(quantity);
        });
    }(Tygh, Tygh.$));

</script>
<!--add_slots_amount--></div>