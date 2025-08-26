<style>
    .idabi-settings .control-label {
        width: 242px;
    }

    .idabi-settings .controls {
        margin-left: 262px;
    }

    .idabi-settings select {
        width: 400px;
    }

</style>
<!-- IDD_MODULE_VERSION: 2.8.8 -->
<div class="idabi-settings">

    <h4>{__("addons.alfabank.general_settings")}</h4>
    {if constant('Tygh\Payments\Processors\Alfabank::API_VERSION') >= 2}
        {if $processor_params.login && $processor_params.password}
            {assign var="alfabank_default_token" value=base64_encode($processor_params.login|cat:":"|cat:$processor_params.password)}
        {else}
            {assign var="alfabank_default_token" value=""}
        {/if}
        <div class="control-group">
            <label class="control-label" for="alfabank_token">{__("addons.alfabank.token")}</label>
            <div class="controls">
                <input type="password" name="payment_data[processor_params][token]" id="alfabank_token" style="width: 400px;"
                       value="{$processor_params.token|default:$alfabank_default_token}" size="60" required>
            </div>
        </div>
    {else}
        <div class="control-group">
            <label class="control-label" for="alfabank_login">{__("addons.alfabank.login")}:</label>
            <div class="controls">
                <input type="text" name="payment_data[processor_params][login]" id="alfabank_login"
                       value="{$processor_params.login}" size="60" required>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="alfabank_password">{__("addons.alfabank.password")}:</label>
            <div class="controls">
                <input type="password" name="payment_data[processor_params][password]" id="alfabank_password"
                       value="{$processor_params.password}" size="60" required>
            </div>
        </div>
    {/if}


    <div class="control-group">
        <label class="control-label" for="mode">{__("test_live_mode")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][mode]" id="mode">
                <option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{__("test")}</option>
                <option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{__("live")}</option>
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="two_staging">{__("addons.alfabank.two_staging")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][two_staging]" id="two_staging">
                <option value="0"
                        {if $processor_params.two_staging == 0}selected="selected"{/if}>{__("addons.alfabank.one-stage")}</option>
                <option value="1"
                        {if $processor_params.two_staging == 1}selected="selected"{/if}>{__("addons.alfabank.two-stage")}</option>
            </select>
        </div>
    </div>

    {assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}

    <div class="control-group">
        <label class="control-label" for="alfabank_confirmed_order_status">{__("addons.alfabank.confirmed_order_status")}
            :</label>
        <div class="controls">
            <select name="payment_data[processor_params][confirmed_order_status]" id="alfabank_confirmed_order_status">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}"
                        {if $processor_params.confirmed_order_status == $k || !$processor_params.confirmed_order_status && $k == 'P'}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    {if constant('Tygh\Payments\Processors\Alfabank::ENABLE_BACK_URL_SETTINGS') == true}
        <div class="control-group">
            <label class="control-label" for="alfabank_backToShopUrl">{__("addons.alfabank.back_to_shop_url")}:</label>
            <div class="controls">
                <input type="text" name="payment_data[processor_params][backToShopUrl]" id="alfabank_backToShopUrl"
                       value="{$processor_params.backToShopUrl}" size="60">
                <p class="muted description">adds URL for checkout page button that will take a cardholder back to the assigned merchant web-site URL</p>
            </div>
        </div>
        <!--div class="control-group">
            <label class="control-label" for="alfabank_backToShopUrlName">{__("addons.alfabank.back_to_shop_url_name")}:</label>
            <div class="controls">
                <input type="text" name="payment_data[processor_params][backToShopUrlName]" id="alfabank_backToShopUrlName"
                       value="{$processor_params.backToShopUrlName}" size="60">
                <p class="muted description">customizes default name "Back to shop" button text label if used along with "Back to shop URL"</p>
            </div>
        </div-->
    {/if}


    {if constant('Tygh\Payments\Processors\Alfabank::ENABLE_SSLVERIFY_FIELD') == true}
    <div class="control-group">
        <label class="control-label" for="enable_cacert">{__("addons.alfabank.enable_cacert")}:</label>
        <div class="controls">
            <input type="checkbox" name="payment_data[processor_params][enable_cacert]" id="enable_cacert"
                   value="Y" {if $processor_params.enable_cacert == 'Y'} checked="checked"{/if}/>
        </div>
    </div>
    {/if}

    <div class="control-group">
        <label class="control-label" for="logging">{__("addons.alfabank.logging")}:</label>
        <div class="controls">
            <input type="checkbox" name="payment_data[processor_params][logging]" id="logging"
                   value="Y" {if $processor_params.logging == 'Y'} checked="checked"{/if}/>
        </div>
    </div>

    {if constant('Tygh\Payments\Processors\Alfabank::ENABLE_CART_OPTIONS_SETTINGS') == true}
        {include file="common/subheader.tpl" title=__("addons.alfabank.text_ofd_map") target="#text_alfabank_ofd_map"}
        <div id="text_alfabank_ofd_map" class="in collapse">

        <div class="control-group">
            <label class="control-label" for="send_order">{__("addons.alfabank.send_order")}:</label>
            <div class="controls">
                <input type="checkbox" name="payment_data[processor_params][send_order]" id="send_order"
                       value="Y" {if $processor_params.send_order == 'Y'} checked="checked"{/if}/>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="tax_system">{__("addons.alfabank.tax_system_label")}:</label>
            <div class="controls">
                <select name="payment_data[processor_params][tax_system]" id="tax_system">
                    <option value="0"
                            {if $processor_params.tax_system == 0}selected="selected"{/if}>{__("addons.alfabank.entry_tax_system_0")}
                    </option>
                    <option value="1"
                            {if $processor_params.tax_system == 1}selected="selected"{/if}>{__("addons.alfabank.entry_tax_system_1")}
                    </option>
                    <option value="2"
                            {if $processor_params.tax_system == 2}selected="selected"{/if}>{__("addons.alfabank.entry_tax_system_2")}
                    </option>
                    <option value="3"
                            {if $processor_params.tax_system == 3}selected="selected"{/if}>{__("addons.alfabank.entry_tax_system_3")}
                    </option>
                    <option value="4"
                            {if $processor_params.tax_system == 4}selected="selected"{/if}>{__("addons.alfabank.entry_tax_system_4")}
                    </option>
                    <option value="5"
                            {if $processor_params.tax_system == 5}selected="selected"{/if}>{__("addons.alfabank.entry_tax_system_5")}
                    </option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="tax_type">{__("addons.alfabank.tax_type")}:</label>
            <div class="controls">
                <select name="payment_data[processor_params][tax_type]" id="tax_type">
                    <option value="0" {if $processor_params.tax_type == 0}selected="selected"{/if}>{__("addons.alfabank.no_vat")}</option>
                    <option value="1" {if $processor_params.tax_type == 1}selected="selected"{/if}>{__("addons.alfabank.vat_0")}</option>
                    <option value="2" {if $processor_params.tax_type == 2}selected="selected"{/if}>{__("addons.alfabank.vat_10")}</option>
                    <option value="3" {if $processor_params.tax_type == 3}selected="selected"{/if}>{__("addons.alfabank.vat_18")}</option>
                    <option value="6" {if $processor_params.tax_type == 6}selected="selected"{/if}>{__("addons.alfabank.vat_20")}</option>
                    <option value="4" {if $processor_params.tax_type == 4}selected="selected"{/if}>{__("addons.alfabank.vat_10_110")}</option>
                    <option value="5" {if $processor_params.tax_type == 5}selected="selected"{/if}>{__("addons.alfabank.vat_18_118")}</option>
                    <option value="7" {if $processor_params.tax_type == 7}selected="selected"{/if}>{__("addons.alfabank.vat_20_120")}</option>
                    <option value="10" {if $processor_params.tax_type == 10}selected="selected"{/if}>{__("addons.alfabank.vat_5")}</option>
                    <option value="11" {if $processor_params.tax_type == 11}selected="selected"{/if}>{__("addons.alfabank.vat_5_105")}</option>
                    <option value="12" {if $processor_params.tax_type == 12}selected="selected"{/if}>{__("addons.alfabank.vat_7")}</option>
                    <option value="13" {if $processor_params.tax_type == 13}selected="selected"{/if}>{__("addons.alfabank.vat_7_107")}</option>
                </select>
            </div>
        </div>

    <div class="control-group">
            <label class="control-label" for="versionFfd">{__("addons.alfabank.ffdversion")}
                :</label>
            <div class="controls">
                <select name="payment_data[processor_params][versionFfd]" id="versionFfd">
                    {*<option value="v10" {if $processor_params.versionFfd == "v10"}selected="selected"{/if}>ФФД 1.0*}
                    {*</option>*}
                    <option value="v1_05" {if $processor_params.versionFfd == "v1_05"}selected="selected"{/if}>1.05
                    </option>
                    <option value="v1_2" {if $processor_params.versionFfd == "v1_2"}selected="selected"{/if}>1.2</option>
                </select>
                <p class="description">
                    <small></small>
                </p>
            </div>
        </div>


        <div class="control-group">
            <label class="control-label" for="paymentMethodType">{__("addons.alfabank.payment_method_label")}
                :</label>
            <div class="controls">
                <select name="payment_data[processor_params][paymentMethodType]" id="paymentMethodType">
                    <option value="1" {if $processor_params.paymentMethodType == 1}selected="selected"{/if}>{__("addons.alfabank.entry_payment_method_1")}</option>
                    <option value="2" {if $processor_params.paymentMethodType == 2}selected="selected"{/if}>{__("addons.alfabank.entry_payment_method_2")}</option>
                    <option value="3" {if $processor_params.paymentMethodType == 3}selected="selected"{/if}>{__("addons.alfabank.entry_payment_method_3")}</option>
                    <option value="4" {if $processor_params.paymentMethodType == 4}selected="selected"{/if}>{__("addons.alfabank.entry_payment_method_4")}</option>
                    <option value="5" {if $processor_params.paymentMethodType == 5}selected="selected"{/if}>{__("addons.alfabank.entry_payment_method_5")}</option>
                    <option value="6" {if $processor_params.paymentMethodType == 6}selected="selected"{/if}>{__("addons.alfabank.entry_payment_method_6")}</option>
                    <option value="7" {if $processor_params.paymentMethodType == 7}selected="selected"{/if}>{__("addons.alfabank.entry_payment_method_7")}</option>
                </select>
                <p class="description">
                    <small></small>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="paymentObjectType">{__("addons.alfabank.payment_object_label")}
                :</label>
            <div class="controls">
                <select name="payment_data[processor_params][paymentObjectType]" id="paymentObjectType">
                    <option value="1" {if $processor_params.paymentObjectType == 1}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_1")}</option>
                    <option value="2" {if $processor_params.paymentObjectType == 2}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_2")}</option>
                    <option value="3" {if $processor_params.paymentObjectType == 3}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_3")}</option>
                    <option value="4" {if $processor_params.paymentObjectType == 4}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_4")}</option>
                    <option value="5" {if $processor_params.paymentObjectType == 5}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_5")}</option>
                    {*<option value="6" {if $processor_params.paymentObjectType == 6}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_6")}</option>*}
                    <option value="7" {if $processor_params.paymentObjectType == 7}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_7")}</option>
                    {*<option value="8" {if $processor_params.paymentObjectType == 8}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_8")}</option>*}
                    <option value="9" {if $processor_params.paymentObjectType == 9}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_9")}</option>
                    <option value="10" {if $processor_params.paymentObjectType == 10}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_10")}</option>
                    <option value="11" {if $processor_params.paymentObjectType == 11}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_11")}</option>
                    <option value="12" {if $processor_params.paymentObjectType == 12}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_12")}</option>
                    <option value="13" {if $processor_params.paymentObjectType == 13}selected="selected"{/if}>{__("addons.alfabank.entry_payment_object_13")}</option>
                </select>
                <p class="description">
                    <small></small>
                </p>
            </div>
        </div>

    </div>
    {/if}

</div>
