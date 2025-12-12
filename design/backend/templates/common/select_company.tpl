{$switcher_param_name = "switch_company_id"}
{$switcher_data_name = "company_id"}
{$switcher_title = {__("pick_store")}}

{if fn_allowed_for("MULTIVENDOR:ULTIMATE")}
    {$switcher_param_name = "s_storefront"}
    {$switcher_data_name = "storefront_id"}
    {$switcher_title = {__("select_storefront", ["[store]" => ""])}}
{/if}

{if $runtime.simple_ultimate}
	{capture name="mainbox"}
        <h4>{__("error_occured")}</h4>
        <p>{__("simple_ultimate_companies_selector") nofilter}</p>
	{/capture}
{else}
	{capture name="mainbox"}
        {$id = $select_id|default:"top_company_id"}

        <div class="store-selector js-storefront-switcher"
            data-ca-switcher-param-name={$switcher_param_name}
            data-ca-switcher-data-name={$switcher_data_name}>
            <div class="inline-label">{$switcher_title} -</div>
            <div class="input-large inline-block">
                {include file="views/storefronts/components/picker/picker.tpl"
                    autoopen=true
                    show_advanced=false
                }
            </div>
        </div>
	{/capture}
{/if}

{include file="common/mainbox.tpl" title=__($title) content=$smarty.capture.mainbox}