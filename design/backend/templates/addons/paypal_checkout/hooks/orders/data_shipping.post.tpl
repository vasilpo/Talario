{if isset($order_info.payment_method.processor_params.is_paypal_checkout)
    && $order_info.payment_method.processor_params.is_paypal_checkout === "YesNo::YES"|enum}
    <div class="control-group">
        <div class="control-label">{__("paypal_checkout.send_shipment_info")}</div>
        <div class="controls">
            <input type="checkbox"
                   id="paypal_checkout_send_shipment_info_{$shipping.group_key}_{$shipment.shipment_id}"
                   name="update_shipping[{$shipping.group_key}][{$shipment.shipment_id}][paypal_checkout_send_shipment_info]"
                   value="{"YesNo::YES"|enum}"
                   checked
            />
        </div>
    </div>
{/if}
