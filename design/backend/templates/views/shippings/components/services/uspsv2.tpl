{$usps_schema = fn_get_schema('shipping_services', 'usps')}
{$usps_settings = $usps_schema.settings}

<fieldset>

{include file="common/subheader.tpl" title=__("general_info")}

<div class="control-group">
    <label class="control-label" for="ship_usps_client_id">{__("ship_usps_client_id")}</label>
    <div class="controls">
    <input id="ship_usps_client_id" type="text" name="shipping_data[service_params][client_id]" size="30" value="{$shipping.service_params.client_id}" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="ship_usps_client_secret">{__("ship_usps_client_secret")}</label>
    <div class="controls">
        <input id="ship_usps_client_secret" type="text" name="shipping_data[service_params][client_secret]" size="30" value="{$shipping.service_params.client_secret}" />
        <div class="well well-small help-block">
            {__("tools_carrier_usps_msg", ["[url]" => "https://developers.usps.com/"])}
        </div>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="test_mode">{__("test_mode")}</label>
    <div class="controls">
    <input type="hidden" name="shipping_data[service_params][test_mode]" value="N" />
    <input id="test_mode" type="checkbox" name="shipping_data[service_params][test_mode]" value="Y" {if $shipping.service_params.test_mode === "Y"}checked="checked"{/if}/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ship_usps_price_type">{__("ship_usps_price_type")}</label>
    <div class="controls">
        <select id="ship_usps_price_type" name="shipping_data[service_params][price_type]">
            {foreach $usps_settings.price_types as $usps_price_type}
                <option value="{$usps_price_type}" {if $shipping.service_params.price_type === $usps_price_type}selected="selected"{/if}>{$usps_price_type}</option>
            {/foreach}
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ship_usps_package_weight">{__("ship_usps_package_weight")}</label>
    <div class="controls">
        <input id="ship_usps_package_weight" type="text" name="shipping_data[service_params][package_weight]" size="30" value="{$shipping.service_params.package_weight}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ship_usps_package_width">{__("ship_usps_package_width")}</label>
    <div class="controls">
        <input id="ship_usps_package_width" type="text" name="shipping_data[service_params][package_width]" size="30" value="{$shipping.service_params.package_width}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ship_usps_package_length">{__("ship_usps_package_length")}</label>
    <div class="controls">
        <input id="ship_usps_package_length" type="text" name="shipping_data[service_params][package_length]" size="30" value="{$shipping.service_params.package_length}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ship_usps_package_height">{__("ship_usps_package_height")}</label>
    <div class="controls">
        <input id="ship_usps_package_height" type="text" name="shipping_data[service_params][package_height]" size="30" value="{$shipping.service_params.package_height}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ship_usps_package_girth">{__("ship_usps_package_girth")}</label>
    <div class="controls">
        <input id="ship_usps_package_girth" type="text" name="shipping_data[service_params][package_girth]" size="30" value="{$shipping.service_params.package_girth}" />
    </div>
    <div class="controls">
        <p class="muted description">{__("ship_usps_girth_description")}</p>
    </div>
</div>

