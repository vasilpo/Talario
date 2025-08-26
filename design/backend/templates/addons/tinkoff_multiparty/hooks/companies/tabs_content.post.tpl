<div class="{if $selected_section !== "tinkoff_multiparty"}hidden{/if}" id="content_tinkoff_multiparty">
    <div class="control-group">
        <label class="control-label" for="elm_tinkoff_multiparty_shopcode"><b>{__("addons.tinkoff_multiparty.shopcode_point")}:</b></label>
        <div class="controls">
            <p id="elm_tinkoff_multiparty_shopcode">
                <span>{$tinkoff_multiparty_shopcode|default:{__("addons.tinkoff_multiparty.shopcode_is_not_registered")}}</span>
            </p>
        </div>
    </div>

    {if !$tinkoff_multiparty_shopcode && $auth.user_type === "UserTypes::ADMIN"|enum && $shop_data}
        <div class="control-group">
            <div class="buttons-container">
                <div class="controls">
                    {include file="buttons/button.tpl"
                        but_text=__("addons.tinkoff_multiparty.button_form_sm_register")
                        but_role="action"
                        but_href="tinkoff_multiparty.register_shopcode?company_id=`$id`&redirect_url=`$config.current_url|escape:url`"
                        but_meta="btn btn-primary cm-post"
                    }
                </div>
            </div>
        </div>
    {/if}

    {if !$tinkoff_multiparty_shopcode && $auth.user_type === "UserTypes::VENDOR"|enum}
        <div class="control-group">
            <div class="buttons-container">
                <div class="controls">
                    {include file="buttons/button.tpl"
                        but_text=__("addons.tinkoff_multiparty.button_form_register_shopcode")
                        but_role="action"
                        but_href="tinkoff_multiparty.form_sm_register"
                        but_meta="btn"
                    }
                </div>
            </div>
        </div>
    {/if}

    {include file="common/subheader.tpl" title=__("addons.tinkoff_multiparty.main_information") target="#tinkoff_multiparty_main_information"}
    <div class="collapse in" id="tinkoff_multiparty_main_information">
        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_service_provider_email" >{__("addons.tinkoff_multiparty.service_provider_email.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_service_provider_email">
                    <span>{$shop_data.serviceProviderEmail|default:''}</span>
                </p>
            </div>
            <div class="controls">
                <p class="muted description">
                    {__("addons.tinkoff_multiparty.service_provider_email.description") nofilter}
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_billing_descriptor" >{__("addons.tinkoff_multiparty.billing_descriptor.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_billing_descriptor">
                    <span>{$shop_data.billingDescriptor|default:''}</span>
                </p>
            </div>
            {if $shop_data.billingDescriptor}
                <div class="controls">
                    <p class="muted description">
                        {__("addons.tinkoff_multiparty.billing_descriptor.description") nofilter}
                    </p>
                </div>
            {/if}
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_full_name" >{__("addons.tinkoff_multiparty.full_name.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_full_name">
                    <span>{$shop_data.fullName|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_name" >{__("addons.tinkoff_multiparty.name.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_name">
                    <span>{$shop_data.name|default:''}</span>
                </p>
            </div>
            {if $shop_data.name}
                <div class="controls">
                    <p class="muted description">
                        {__("addons.tinkoff_multiparty.name.description") nofilter}
                    </p>
                </div>
            {/if}
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_phone_organization" >{__("addons.tinkoff_multiparty.phone_organization")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_phone_organization">
                    <span>{$shop_data.phone_organization|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_inn" >{__("addons.tinkoff_multiparty.inn.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_inn">
                    <span>{$shop_data.inn|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_kpp" >{__("addons.tinkoff_multiparty.kpp.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_kpp">
                    <span>{$shop_data.kpp|default:''}</span>
                </p>
            </div>
            {if $shop_data.kpp}
                <div class="controls">
                    <p class="muted description">
                        {__("addons.tinkoff_multiparty.kpp.description") nofilter}
                    </p>
                </div>
            {/if}
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_ogrn" >{__("addons.tinkoff_multiparty.ogrn.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_ogrn">
                    <span>{$shop_data.ogrn|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_email" >{__("addons.tinkoff_multiparty.email.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_email">
                    <span>{$shop_data.email|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_site_url" >{__("addons.tinkoff_multiparty.site_url.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_site_url">
                    <span>{$shop_data.siteUrl|default:''}</span>
                </p>
            </div>
        </div>
    </div>

    {include file="common/subheader.tpl" title=__("addons.tinkoff_multiparty.addresses") target="#tinkoff_multiparty_addresses"}
    <div class="collapse in" id="tinkoff_multiparty_addresses">
        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_addresses_type">{__("addons.tinkoff_multiparty.addresses_type.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_addresses_type">
                    <span>{$shop_data.addresses[0].type|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_addresses_zip" >{__("addons.tinkoff_multiparty.addresses_zip.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_addresses_zip">
                    <span>{$shop_data.addresses[0].zip|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_addresses_country" >{__("addons.tinkoff_multiparty.addresses_country.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_addresses_country">
                    <span>{$shop_data.addresses[0].country|default:''}</span>
                </p>
            </div>
            {if $shop_data.addresses[0].country}
                <div class="controls">
                    <p class="muted description">
                        {__("addons.tinkoff_multiparty.addresses_country.description") nofilter}
                    </p>
                </div>
            {/if}
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_addresses_city" >{__("addons.tinkoff_multiparty.addresses_city.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_addresses_city">
                    <span>{$shop_data.addresses[0].city|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_addresses_street" >{__("addons.tinkoff_multiparty.addresses_street.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_addresses_street">
                    <span>{$shop_data.addresses[0].street|default:''}</span>
                </p>
            </div>
        </div>
    </div>

    {include file="common/subheader.tpl" title=__("addons.tinkoff_multiparty.ceo") target="#tinkoff_multiparty_ceo"}
    <div class="collapse in" id="tinkoff_multiparty_ceo">
        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_ceo_first_name" >{__("addons.tinkoff_multiparty.ceo_first_name.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_ceo_first_name">
                    <span>{$shop_data.ceo.firstName|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_ceo_last_name" >{__("addons.tinkoff_multiparty.ceo_last_name.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_ceo_last_name">
                    <span>{$shop_data.ceo.lastName|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_ceo_middle_name" >{__("addons.tinkoff_multiparty.ceo_middle_name.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_ceo_middle_name">
                    <span>{$shop_data.ceo.middleName|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_ceo_birth_date" >{__("addons.tinkoff_multiparty.ceo_birth_date.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_ceo_birth_date">
                    <span>{$shop_data.ceo.birthDate|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_ceo_phone" >{__("addons.tinkoff_multiparty.ceo_phone.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_ceo_phone">
                    <span>{$shop_data.ceo.phone|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_ceo_country" >{__("addons.tinkoff_multiparty.ceo_country.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_ceo_country">
                    <span>{$shop_data.ceo.country|default:''}</span>
                </p>
            </div>
            {if $shop_data.ceo.country}
                <div class="controls">
                    <p class="muted description">
                        {__("addons.tinkoff_multiparty.ceo_country.description") nofilter}
                    </p>
                </div>
            {/if}
        </div>
    </div>

    {include file="common/subheader.tpl" title=__("addons.tinkoff_multiparty.bank") target="#tinkoff_multiparty_bank"}
    <div class="collapse in" id="tinkoff_multiparty_bank">
        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_bank_account" >{__("addons.tinkoff_multiparty.bank_account.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_bank_account">
                    <span>{$shop_data.bankAccount.account|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_bank_name" >{__("addons.tinkoff_multiparty.bank_name.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_bank_name">
                    <span>{$shop_data.bankAccount.bankName|default:''}</span>
                </p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_tinkoff_multiparty_bank_bik" >{__("addons.tinkoff_multiparty.bank_bik.name")}:</label>
            <div class="controls">
                <p id="elm_tinkoff_multiparty_bank_bik">
                    <span>{$shop_data.bankAccount.bik|default:''}</span>
                </p>
            </div>
        </div>
    </div>
</div>