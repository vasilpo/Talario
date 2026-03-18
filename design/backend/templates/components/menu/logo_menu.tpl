{strip}
    {$logo_path_light = "cart_logo.svg"}
    {$logo_short_path_light = "cart_logo_header_short.svg"}
    {$logo_path_dark = "cart_logo_white.svg"}
    {$logo_short_path_dark = "cart_logo_header_short_white.svg"}

    <a href="{""|fn_url}" {" "}
        class="logo-menu__btn" {" "}
        title="{__("admin.go_to_the_homepage")}" {" "}
    >
        <span class="logo-menu__btn-inner">
            {hook name="menu:logo_menu"}
                {if $backoffice_color_scheme === "BackofficeColorSchemeVariants::DARK"|enum}
                    <img src="{$images_dir}/{$logo_path_dark}" border="0" alt="" class="logo-menu__logo logo-menu__logo--cscart logo-menu__logo--menu-type-collapse"/>
                    <img src="{$images_dir}/{$logo_short_path_dark}" border="0" alt="" class="logo-menu__logo-short logo-menu__logo-short--cscart logo-menu__logo-short--menu-type-dropdown"/>
                {elseif $backoffice_color_scheme === "BackofficeColorSchemeVariants::SYSTEM"|enum}
                    <img src="{$images_dir}/{$logo_path_light}" border="0" alt="" class="logo-menu__logo logo-menu__logo--cscart logo-menu__logo--light logo-menu__logo--menu-type-collapse"/>
                    <img src="{$images_dir}/{$logo_short_path_light}" border="0" alt="" class="logo-menu__logo-short logo-menu__logo-short--cscart logo-menu__logo-short--light logo-menu__logo-short--menu-type-dropdown"/>
                    <img src="{$images_dir}/{$logo_path_dark}" border="0" alt="" class="logo-menu__logo logo-menu__logo--cscart logo-menu__logo--dark logo-menu__logo--menu-type-collapse"/>
                    <img src="{$images_dir}/{$logo_short_path_dark}" border="0" alt="" class="logo-menu__logo-short logo-menu__logo-short--cscart logo-menu__logo-short--dark logo-menu__logo-short--menu-type-dropdown"/>
                {else}
                    <img src="{$images_dir}/{$logo_path_light}" border="0" alt="" class="logo-menu__logo logo-menu__logo--cscart logo-menu__logo--menu-type-collapse"/>
                    <img src="{$images_dir}/{$logo_short_path_light}" border="0" alt="" class="logo-menu__logo-short logo-menu__logo-short--cscart logo-menu__logo-short--menu-type-dropdown"/>
                {/if}
            {/hook}
        </span>
    </a>
{/strip}