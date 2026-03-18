<div class="control-group">
    <label class="control-label" for="stripe_connectivity">{__("stripe_connect.stripe_connect")}</label>
    <div class="controls">
        <input type="hidden" name="stripe_connectivity" id="stripe_connectivity">
        <select name="stripe_connectivity[]" id="stripe_connectivity" multiple>
            <option value="connected" {if "connected"|in_array:$search.stripe_connectivity}selected="selected"{/if}>{__("stripe_connect.connected")}</option>
            <option value="not_connected" {if "not_connected"|in_array:$search.stripe_connectivity}selected="selected"{/if}>{__("stripe_connect.not_connected")}</option>
        </select>
        <p class="muted description">{__("stripe_connect.stripe_connectivity.description")}</p>
    </div>
</div>
