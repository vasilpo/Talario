{if $smarty.const.ACCOUNT_TYPE === "vendor"}
    {$is_collapse = ($main_menu_type === "MainMenuTypeVariants::COLLAPSE"|enum)}

    {* Default dark *}
    {$image_data_dark = (!empty($runtime.vendor_panel_style.logo_dark))
        ? ($runtime.vendor_panel_style.logo_dark|fn_image_to_display) : "{$images_dir}/cart_logo_white.svg"}

    {$image_attributes_dark = [
        "src" => $image_data_dark.image_path|default:"{$images_dir}/cart_logo_white.svg",
        "width" => $image_data_dark.width|default:"",
        "height" => $image_data_dark.height|default:"",
        "class" => (!empty($runtime.vendor_panel_style.logo_dark)) ? "logo-menu__logo logo-menu__logo--custom logo-menu__logo--menu-type-collapse" : "logo-menu__logo logo-menu__logo--cscart logo-menu__logo--menu-type-collapse"
    ]}

    {* Short dark *}
    {$image_short_data_dark = (!empty($runtime.vendor_panel_style.logo_dark))
        ? ($runtime.vendor_panel_style.logo_dark|fn_image_to_display) : "{$images_dir}/cart_logo_header_short_white.svg"}

    {$image_short_attributes_dark = [
        "src" => $image_data_dark.image_path|default:"{$images_dir}/cart_logo_header_short_white.svg",
        "width" => $image_data_dark.width|default:"",
        "height" => $image_data_dark.height|default:"",
        "class" => (!empty($runtime.vendor_panel_style.logo_dark)) ? "logo-menu__logo logo-menu__logo--custom logo-menu__logo-short--menu-type-dropdown" : "logo-menu__logo logo-menu__logo--cscart logo-menu__logo-short--menu-type-dropdown"
    ]}

    {* Default light *}
    {$image_data_light = (!empty($runtime.vendor_panel_style.logo))
        ? ($runtime.vendor_panel_style.logo|fn_image_to_display) : "{$images_dir}/cart_logo.svg"}

    {$image_attributes_light = [
        "src" => $image_data_light.image_path|default:"{$images_dir}/cart_logo.svg",
        "width" => $image_data_light.width|default:"",
        "height" => $image_data_light.height|default:"",
        "class" => (!empty($runtime.vendor_panel_style.logo)) ? "logo-menu__logo logo-menu__logo--custom logo-menu__logo--menu-type-collapse" : "logo-menu__logo logo-menu__logo--cscart logo-menu__logo--menu-type-collapse"
    ]}

    {* Short light *}
    {$image_short_data_light = (!empty($runtime.vendor_panel_style.logo))
        ? ($runtime.vendor_panel_style.logo|fn_image_to_display) : "{$images_dir}/cart_logo_header_short.svg"}

    {$image_short_attributes_light = [
        "src" => $image_data_light.image_path|default:"{$images_dir}/cart_logo_header_short.svg",
        "width" => $image_data_light.width|default:"",
        "height" => $image_data_light.height|default:"",
        "class" => (!empty($runtime.vendor_panel_style.logo)) ? "logo-menu__logo logo-menu__logo--custom logo-menu__logo-short--menu-type-dropdown" : "logo-menu__logo logo-menu__logo--cscart logo-menu__logo-short--menu-type-dropdown"
    ]}

    {if $backoffice_color_scheme === "BackofficeColorSchemeVariants::DARK"|enum}
        <img {$image_attributes_dark|render_tag_attrs nofilter}/>
        <img {$image_short_attributes_dark|render_tag_attrs nofilter}/>
    {elseif $backoffice_color_scheme === "BackofficeColorSchemeVariants::SYSTEM"|enum}
        {$image_attributes_light.class = "{$image_attributes_light.class} logo-menu__logo--light"}
        {$image_short_attributes_light.class = "{$image_short_attributes_light.class} logo-menu__logo--light"}
        {$image_attributes_dark.class = "{$image_attributes_dark.class} logo-menu__logo--dark"}
        {$image_short_attributes_dark.class = "{$image_short_attributes_dark.class} logo-menu__logo--dark"}
        <img {$image_attributes_light|render_tag_attrs nofilter}/>
        <img {$image_short_attributes_light|render_tag_attrs nofilter}/>
        <img {$image_attributes_dark|render_tag_attrs nofilter}/>
        <img {$image_short_attributes_dark|render_tag_attrs nofilter}/>
    {else}
        <img {$image_attributes_light|render_tag_attrs nofilter}/>
        <img {$image_short_attributes_light|render_tag_attrs nofilter}/>
    {/if}
{/if}
