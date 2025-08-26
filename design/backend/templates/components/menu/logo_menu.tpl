{strip}
    {$logo_path_light = "cart_logo.svg"}
    {$logo_path_dark = "cart_logo_white.svg"}

    <a href="{""|fn_url}" {" "}
        class="top-bar__btn top-bar__btn--not-hover mobile-hidden" {" "}
        title="{__("admin.go_to_the_homepage")}" {" "}
    >
        <span class="top-bar__btn-inner logo-menu__btn-inner">
            {hook name="menu:logo_menu"}
                {if $backoffice_color_scheme === "BackofficeColorSchemeVariants::DARK"|enum}
                    <img src="{$images_dir}/{$logo_path_dark}" border="0" alt="" class="logo-menu__logo logo-menu__logo--cscart"/>
                {elseif $backoffice_color_scheme === "BackofficeColorSchemeVariants::SYSTEM"|enum}
                    <img src="{$images_dir}/{$logo_path_light}" border="0" alt="" class="logo-menu__logo logo-menu__logo--cscart logo-menu__logo--light"/>
                    <img src="{$images_dir}/{$logo_path_dark}" border="0" alt="" class="logo-menu__logo logo-menu__logo--cscart logo-menu__logo--dark"/>
                {else}
                    <img src="{$images_dir}/{$logo_path_light}" border="0" alt="" class="logo-menu__logo logo-menu__logo--cscart"/>
                {/if}
            {/hook}
        </span>
    </a>
{/strip}