{if isset($order_info.payment_method.processor_params.is_paypal_checkout)
    && $order_info.payment_method.processor_params.is_paypal_checkout === "YesNo::YES"|enum}
    <div class="cm-toggle-button">
        <div class="control-group select-field">
            <div class="controls">
                <label for="paypal_checkout_send_shipment_info" class="checkbox">
                    <input type="checkbox"
                           id="paypal_checkout_send_shipment_info"
                           name="shipment_data[paypal_checkout_send_shipment_info]"
                           value="{"YesNo::YES"|enum}"
                           checked
                    />
                    {__("paypal_checkout.send_shipment_info")}
                </label>
            </div>
        </div>
    </div>
{/if}
