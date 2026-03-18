{if "MULTIVENDOR"|fn_allowed_for && ($company_name || $company_id) && $settings.Vendors.display_vendor == "Y"}
    <div class="ty-control-group ty-product-company-data{if !$capture_options_vs_qty} product-list-field{/if}">
        {hook name="companies:product_company_data"}
            <label class="ty-control-group__label ty-product-company-data__label">{__("vendor")}:</label>
            <span class="ty-control-group__item ty-company-name ty-product-company-data__item"><a href="{"companies.products?company_id=`$company_id`"|fn_url}" class="ty-company-name-link">{if $company_name}{$company_name}{else}{$company_id|fn_get_company_name}{/if}</a></span>
        {/hook}
    </div>
{/if}
