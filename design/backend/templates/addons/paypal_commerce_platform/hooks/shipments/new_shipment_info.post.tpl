{if isset($order_info.payment_method.processor_params.is_paypal_commerce_platform)
    && $order_info.payment_method.processor_params.is_paypal_commerce_platform === "YesNo::YES"|enum}
    <div class="cm-toggle-button">
        <div class="control-group select-field">
            <div class="controls">
                <label for="paypal_commerce_platform_send_shipment_info" class="checkbox">
                    <input type="checkbox"
                           id="paypal_commerce_platform_send_shipment_info"
                           name="shipment_data[paypal_commerce_platform_send_shipment_info]"
                           value="{"YesNo::YES"|enum}"
                           checked
                    />
                    {__("paypal_commerce_platform.send_shipment_info")}
                </label>
            </div>
        </div>
    </div>
{/if}
