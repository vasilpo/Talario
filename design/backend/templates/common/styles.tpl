{include file="common/critical_css.tpl"}

{function get_tygh_style_contents}
    {style src="ui/jqueryui.css"}
    {style src="lib/select2/select2.min.css"}
    {style src="lib/bootstrap_switch/stylesheets/bootstrapSwitch.css"}

    {hook name="index:styles"}
        {style src="styles.less"}

        {if $smarty.const.ACCOUNT_TYPE === "vendor"}
            {style src="config_vendor.less"}
        {/if}

        {style src="tygh/supports.css"}

        {style src="glyphs.css"}

        {include file="views/statuses/components/styles.tpl" type=$smarty.const.STATUSES_ORDER}

        {if $language_direction == 'rtl'}
            {style src="rtl.less"}
        {/if}
    {/hook}

    {if $runtime.customization_mode.block_manager}
        {style src="../../themes/responsive/css/tygh/components/block_manager.less"}
    {/if}

    {if $color_scheme === "BackofficeColorSchemeVariants::DARK"|enum}
        {style src="config_dark.less"}
        {if $smarty.const.ACCOUNT_TYPE === "vendor"}
            {style src="config_vendor_dark.less"}
        {/if}
    {/if}
{/function}


{$is_theme_editor_currently_opened = $smarty.const.ACCOUNT_TYPE === "vendor" && $runtime.customization_mode.theme_editor === true}

{if
    $backoffice_color_scheme === "BackofficeColorSchemeVariants::DARK"|enum
    && !$is_theme_editor_currently_opened
}
    {styles}
        {get_tygh_style_contents color_scheme="BackofficeColorSchemeVariants::DARK"|enum}
    {/styles}
{elseif
    $backoffice_color_scheme === "BackofficeColorSchemeVariants::SYSTEM"|enum
    && !$is_theme_editor_currently_opened
}
    {styles link_media="screen and (prefers-color-scheme: light)"}
        {get_tygh_style_contents color_scheme="BackofficeColorSchemeVariants::LIGHT"|enum}
    {/styles}

    {styles link_media="screen and (prefers-color-scheme: dark)"}
        {get_tygh_style_contents color_scheme="BackofficeColorSchemeVariants::DARK"|enum}
    {/styles}
{else}
    {styles}
        {get_tygh_style_contents color_scheme="BackofficeColorSchemeVariants::LIGHT"|enum}
    {/styles}
{/if}
