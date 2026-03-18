<div class="control-group">
    <label class="control-label" for="ppcp_connectivity">{__("paypal_commerce_platform.ppcp_multiparty")}</label>
    <div class="controls">
        <input type="hidden" name="ppcp_connectivity" id="ppcp_connectivity">
        <select name="ppcp_connectivity[]" id="ppcp_connectivity" multiple>
            <option value="connected" {if "connected"|in_array:$search.ppcp_connectivity}selected="selected"{/if}>{__("paypal_commerce_platform.connected")}</option>
            <option value="not_connected" {if "not_connected"|in_array:$search.ppcp_connectivity}selected="selected"{/if}>{__("paypal_commerce_platform.not_connected")}</option>
        </select>
        <p class="muted description">{__("paypal_commerce_platform.ppcp_connectivity.description")}</p>
    </div>
</div>
