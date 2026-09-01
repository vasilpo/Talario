{if $smarty.const.ACCOUNT_TYPE === "vendor"}
    {$is_collapse = ($main_menu_type === "MainMenuTypeVariants::COLLAPSE"|enum)}

    {* Talario: фирменные логотипы с прозрачным фоном, без белой подложки. *}
    {$image_data_dark = "{$images_dir}/addons/talario_vendor_cabinet/talario_logo_transparent.svg"}
    {$image_attributes_dark = [
        "src" => $image_data_dark,
        "class" => "logo-menu__logo logo-menu__logo--menu-type-collapse"
    ]}

    {$image_short_data_dark = "{$images_dir}/addons/talario_vendor_cabinet/talario_logo_symbol_transparent.svg"}
    {$image_short_attributes_dark = [
        "src" => $image_short_data_dark,
        "class" => "logo-menu__logo logo-menu__logo-short--menu-type-dropdown"
    ]}

    {$image_data_light = "{$images_dir}/addons/talario_vendor_cabinet/talario_logo_transparent.svg"}
    {$image_attributes_light = [
        "src" => $image_data_light,
        "class" => "logo-menu__logo logo-menu__logo--menu-type-collapse"
    ]}

    {$image_short_data_light = "{$images_dir}/addons/talario_vendor_cabinet/talario_logo_symbol_transparent.svg"}
    {$image_short_attributes_light = [
        "src" => $image_short_data_light,
        "class" => "logo-menu__logo logo-menu__logo-short--menu-type-dropdown"
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
