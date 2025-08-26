{if isset($order_info.payment_method.processor_params.is_paypal_commerce_platform)
    && $order_info.payment_method.processor_params.is_paypal_commerce_platform === "YesNo::YES"|enum}
    <div class="control-group">
        <div class="control-label">{__("paypal_commerce_platform.send_shipment_info")}</div>
        <div class="controls">
            <input type="checkbox"
                   id="paypal_commerce_platform_send_shipment_info_{$shipping.group_key}_{$shipment_id}"
                   name="update_shipping[{$shipping.group_key}][{$shipment_id}][paypal_commerce_platform_send_shipment_info]"
                   value="{"YesNo::YES"|enum}"
                   checked
            />
        </div>
    </div>
{/if}
