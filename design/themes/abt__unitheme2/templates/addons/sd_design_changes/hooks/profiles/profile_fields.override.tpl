<!-- The "{$smarty.template}" template was overriden by the "sd_design_changes" add-on -->
{$check_storefront_passed=isset($sd_check_storefront)}
{if (!$check_storefront_passed) || ($check_storefront_passed && $sd_check_storefront && $field.storefront_show == "YesNo::YES"|enum)}
    <div class="ty-control-group ty-profile-field__item ty-{$field.class}{if $field.wrapper_class} {$field.wrapper_class}{/if}{if $field.field_type == "ProfileFieldTypes::PHONE"|enum || $field.autocomplete_type == "phone-full"} cm-mask-phone-group{/if}"{if $field.field_type == "ProfileFieldTypes::PHONE"|enum || $field.autocomplete_type == "phone-full"} data-ca-phone-mask-group-id="{$element_id}"{/if}>
        {if $sd_label_position != "after" && ($pref_field_name != $field.description || $required == "Y") && $field.field_type != "ProfileFieldTypes::VENDOR_TERMS"|enum}
            {strip}
            <label
                for="{$element_id}"
                class="ty-control-group__title cm-profile-field {if $field.autocomplete_type == "phone-full" || $field.field_type == "ProfileFieldTypes::PHONE"|enum}cm-mask-phone-label{/if} {if $required == "Y"}cm-required cm-trim{/if}{if $field.field_type == "Z"} cm-zipcode{/if}{if $field.field_type == "E"} cm-email{/if} {if $field.field_type == "Z"}{if $section == "S"}cm-location-shipping{else}cm-location-billing{/if}{/if}"
            >
                {if $field.field_type == "ProfileFieldTypes::CHECKBOX"|enum && $field.link}
                    <a href="{$field.link|fn_url}"
                        {if strpos($field.class, 'sd-dialog-opener') !== false}
                            class="cm-dialog-opener cm-dialog-auto-size cm-dialog-destroy-on-close"
                            data-ca-dialog-title="{$field.description}"
                        {/if}
                        target="_blank"
                    >
                {/if}
                    {$field.description}
                {if $field.field_type == "ProfileFieldTypes::CHECKBOX"|enum && $field.link}
                    </a>
                {/if}
            </label>
            {/strip}
        {/if}

            {if $field.field_type == "ProfileFieldTypes::STATE"|enum}
                {$_country = $profile_data.s_country}
                {$_state = $value}

                <select {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} id="{$element_id}" class="ty-profile-field__select-state cm-state {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if} {if !$skip_field}{$_class}{/if}" name="{$data_name}[{$data_id}]" {if !$skip_field}{$disabled_param nofilter}{/if}>
                    {if $required !== "Y"}
                        <option value="">- {__("select_state")} -</option>
                    {/if}
                    {if $states && $states.$_country}
                        {foreach from=$states.$_country item=state}
                            <option {if $_state == $state.code}selected="selected"{/if} value="{$state.code}">{$state.state}</option>
                        {/foreach}
                    {/if}
                </select>

                <input {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} type="text" id="elm_{$field.field_id}_d" name="{$data_name}[{$data_id}]" size="32" maxlength="64" value="{$_state}" disabled="disabled" class="cm-state {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if} ty-input-text hidden {if $_class}disabled{/if}"/>

            {elseif $field.field_type == "ProfileFieldTypes::COUNTRY"|enum}
                {$_country = $value}

                <select {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} id="{$element_id}" class="ty-profile-field__select-country cm-country {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if} {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}" name="{$data_name}[{$data_id}]" {if !$skip_field}{$disabled_param nofilter}{/if}>
                    {hook name="profiles:country_selectbox_items"}
                    {if $required !== "Y"}
                        <option value="">- {__("select_country")} -</option>
                    {/if}
                    {foreach from=$countries item="country" key="code"}
                    <option {if $_country == $code}selected="selected"{/if} value="{$code}">{$country}</option>
                    {/foreach}
                    {/hook}
                </select>

            {elseif $field.field_type == "ProfileFieldTypes::CHECKBOX"|enum}
                <input type="hidden" name="{$data_name}[{$data_id}]" value="N" {if !$skip_field}{$disabled_param nofilter}{/if} />
                <input type="checkbox" id="{$element_id}" name="{$data_name}[{$data_id}]" value="Y" {if $value == "Y"}checked="checked"{/if} class="checkbox {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}" {if !$skip_field}{$disabled_param nofilter}{/if} />

            {elseif $field.field_type == "ProfileFieldTypes::TEXT_AREA"|enum}
                <textarea placeholder=" " {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} class="ty-input-textarea {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}" id="{$element_id}" name="{$data_name}[{$data_id}]" cols="32" rows="3" {if !$skip_field}{$disabled_param nofilter}{/if}>{$value}</textarea>

            {elseif $field.field_type == "ProfileFieldTypes::DATE"|enum}
                {if !$skip_field}
                    {include file="common/calendar.tpl" date_id="`$id_prefix`elm_`$field.field_id`" date_name="`$data_name`[`$data_id`]" date_val=$value extra=$disabled_param}
                {else}
                    {include file="common/calendar.tpl" date_id="`$id_prefix`elm_`$field.field_id`" date_name="`$data_name`[`$data_id`]" date_val=$value}
                {/if}

            {elseif $field.field_type == "ProfileFieldTypes::SELECT_BOX"|enum}
                <select {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} id="{$element_id}" class="ty-profile-field__select {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}" name="{$data_name}[{$data_id}]" {if !$skip_field}{$disabled_param nofilter}{/if}>
                    {if $required != "Y"}
                    <option value="">--</option>
                    {/if}
                    {foreach from=$field.values key=k item=v}
                    <option {if $value == $k}selected="selected"{/if} value="{$k}">{$v}</option>
                    {/foreach}
                </select>

            {elseif $field.field_type == "ProfileFieldTypes::RADIO"|enum}
                <div id="{$element_id}">
                    {foreach from=$field.values key=k item=v name="rfe"}
                    <input class="radio {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if} {$id_prefix}elm_{$field.field_id}" type="radio" id="{$id_prefix}elm_{$field.field_id}_{$k}" name="{$data_name}[{$data_id}]" value="{$k}" {if (!$value && $smarty.foreach.rfe.first) || $value == $k}checked="checked"{/if} {if !$skip_field}{$disabled_param nofilter}{/if} /><span class="radio">{$v}</span>
                    {/foreach}
                </div>

            {elseif $field.field_type == "ProfileFieldTypes::ADDRESS_TYPE"|enum}
                <input class="radio {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if} {$id_prefix}elm_{$field.field_id}" type="radio" id="{$id_prefix}elm_{$field.field_id}_residential" name="{$data_name}[{$data_id}]" value="residential" {if !$value || $value == "residential"}checked="checked"{/if} {if !$skip_field}{$disabled_param nofilter}{/if} /><span class="radio">{__("address_residential")}</span>
                <input class="radio {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if} {$id_prefix}elm_{$field.field_id}" type="radio" id="{$id_prefix}elm_{$field.field_id}_commercial" name="{$data_name}[{$data_id}]" value="commercial" {if $value == "commercial"}checked="checked"{/if} {if !$skip_field}{$disabled_param nofilter}{/if} /><span class="radio">{__("address_commercial")}</span>

            {elseif $field.field_type == "ProfileFieldTypes::VENDOR_TERMS"|enum}

                {include file="views/profiles/components/vendor_terms.tpl"}

            {elseif $field.field_type == "ProfileFieldTypes::FILE"|enum}
                {if isset($value.file_name)}
                    <div class="text-type-value" data-file-id="{$hash_name}">
                        {$additional_class = ($field.required === "YesNo::YES"|enum) ? "cm-file-required" : ""}
                        {include_ext file="common/icon.tpl"
                            class="ty-icon-cancel-circle ty-fileuploader__icon cm-file-remove `$additional_class`"
                            id=$hash_name title=__("remove_this_item")
                        }
                        <span class="ty-fileuploader__filename ty-filename-link upload-filename">
                            <a href="{$value.link|default:""}">{$value.file_name}</a>
                        </span>
                    </div>
                {/if}
                {include file="common/fileuploader.tpl"
                    var_name=$var_name
                    label_id="elm_{$id_prefix}{$field.field_id}"
                    hidden_name="{$data_name}[{$data_id}]"
                    hidden_value=$value.file_name|default:""
                    prefix=$id_prefix
                    disabled_param=$disabled_param
                    max_upload_filesize=$config.tweaks.profile_field_max_upload_filesize
                }

            {elseif $field.field_type == "ProfileFieldTypes::PHONE"|enum || $field.autocomplete_type == "phone-full"}
                {$attrs = [size => "32"]}
                {if $field.autocomplete_type}
                    {$attrs["x-autocompletetype"] = $field.autocomplete_type}
                {/if}
                {$class = (!$skip_field) ? $_class : "cm-skip-avail-switch"}
                {if !$skip_field}
                    {$attrs_string = $disabled_param}
                {/if}
                {include file="components/phone.tpl"
                    show_control_group=false
                    id=$element_id
                    name="`$data_name`[`$data_id`]"
                    value=$value
                    label_text=$field.description
                    attrs_string=$attrs_string
                    attrs=$attrs
                    class=(($smarty.foreach.profile_fields.index === 0) ? "`$class` cm-focus" : $class)
                    width="full"
                }
            {elseif $field.field_type == $smarty.const.PROFILE_FIELD_TYPE_VENDOR_PLAN}
                <div class="{if !$vendor_plans} hidden{/if}">
                    {$sd_label_position = "before"}
                    {if $pref_field_name != $field.description || $field.required == "Y"}
                        <label for="{$id_prefix}elm_{$field.field_id}" class="ty-control-group__title cm-profile-field">{$field.description}</label>
                    {/if}

                    {$default_plan = $company_data.plan_id}
                    {if !$default_plan}
                        {$default_plan = $smarty.request.plan_id}
                    {/if}

                    <select {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} id="{$id_prefix}elm_{$field.field_id}" class="ty-profile-field__select {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}" name="{$data_name}[{$data_id}]" {if !$skip_field}{$disabled_param nofilter}{/if}>
                        {foreach $field.plans as $plan}
                            <option value="{$plan.plan_id}"{if (!$default_plan && $plan.is_default) || $plan.plan_id == $default_plan} selected="selected"{/if}>{$plan.plan} ({include file="common/price.tpl" value=$plan.price})</option>
                        {/foreach}
                    </select>
                </div>
            {else}  {* Simple input *}
                <input
                    {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if}
                    type="text"
                    id="{$element_id}"
                    name="{$data_name}[{$data_id}]"
                    size="32"
                    value="{$value}"
                    placeholder=" "
                    class="ty-input-text {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if} {if $smarty.foreach.profile_fields.index == 0} cm-focus{/if}"
                    {if !$skip_field}{$disabled_param nofilter}{/if}
                />
            {/if}

        {if $sd_label_position == "after" && ($pref_field_name != $field.description || $required == "Y") && $field.field_type != "ProfileFieldTypes::VENDOR_TERMS"|enum}
            <label
                for="{$element_id}"
                class="ty-control-group__title cm-profile-field {if $field.autocomplete_type == "phone-full" || $field.field_type == "ProfileFieldTypes::PHONE"|enum}cm-mask-phone-label{/if} {if $required == "Y"}cm-required cm-trim{/if}{if $field.field_type == "Z"} cm-zipcode{/if}{if $field.field_type == "E"} cm-email{/if} {if $field.field_type == "Z"}{if $section == "S"}cm-location-shipping{else}cm-location-billing{/if}{/if}"
            >{$field.description}</label>
        {/if}

        {assign var="pref_field_name" value=$field.description}
    </div>
{/if}
