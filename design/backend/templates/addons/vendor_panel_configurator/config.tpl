{if $smarty.const.ACCOUNT_TYPE === "vendor"}

    {* Page: addons.update&addon=vendor_panel_style&selected_section=settings *}
    {* Element color *}
    {if $runtime.vendor_panel_style.element_color}
        {$mainColor = $dashboard_element_color|default:$runtime.vendor_panel_style.element_color scope=parent}
    {elseif $config["vendor_panel_style"]["main_color"]}
        {$mainColor = $config["vendor_panel_style"]["main_color"] scope=parent}
    {else}
        {$mainColor = "#024567" scope=parent} {* Admin main color #0388cc + 20% darken *}
    {/if}

    {* Sidebar color *}
    {if $runtime.vendor_panel_style.sidebar_color}
        {$menuSidebarColor = $dashboard_sidebar_color|default:$runtime.vendor_panel_style.sidebar_color scope=parent}
    {elseif $config["vendor_panel_style"]["menu_sidebar_color"]}
        {$menuSidebarColor = $config["vendor_panel_style"]["menu_sidebar_color"] scope=parent}
    {else}
        {$menuSidebarColor = "#fff" scope=parent}
    {/if}

    {* Menu sidebar background *}
    {if $runtime.vendor_panel_style.main_pair.icon.image_path}
        {$menuSidebarBg = "url(`$runtime.vendor_panel_style.main_pair.icon.image_path`)" scope=parent}
    {elseif $config["vendor_panel_style"]["menu_sidebar_bg"]}
        {$menuSidebarBg = "url(`$config['vendor_panel_style']['menu_sidebar_bg']`)" scope=parent}
    {else}
        {$menuSidebarBg = "none" scope=parent}
    {/if}

    {if $is_gray_main_color}
        {$isGrayMainColor = $is_gray_main_color|default:false scope=parent}
    {/if}
{/if}
