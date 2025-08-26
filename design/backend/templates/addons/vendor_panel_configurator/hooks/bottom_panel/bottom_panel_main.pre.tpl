{$is_demo_mode = $config.demo_mode|default:false}
{$show_theme_editor = (
    $smarty.const.AREA === "SiteArea::ADMIN_PANEL"|enum
    && $auth.act_as_area && $auth.act_as_area === "UserTypes::VENDOR"|enum
    || $is_demo_mode
) scope=parent}

{if $smarty.const.ACCOUNT_TYPE === "vendor"}
    {if $runtime.vendor_panel_style.logo_dark}
        {$bottom_panel_open_logo = $runtime.vendor_panel_style.logo_dark scope=parent}
    {/if}
    {if $runtime.vendor_panel_style.logo}
        {$bottom_panel_close_logo = $runtime.vendor_panel_style.logo scope=parent}
    {/if}
{/if}
