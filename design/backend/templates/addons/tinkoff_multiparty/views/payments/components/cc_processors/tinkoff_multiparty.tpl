{include file="common/subheader.tpl" title=__("information") target="#tinkoff_payment_instruction_`$payment_id`"}
<div id="tinkoff_multiparty_payment_instruction_{$payment_id}" class="in collapse">
    {include file="common/widget_copy.tpl"
        widget_copy_text=__("addons.tinkoff_multiparty.url_for_payment_notifications")
        widget_copy_code_text="{$notification_url|default: fn_url("tinkoff_multiparty.get_notification","SiteArea::STOREFRONT"|enum)}"
    }

    {include file="common/widget_copy.tpl"
        widget_copy_text=__("addons.tinkoff_multiparty.url_for_success")
        widget_copy_code_text="{$success_url|default: fn_url("tinkoff_multiparty.success","SiteArea::STOREFRONT"|enum)}"
    }

    {include file="common/widget_copy.tpl"
        widget_copy_text=__("addons.tinkoff_multiparty.url_for_fail")
        widget_copy_code_text="{$fail_url|default: fn_url("tinkoff_multiparty.fail","SiteArea::STOREFRONT"|enum)}"
    }
</div>
<input type="hidden"
       name="payment_data[processor_params][is_tinkoff_multiparty]"
       value="{"YesNo::YES"|enum}"
/>
{include file="common/subheader.tpl" title=__("settings") target="#tinkoff_multiparty_payment_settings_`$payment_id`"}
<div id="tinkoff_multiparty_payment_settings_{$payment_id}" class="in collapse">
    <div class="control-group">
        <label class="control-label cm-required" for="terminal_key_{$payment_id}">{__("addons.tinkoff_multiparty.terminal_key")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][terminal_key]" id="terminal_key_{$payment_id}" value="{$processor_params.terminal_key}" class="input-text-large"  size="60" />
        </div>
        <div class="controls">
            <p class="muted description">{__("addons.tinkoff_multiparty.terminal_key_notice")}</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label cm-required" for="password_{$payment_id}">{__("addons.tinkoff_multiparty.terminal_password")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][password]" id="password_{$payment_id}" value="{$processor_params.password}" class="input-text-large"  size="60" />
        </div>
        <div class="controls">
            <p class="muted description">{__("addons.tinkoff_multiparty.terminal_password_notice")}</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label cm-required" for="sm_register_username_{$payment_id}">{__("addons.tinkoff_multiparty.sm_register_username")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][sm_register_username]" id="sm_register_username_{$payment_id}" value="{$processor_params.sm_register_username}" class="input-text-large" size="60" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label cm-required" for="sm_register_password_{$payment_id}">{__("addons.tinkoff_multiparty.sm_register_password")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][sm_register_password]" id="sm_register_password_{$payment_id}" value="{$processor_params.sm_register_password}" class="input-text-large" size="60" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label cm-required" for="number_agreement_with_bank_{$payment_id}">{__("addons.tinkoff_multiparty.number_agreement_with_bank")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][number_agreement_with_bank]" id="number_agreement_with_bank_{$payment_id}" value="{$processor_params.number_agreement_with_bank}" class="input-text-large" size="20" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label cm-required" for="date_agreement_with_bank_{$payment_id}">{__("addons.tinkoff_multiparty.date_agreement_with_bank")}:</label>
        <div class="controls">
            <input type="date" name="payment_data[processor_params][date_agreement_with_bank]" id="date_agreement_with_bank_{$payment_id}" value="{$processor_params.date_agreement_with_bank}" class="input-text-large" size="20" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label cm-required" for="marketplace_inn_{$payment_id}">{__("addons.tinkoff_multiparty.marketplace_inn.name")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][marketplace_inn]" id="marketplace_inn_{$payment_id}" value="{$processor_params.marketplace_inn}" class="input-text-large" size="20" />
        </div>
        <div class="controls">
            <p class="muted description">{__("addons.tinkoff_multiparty.marketplace_inn.description")}</p>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="pay_type_{$payment_id}">
            {__("addons.tinkoff_multiparty.pay_type")}
        </label>
        <div class="controls">
            <label class="radio" for="one_step_type_{$payment_id}">
                <input type="radio"
                       name="payment_data[processor_params][pay_type]"
                       id="one_step_type_{$payment_id}"
                       {if !$processor_params.pay_type || $processor_params.pay_type === "\Tygh\Addons\TinkoffMultiparty\Enum\PayTypes::ONE_STEP"|constant}checked="checked"{/if}
                       value="{"\Tygh\Addons\TinkoffMultiparty\Enum\PayTypes::ONE_STEP"|constant}"
                />
                {__("addons.tinkoff_multiparty.one_step_payment")}
            </label>
            <label class="radio" for="two_step_type_{$payment_id}">
                <input type="radio"
                       name="payment_data[processor_params][pay_type]"
                       id="two_step_type_{$payment_id}"
                       {if $processor_params.pay_type === "\Tygh\Addons\TinkoffMultiparty\Enum\PayTypes::TWO_STEP"|constant}checked="checked"{/if}
                       value="{"\Tygh\Addons\TinkoffMultiparty\Enum\PayTypes::TWO_STEP"|constant}"
                />
                {__("addons.tinkoff_multiparty.two_step_payment")}
            </label>
        </div>
        <div class="controls">
            <p class="muted description">{__("addons.tinkoff_multiparty.two_step.description")}</p>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="tax_system_{$payment_id}">{__("rus_taxes.tax_system")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][tax_system]" id="tax_system_{$runtime.company_id}">
                {foreach $tax_systems as $tax_system}
                    <option value="{$tax_system}"
                            {if $processor_params.tax_system|default:"osn" === $tax_system}
                                selected="selected"
                            {/if}
                    >{__("rus_taxes.tax_system.`$tax_system`")}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>
