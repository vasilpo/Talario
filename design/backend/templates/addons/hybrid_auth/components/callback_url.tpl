{$protocol = ($settings.Security.secure_storefront === "YesNo::YES"|enum) ? "https" : "http"}
{$provider_name = $providers_schema[$provider]['provider']}
{$provider = $providers_schema[$provider]}
{if $provider_data.storefront_ids}
    {$storefront_ids = $provider_data.storefront_ids}
{else}
    {$storefront_ids = $all_storefront_ids}
{/if}
{if $callback_urls}
    {foreach $callback_urls as $callback_url}
        <div class="control-group">
            {include file="common/widget_copy.tpl"
                widget_copy_code_text=$callback_url
            }
        </div>
    {/foreach}
{else}
    {if $callback_url}
        {$callback_url = $callback_url|ltrim:"/\\"}
    {/if}
    {foreach $storefront_ids as $storefront_id}
        <div class="control-group">
            {include file="common/widget_copy.tpl"
                widget_copy_code_text=($callback_url|default:"auth.process&storefront_id={$storefront_id}")|fn_url:("SiteArea::STOREFRONT"|enum):$protocol
            }
        </div>
    {/foreach}
{/if}
