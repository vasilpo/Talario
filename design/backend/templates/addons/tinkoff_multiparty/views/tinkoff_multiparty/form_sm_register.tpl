{capture name="mainbox"}
    <form action="{""|fn_url}" method="POST" enctype="multipart/form-data" class="form-horizontal" name="update_form_sm_register">
        <input type="hidden" name="redirect_url" value="tinkoff_multiparty.form_sm_register"/>
        {include file="common/subheader.tpl" title=__("addons.tinkoff_multiparty.main_information") target="#tinkoff_multiparty_main_information"}
        <div class="collapse in" id="tinkoff_multiparty_main_information">
            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_service_provider_email" >{__("addons.tinkoff_multiparty.service_provider_email.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[serviceProviderEmail]"
                           id="elm_tinkoff_multiparty_service_provider_email"
                           class="input-text"
                           value="{$shop_data.serviceProviderEmail|default:$company_data.email}"
                    />
                </div>
                <div class="controls">
                    <p class="muted description">
                        {__("addons.tinkoff_multiparty.service_provider_email.description") nofilter}
                    </p>
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_billing_descriptor" >{__("addons.tinkoff_multiparty.billing_descriptor.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[billingDescriptor]"
                           id="elm_tinkoff_multiparty_billing_descriptor"
                           class="input-text"
                           value="{$shop_data.billingDescriptor|default:''}"
                           size="14"
                    />
                </div>
                <div class="controls">
                    <p class="muted description">
                        {__("addons.tinkoff_multiparty.billing_descriptor.description") nofilter}
                    </p>
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_full_name" >{__("addons.tinkoff_multiparty.full_name.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[fullName]"
                           id="elm_tinkoff_multiparty_full_name"
                           class="input-text"
                           value="{$shop_data.fullName|default:''}"
                    />
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_name" >{__("addons.tinkoff_multiparty.name.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[name]"
                           id="elm_tinkoff_multiparty_name"
                           class="input-text"
                           value="{$shop_data.name|default:''}"
                    />
                </div>
                <div class="controls">
                    <p class="muted description">
                        {__("addons.tinkoff_multiparty.name.description") nofilter}
                    </p>
                </div>
            </div>

            {include file="components/phone.tpl"
                id="elm_tinkoff_multiparty_phone_organization"
                name="shop_data[phone_organization]"
                value=$shop_data.phone_organization|default:""
                required=true
                label_text=__("addons.tinkoff_multiparty.phone_organization")
            }

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_inn" >{__("addons.tinkoff_multiparty.inn.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[inn]"
                           id="elm_tinkoff_multiparty_inn"
                           class="input-text"
                           value="{$shop_data.inn|default:$company_data.tax_number}"
                    />
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_kpp" >{__("addons.tinkoff_multiparty.kpp.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[kpp]"
                           id="elm_tinkoff_multiparty_kpp"
                           class="input-text"
                           value="{$shop_data.kpp|default:'000000000'}"
                    />
                </div>
                <div class="controls">
                    <p class="muted description">
                        {__("addons.tinkoff_multiparty.kpp.description") nofilter}
                    </p>
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_ogrn" >{__("addons.tinkoff_multiparty.ogrn.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[ogrn]"
                           id="elm_tinkoff_multiparty_ogrn"
                           class="input-text"
                           value="{$shop_data.ogrn|default:''}"
                    />
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_email" >{__("addons.tinkoff_multiparty.email.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[email]"
                           id="elm_tinkoff_multiparty_email"
                           class="input-text"
                           value="{$shop_data.email|default:$company_data.email}"
                    />
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_site_url" >{__("addons.tinkoff_multiparty.site_url.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[siteUrl]"
                           id="elm_tinkoff_multiparty_site_url"
                           class="input-text"
                           value="{$shop_data.siteUrl|default:$site_url}"
                    />
                </div>
            </div>
        </div>

        {include file="common/subheader.tpl" title=__("addons.tinkoff_multiparty.addresses") target="#tinkoff_multiparty_addresses"}
        <div class="collapse in" id="tinkoff_multiparty_addresses">
            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_addresses_type">{__("addons.tinkoff_multiparty.addresses_type.name")}:</label>
                <div class="controls">
                    <select name="shop_data[addresses][0][type]" id="elm_tinkoff_multiparty_addresses_type">
                        {foreach $addresses_types as $addresses_type}
                            <option value="{$addresses_type}"{if $shop_data.addresses[0].type === $addresses_type} selected="selected"{/if}>{__("addons.tinkoff_multiparty.addresses_type.`$addresses_type`")}</option>
                        {/foreach}
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_addresses_zip" >{__("addons.tinkoff_multiparty.addresses_zip.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[addresses][0][zip]"
                           id="elm_tinkoff_multiparty_addresses_zip"
                           class="input-text"
                           value="{$shop_data.addresses[0].zip|default:''}"
                    />
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_addresses_country" >{__("addons.tinkoff_multiparty.addresses_country.name")}:</label>
                <div class="controls">
                    <select name="shop_data[addresses][0][country]" class="cm-country" id="elm_tinkoff_multiparty_addresses_country">
                        <option value="">- {__("select_country")} -</option>
                        {foreach $countries as $country}
                            <option {if $country.code_A3 === $shop_data.addresses[0].country}selected="selected"{/if} value="{$country.code_A3}">{$country.country}</option>
                        {/foreach}
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_addresses_city" >{__("addons.tinkoff_multiparty.addresses_city.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[addresses][0][city]"
                           id="elm_tinkoff_multiparty_addresses_city"
                           class="input-text"
                           value="{$shop_data.addresses[0].city|default:''}"
                    />
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_addresses_street" >{__("addons.tinkoff_multiparty.addresses_street.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[addresses][0][street]"
                           id="elm_tinkoff_multiparty_addresses_street"
                           class="input-text"
                           value="{$shop_data.addresses[0].street|default:''}"
                    />
                </div>
            </div>
        </div>

        {include file="common/subheader.tpl" title=__("addons.tinkoff_multiparty.ceo") target="#tinkoff_multiparty_ceo"}
        <div class="collapse in" id="tinkoff_multiparty_ceo">
            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_ceo_first_name" >{__("addons.tinkoff_multiparty.ceo_first_name.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[ceo][firstName]"
                           id="elm_tinkoff_multiparty_ceo_first_name"
                           class="input-text"
                           value="{$shop_data.ceo.firstName|default:''}"
                    />
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_ceo_last_name" >{__("addons.tinkoff_multiparty.ceo_last_name.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[ceo][lastName]"
                           id="elm_tinkoff_multiparty_ceo_last_name"
                           class="input-text"
                           value="{$shop_data.ceo.lastName|default:''}"
                    />
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_ceo_middle_name" >{__("addons.tinkoff_multiparty.ceo_middle_name.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[ceo][middleName]"
                           id="elm_tinkoff_multiparty_ceo_middle_name"
                           class="input-text"
                           value="{$shop_data.ceo.middleName|default:''}"
                    />
                </div>
            </div>

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_ceo_birth_date" >{__("addons.tinkoff_multiparty.ceo_birth_date.name")}:</label>
                <div class="controls">
                    <input type="date"
                           name="shop_data[ceo][birthDate]"
                           id="elm_tinkoff_multiparty_ceo_birth_date"
                           class="input-text"
                           value="{$shop_data.ceo.birthDate|default:''}"
                    />
                </div>
            </div>

            {include file="components/phone.tpl"
                id="elm_tinkoff_multiparty_ceo_phone"
                name="shop_data[ceo][phone]"
                value=$shop_data.ceo.phone|default:""
                required=true
                label_text=__("addons.tinkoff_multiparty.ceo_phone.name")
            }

            <div class="control-group">
                <label class="cm-required control-label" for="elm_tinkoff_multiparty_ceo_country" >{__("addons.tinkoff_multiparty.ceo_country.name")}:</label>
                <div class="controls">
                    <select name="shop_data[ceo][country]" class="cm-country" id="elm_tinkoff_multiparty_ceo_country">
                        <option value="">- {__("select_country")} -</option>
                        {foreach $countries as $country}
                            <option {if $country.code_A3 === $shop_data.ceo.country}selected="selected"{/if} value="{$country.code_A3}">{$country.country}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>

        {include file="common/subheader.tpl" title=__("addons.tinkoff_multiparty.bank") target="#tinkoff_multiparty_bank"}
        <div class="collapse in" id="tinkoff_multiparty_bank">
            <div class="control-group">
                <label class="control-label" for="elm_tinkoff_multiparty_bank_account" >{__("addons.tinkoff_multiparty.bank_account.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[bankAccount][account]"
                           id="elm_tinkoff_multiparty_bank_account"
                           class="input-text"
                           value="{$shop_data.bankAccount.account|default:''}"
                    />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_tinkoff_multiparty_bank_name" >{__("addons.tinkoff_multiparty.bank_name.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[bankAccount][bankName]"
                           id="elm_tinkoff_multiparty_bank_name"
                           class="input-text"
                           value="{$shop_data.bankAccount.bankName|default:''}"
                    />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_tinkoff_multiparty_bank_bik" >{__("addons.tinkoff_multiparty.bank_bik.name")}:</label>
                <div class="controls">
                    <input type="text"
                           name="shop_data[bankAccount][bik]"
                           id="elm_tinkoff_multiparty_bank_bik"
                           class="input-text"
                           value="{$shop_data.bankAccount.bik|default:''}"
                    />
                </div>
            </div>
        </div>
    </form>
{/capture}

{capture name="buttons"}
    {include file="buttons/save.tpl"
        but_name="dispatch[tinkoff_multiparty.update_form_sm_register]"
        but_target_form="update_form_sm_register"
        but_role="submit-link"
    }
{/capture}

{include file="common/mainbox.tpl"
    title=__("addons.tinkoff_multiparty.form_register_shopcode")
    content=$smarty.capture.mainbox
    buttons=$smarty.capture.buttons
}