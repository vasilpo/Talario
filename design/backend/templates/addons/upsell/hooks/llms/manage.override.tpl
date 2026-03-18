{$feature=constant("\Tygh\Licensing\Features::LLMS")}
{if fn_upsell_is_upsellable($feature)}
    {capture name="mainbox"}
        {include file="addons/upsell/components/inline.tpl"
            feature=$feature
        }
    {/capture}

    {include file="common/mainbox.tpl"
        title=__("llms_title")
        content=$smarty.capture.mainbox
        select_storefront=true
        show_all_storefront=!("MULTIVENDOR"|fn_allowed_for)
    }
{/if}
