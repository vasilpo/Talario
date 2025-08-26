{if $smarty.const.ACCOUNT_TYPE === "vendor"}
    {* Recommended logo size: 416x68px *}
    {$top_bar_height = 56}
    {$top_bar_padding = 11}
    {$image_height = ($top_bar_height - 2 * $top_bar_padding) * 2} {* HiDPI height *}

    {$image_data_dark = (!empty($runtime.vendor_panel_style.logo_dark))
        ? ($runtime.vendor_panel_style.logo_dark|fn_image_to_display:0:$image_height) : "{$images_dir}/cart_logo_white.svg"}

    {$image_attributes_dark = [
        "src" => $image_data_dark.image_path|default:"{$images_dir}/cart_logo_white.svg",
        "width" => $image_data_dark.width|default:"",
        "height" => $image_data_dark.height|default:"",
        "class" => (!empty($runtime.vendor_panel_style.logo_dark)) ? "logo-menu__logo logo-menu__logo--custom" : "logo-menu__logo logo-menu__logo--cscart"
    ]}

    {$image_data_light = (!empty($runtime.vendor_panel_style.logo))
        ? ($runtime.vendor_panel_style.logo|fn_image_to_display:0:$image_height) : "{$images_dir}/cart_logo.svg"}

    {$image_attributes_light = [
        "src" => $image_data_light.image_path|default:"{$images_dir}/cart_logo.svg",
        "width" => $image_data_light.width|default:"",
        "height" => $image_data_light.height|default:"",
        "class" => (!empty($runtime.vendor_panel_style.logo)) ? "logo-menu__logo logo-menu__logo--custom" : "logo-menu__logo logo-menu__logo--cscart"
    ]}

    <span class="top-bar__btn-inner logo-menu__btn-inner">
        {if $backoffice_color_scheme === "BackofficeColorSchemeVariants::DARK"|enum}
            <img {$image_attributes_dark|render_tag_attrs nofilter}/>
        {elseif $backoffice_color_scheme === "BackofficeColorSchemeVariants::SYSTEM"|enum}
            {$image_attributes_light.class = "{$image_attributes_light.class} logo-menu__logo--light"}
            {$image_attributes_dark.class = "{$image_attributes_dark.class} logo-menu__logo--dark"}
            <img {$image_attributes_light|render_tag_attrs nofilter}/>
            <img {$image_attributes_dark|render_tag_attrs nofilter}/>
        {else}
            <img {$image_attributes_light|render_tag_attrs nofilter}/>
        {/if}
    </span>
{/if}