{if !$code|in_array:$usps_settings.intl_mail_classes}
    <div class="control-group">
        <label class="control-label" for="ship_usps_processing_category">{__("ship_usps_processing_category")}</label>
        <div class="controls">
            <select id="ship_usps_processing_category" name="shipping_data[service_params][processing_category]">
                {foreach $usps_settings.processing_categories as $usps_processing_category}
                    <option value="{$usps_processing_category}" {if $shipping.service_params.processing_category === $usps_processing_category}selected="selected"{/if}>{$usps_processing_category}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="ship_usps_rate_indicator">{__("ship_usps_rate_indicator")}</label>
        <div class="controls">
            <select id="ship_usps_rate_indicator" name="shipping_data[service_params][rate_indicator]">
                {foreach $usps_settings.domestic_rate_indicators as $usps_domestic_rate_indicator}
                    <option value="{$usps_domestic_rate_indicator}" {if $shipping.service_params.rate_indicator === $usps_domestic_rate_indicator}selected="selected"{/if}>{__("ship_usps_rate_indicator_{$usps_domestic_rate_indicator|lower}")}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="ship_usps_destination_entry_type">{__("ship_usps_destination_entry_type")}</label>
        <div class="controls">
            <select id="ship_usps_destination_entry_type" name="shipping_data[service_params][destination_entry_type]">
                {foreach $usps_settings.domestic_destination_entry_facility_types as $domestic_destination_entry_facility_type}
                    <option value="{$domestic_destination_entry_facility_type}" {if $shipping.service_params.destination_entry_type === $domestic_destination_entry_facility_type}selected="selected"{/if}>{__("ship_usps_entry_type_{$domestic_destination_entry_facility_type|lower}")}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">{__("extra_services")}</label>
        <div class="table-filters controls">
            <div class="scroll-y">
                {foreach $usps_settings.domestic_extra_services as $usps_domestic_extra_service}
                    <div class="select-field">
                        <input type="hidden" name="shipping_data[service_params][domestic_service_{$usps_domestic_extra_service}]" value="N" />
                        <label class="checkbox" for="domestic_service_{$usps_domestic_extra_service}">
                            <input type="checkbox" {if $shipping.service_params["domestic_service_$usps_domestic_extra_service"] === "Y"}checked="checked"{/if} value="Y" name="shipping_data[service_params][domestic_service_{$usps_domestic_extra_service}]" id="domestic_service_{$usps_domestic_extra_service}">{__("ship_usps_service_{$usps_domestic_extra_service}")}</label>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>

{else}
    <div class="control-group">
        <label class="control-label" for="ship_usps_intl_processing_category">{__("ship_usps_processing_category")}</label>
        <div class="controls">
            <select id="ship_usps_intl_processing_category" name="shipping_data[service_params][intl_processing_category]">
                {foreach $usps_settings.processing_categories as $usps_processing_category}
                    <option value="{$usps_processing_category}" {if $shipping.service_params.intl_processing_category === $usps_processing_category}selected="selected"{/if}>{$usps_processing_category}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="ship_usps_intl_rate_indicator">{__("ship_usps_rate_indicator")}</label>
        <div class="controls">
            <select id="ship_usps_intl_rate_indicator" name="shipping_data[service_params][intl_rate_indicator]">
                {foreach $usps_settings.intl_rate_indicators as $usps_intl_rate_indicator}
                    <option value="{$usps_intl_rate_indicator}" {if $shipping.service_params.intl_rate_indicator === $usps_intl_rate_indicator}selected="selected"{/if}>{__("ship_usps_rate_indicator_{$usps_intl_rate_indicator|lower}")}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="ship_usps_intl_destination_entry_type">{__("ship_usps_destination_entry_type")}</label>
        <div class="controls">
            <select id="ship_usps_intl_destination_entry_type" name="shipping_data[service_params][intl_destination_entry_type]">
                {foreach $usps_settings.intl_destination_entry_facility_types as $intl_destination_entry_facility_type}
                    <option value="{$intl_destination_entry_facility_type}" {if $shipping.service_params.intl_destination_entry_type === $intl_destination_entry_facility_type}selected="selected"{/if}>{__("ship_usps_entry_type_{$intl_destination_entry_facility_type|lower}")}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">{__("extra_services")}</label>
        <div class="table-filters controls">
            <div class="scroll-y">
                {foreach $usps_settings.intl_extra_services as $usps_intl_extra_service}
                    <div class="select-field">
                        <input type="hidden" name="shipping_data[service_params][intl_service_{$usps_intl_extra_service}]" value="N" />
                        <label class="checkbox" for="intl_service_{$usps_intl_extra_service}">
                        <input type="checkbox" {if $shipping.service_params["intl_service_$usps_intl_extra_service"] === "Y"}checked="checked"{/if} value="Y" name="shipping_data[service_params][intl_service_{$usps_intl_extra_service}]" id="intl_service_{$usps_intl_extra_service}">{__("ship_usps_service_{$usps_intl_extra_service}")}</label>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
{/if}

</fieldset>
