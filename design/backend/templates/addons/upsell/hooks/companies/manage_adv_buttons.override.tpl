{$feature = constant("\Tygh\Licensing\Features::ADD_STOREFRONT")}

{if fn_allowed_for("ULTIMATE") && $auth.user_type === "UserTypes::ADMIN"|enum && !fn_is_allowed($feature) && $settings.Upgrade_center.license_number}
    {$popup_id="upsell_help_block_popup_{$feature}"}

    {include file="common/tools.tpl"
        tool_override_meta="btn btn-primary cm-dialog-opener cm-dialog-auto-height"
        prefix="top"
        hide_tools=true
        title=__("add_storefront")
        link_text=__("add_storefront")
        icon="icon-plus"
        meta_data="data-ca-target-id='$popup_id'"
    }

    {include file="addons/upsell/components/popup.tpl" popup_id=$popup_id feature=$feature}
{else}
    {include file="common/tools.tpl"
        tool_href="companies.add"
        tool_override_meta="btn btn-primary nav__actions-btn-primary"
        prefix="top"
        hide_tools=true
        title=$add_vendor_text
        link_text=$add_vendor_text
        icon="icon-plus"
    }
{/if}
