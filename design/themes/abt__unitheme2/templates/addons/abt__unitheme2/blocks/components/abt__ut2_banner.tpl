{** ab:single-banner **}
{hook name="abt__ut2_banner:banners"}

<div class="ut2-banner ut2-banner-{$b.abt__ut2_device_settings} {if $block.properties.navigation == "L"} ut2-scroll-item {/if}{$b.abt__ut2_class}" style="{strip}
    {if $b.abt__ut2_color_scheme === "dark"}{$b_color_scheme = "0,0,0"}
        --ab-banner-color-schemes: dark;
    {else}{$b_color_scheme = "255,255,255"}
        --ab-banner-color-schemes: light;
    {/if}
    {if $b.abt__ut2_background_color_use === "YesNo::YES"|enum}--ab-banner-background-color: {$b.abt__ut2_background_color|default:white};{/if}
    {if $b.abt__ut2_background_image_size}--ab-banner-background-size: {$b.abt__ut2_background_image_size};{/if}
    {if $b.abt__ut2_image_position}--ab-banner-background-position: {$b.abt__ut2_image_position};{/if}

    {if $b.abt__ut2_content_bg === "colored" && $b.abt__ut2_content_bg_color_use === "YesNo::YES"|enum}
        --ab-banner-mask-background-color: {$b.abt__ut2_content_bg_color};
    {/if}
    {if $b.abt__ut2_content_bg === "transparent" || $b.abt__ut2_content_bg === "transparent_blur"}
        --ab-banner-mask-background-color: color-mix(in srgb, {$b.abt__ut2_content_bg_color|default: "rgba({$b_color_scheme})"} {$b.abt__ut2_content_bg_opacity}%, rgba({$b_color_scheme},0));
    {/if}
    {if $b.abt__ut2_content_bg === "transparent_gradient"}
        --ab-banner-mask-background-mix-color: color-mix(in srgb, {$b.abt__ut2_content_bg_color|default: "rgba({$b_color_scheme})"} {$b.abt__ut2_content_bg_opacity}%, rgba({$b_color_scheme},0));
        --ab-banner-mask-background-gradient:

        {if $b.abt__ut2_content_bg_align === "auto"}
            {if $b.abt__ut2_content_align === "center" && $b.abt__ut2_content_valign === "center"}
                radial-gradient(ellipse at center center, var(--ab-banner-mask-background-mix-color)20%, rgba({$b_color_scheme},0)80%);
            {else}
                {if $b.abt__ut2_content_full_width === "YesNo::YES"|enum}
                    {if $b.abt__ut2_content_valign === "top"}linear-gradient(-180deg, var(--ab-banner-mask-background-mix-color)0%, rgba({$b_color_scheme},0)100%);{/if}
                    {if $b.abt__ut2_content_valign === "center"}linear-gradient(90deg, var(--ab-banner-mask-background-mix-color)0%, rgba({$b_color_scheme},0)100%);{/if}
                    {if $b.abt__ut2_content_valign === "bottom"}linear-gradient(0deg, var(--ab-banner-mask-background-mix-color)0%, rgba({$b_color_scheme},0)100%);{/if}
                {else}
                    {if $b.abt__ut2_content_align === "left"}
                        linear-gradient(90deg, var(--ab-banner-mask-background-mix-color)0%, rgba({$b_color_scheme},0)100%);
                    {/if}
                    {if $b.abt__ut2_content_align === "center"}
                        {if $b.abt__ut2_content_valign === "top"}linear-gradient(-180deg, var(--ab-banner-mask-background-mix-color)0%, rgba({$b_color_scheme},0)100%);{/if}
                        {if $b.abt__ut2_content_valign === "center"}linear-gradient(90deg, var(--ab-banner-mask-background-mix-color)0%, rgba({$b_color_scheme},0)100%);{/if}
                        {if $b.abt__ut2_content_valign === "bottom"}linear-gradient(0deg, var(--ab-banner-mask-background-mix-color)0%, rgba({$b_color_scheme},0)100%);{/if}
                    {/if}
                    {if $b.abt__ut2_content_align === "right"}
                        linear-gradient(-90deg, var(--ab-banner-mask-background-mix-color)0%, rgba({$b_color_scheme},0)100%);
                    {/if}
                {/if}
            {/if}
        {/if}
        {if $b.abt__ut2_content_bg_align === "left_to_right"}
            linear-gradient(90deg, var(--ab-banner-mask-background-mix-color)30%, rgba({$b_color_scheme},0)100%);
        {/if}
        {if $b.abt__ut2_content_bg_align === "right_to_left"}
            linear-gradient(-90deg, var(--ab-banner-mask-background-mix-color)30%, rgba({$b_color_scheme},0)100%);
        {/if}
        {if $b.abt__ut2_content_bg_align === "top_to_bottom"}
            linear-gradient(-180deg, var(--ab-banner-mask-background-mix-color)30%, rgba({$b_color_scheme},0)70%);
        {/if}
        {if $b.abt__ut2_content_bg_align === "bottom_to_top"}
            linear-gradient(0deg, var(--ab-banner-mask-background-mix-color)30%, rgba({$b_color_scheme},0)70%);
        {/if}
        {if $b.abt__ut2_content_bg_align === "center"}
            radial-gradient(ellipse at center center, var(--ab-banner-mask-background-mix-color)50%, rgba({$b_color_scheme},0)70%);
        {/if}
    {/if}

    {if strlen(trim($b.abt__ut2_title))}
        --ab-banner-title-size: {$b.abt__ut2_title_font_size};
        --ab-banner-title-color: {if $b.abt__ut2_title_color_use === "YesNo::YES"|enum}{$b.abt__ut2_title_color|default:black}{elseif $b.abt__ut2_color_scheme === "dark"}white{else}black{/if};
    {/if}

    {if $b.abt__ut2_description_font_size}--ab-banner-description-size: {$b.abt__ut2_description_font_size};{/if}
    {if $b.abt__ut2_description_color_use === "YesNo::YES"|enum}--ab-banner-description-color: {$b.abt__ut2_description_color|default:black};{elseif $b.abt__ut2_color_scheme === "dark"}--ab-banner-description-color: white;{/if}
    {if $b.abt__ut2_description_bg_color_use === "YesNo::YES"|enum}--ab-banner-description-bg: {$b.abt__ut2_description_bg_color|default:black};{/if}

    {if $b.abt__ut2_button_text_color_use === "YesNo::YES"|enum}--ab-banner-button-text-color: {$b.abt__ut2_button_text_color|default: white};{/if}
    {if $b.abt__ut2_button_color_use === "YesNo::YES"|enum}--ab-banner-button-background-color: {$b.abt__ut2_button_color};{/if}

    {if $b.abt__ut2_content_valign}--ab-banner-v-align: {$b.abt__ut2_content_valign};{/if}
    {if $b.abt__ut2_content_align}--ab-banner-g-align: {$b.abt__ut2_content_align};{/if}

    {if $settings.ab__device !== "desktop" && $block.properties.height_mobile}
        --ab-banner-height: {$block.properties.height_mobile|default: auto};
    {else}
        --ab-banner-height: {$block.properties.height|default: auto};
    {/if}

    {if !empty($b.abt__ut2_padding)}--ab-banner-content-padding: {$b.abt__ut2_padding};{/if}

    {if $block.properties.margin|trim}--ab-banner-block-margin: {$block.properties.margin};{/if}

{/strip}">
    {if $b.abt__ut2_background_type === "image"}
        {* set Background-vars *}
        {if $settings.abt__ut2.general.lazy_load === "YesNo::YES"|enum}
            {$data_backgroud_url = $b.abt__ut2_background_image.icon.image_path}
        {else}
            {$background_url = $b.abt__ut2_background_image.icon.image_path}
        {/if}

        {if $b.abt__ut2_background_image.icon.is_high_res}
            {$original_image_w = $b.abt__ut2_background_image.icon.image_x}
            {$original_image_h = $b.abt__ut2_background_image.icon.image_y }
            {$cropped = fn_image_to_display($b.abt__ut2_background_image, $original_image_w, $original_image_h)}

            {$url_1 = "url(`$cropped.image_path`) 1x"}
            {$url_2 = "url(`$b.abt__ut2_background_image.icon.image_path`) 2x"}

            {$background_url="`$cropped.image_path`'); background-image: image-set($url_1, $url_2);" scope="parent"}
            {$data_backgroud_url = "" scope="parent"}
        {/if}
    {/if}
    {hook name="abt__ut2_banner:banner"}{/hook}

    {if $b.abt__ut2_button_use === "YesNo::NO"|enum && $b.abt__ut2_url|trim && $b.abt__ut2_object !== 'products'}
        <a {if $b.abt__ut2_object === 'video' && $b.abt__ut2_youtube_id}data-content="video"{/if} href="{$b.abt__ut2_url|fn_url}"{if $b.abt__ut2_how_to_open === 'in_new_window'} target="_blank"{/if} title="">
    {/if}

    <div class="{strip}ut2-a__bg-banner
        {if $b.abt__ut2_object === 'products' && $b.products} ut2-a__products-banner{/if}
        {if $b.abt__ut2_background_color === '#ffffff' && $b.abt__ut2_background_color_use === "YesNo::YES"|enum} white-bg{/if}
        {if $b.abt__ut2_content_bg_position === "full_height"} mask-full-height{/if}
        {if $b.abt__ut2_content_bg_position === "whole_banner"} mask-whole-banner{/if}
        {if $b.abt__ut2_content_bg === "transparent_blur"} blur{/if}
        {if $data_backgroud_url} lazyload{/if} {$b.abt__ut2_color_scheme}{/strip}"
         data-id="{$b.banner_id}" {if $data_backgroud_url}data-background-url="{$data_backgroud_url}"{/if}{if $background_url} style="background-image: url('{$background_url}');"{/if}>

        {if $b.abt__ut2_background_type === "mp4_video" && $b.abt__ut2_background_mp4_video}
            <video class="ut2-banner__video" src="{$config.origin_https_location}/images/{$b.abt__ut2_background_mp4_video}" muted loop playsinline></video>
        {/if}

        <div class="{strip}ut2-a__content valign-{$b.abt__ut2_content_valign} align-{$b.abt__ut2_content_align}
            {if $b.abt__ut2_content_full_width === "YesNo::YES"|enum} width-full{else} width-half{/if}
            {if $b.abt__ut2_object === 'image' && $b.abt__ut2_main_image.icon.image_path} internal-image{/if}
            {if $b.abt__ut2_object === 'video' && $b.abt__ut2_youtube_id} internal-image internal-video{/if}
            {if $b.abt__ut2_object === 'products' && $b.products} internal-products{/if}{/strip}">

            {if $b.abt__ut2_object === 'image' && $b.abt__ut2_main_image.icon.image_path}
                <div class="ut2-a__img {if $b.abt__ut2_image_position}vp--{$b.abt__ut2_image_position}{/if}">
                    {include file="common/image.tpl" images=$b.abt__ut2_main_image.icon}
                </div>
            {elseif $b.abt__ut2_object === 'video' && $b.abt__ut2_youtube_id}
                <div class="ut2-a__img ut2-a__video {if $b.abt__ut2_image_position}vp--{$b.abt__ut2_image_position}{/if}" {if $block.properties.height || $block.properties.height_mobile}style="height: {if $settings.ab__device === "mobile"}100%{else}{$block.properties.height|default: 0}{/if}"{/if}
                     data-banner-youtube-id="{$b.abt__ut2_youtube_id}"
                     data-is-autoplay="{$b.abt__ut2_youtube_autoplay}"
                     data-banner-youtube-params="{$b|fn_abt__ut2_build_youtube_link:true}">

                    <img data-type="youtube-img"
                         {if $settings.abt__ut2.general.lazy_load === "YesNo::YES"|enum}src="{$smarty.const.ABT__UT2_LAZY_IMAGE}"
                         class="lazyload"
                         data-{/if}src="https://img.youtube.com/vi/{$b.abt__ut2_youtube_id}/hqdefault.jpg"
                         alt="{$b.abt__ut2_title|strip_tags}">

                </div>
            {elseif $b.abt__ut2_object === 'products' && $b.products}
                <div class="ut2-a__img ut2-a__products {if $b.abt__ut2_image_position}vp--{$b.abt__ut2_image_position}{/if}">
                    {include file="addons/abt__unitheme2/blocks/components/abt__ut2_banner_products.tpl" banner=$b}
                </div>
            {/if}

            {if strlen(trim($b.abt__ut2_title)) || strlen(trim($b.abt__ut2_description)) || $b.abt__ut2_button_use === "YesNo::YES"|enum && $b.abt__ut2_url|trim}
                <div class="ut2-a__description{if $b.abt__ut2_content_bg !== "none" && $b.abt__ut2_content_bg_position === "only_under_content"} mask-under-content{if $b.abt__ut2_color_scheme === "dark"} dark{else} light{/if}{if $b.abt__ut2_content_bg === "transparent_blur"} blur{/if}{/if}">
                    <div class="box">

                        <{$b.abt__ut2_title_tag|default:"div"} class="ut2-a__title{if $b.abt__ut2_title_shadow === "YesNo::YES"|enum} shadow{/if} weight-{$b.abt__ut2_title_font_weight}">
                            {$b.abt__ut2_title nofilter}
                        </{$b.abt__ut2_title_tag|default:"div"}>

                        {if $b.abt__ut2_description}
                            <div class="ut2-a__descr {if $b.abt__ut2_description_bg_color_use === "YesNo::YES"|enum && strlen(trim($b.abt__ut2_description_bg_color))}marked{/if}">
                                {$b.abt__ut2_description nofilter}
                            </div>
                        {/if}

                        {if $b.abt__ut2_button_use === "YesNo::YES"|enum && $b.abt__ut2_url|trim && strpos($b.abt__ut2_class, "b--button-position-bottom") === false}
                            <div class="ut2-a__button">
                                <a class="{strip}ty-btn ty-btn{if $b.abt__ut2_button_style === "normal"}__primary{if $b.abt__ut2_button_text_color_use === "YesNo::YES"|enum} --tc{/if}{if $b.abt__ut2_button_color_use === "YesNo::YES"|enum} --bc{/if}{/if}
                                {if $b.abt__ut2_button_style === "outline"}__outline{if $b.abt__ut2_button_text_color_use === "YesNo::YES"|enum} --tc{/if}
                                {if $b.abt__ut2_button_color_use === "YesNo::YES"|enum} --bc{/if}{/if}
                                {if $b.abt__ut2_button_style === "text"}__text{if $b.abt__ut2_button_text_color_use === "YesNo::YES"|enum} --tc{/if}{if $b.abt__ut2_button_color_use === "YesNo::YES"|enum} --bc{/if}{/if}
                                {/strip}" href="{$b.abt__ut2_url|fn_url}"{if $b.abt__ut2_how_to_open === 'in_new_window'} target="_blank"{/if}>{$b.abt__ut2_button_text|default:"button"}</a>
                            </div>
                        {/if}
                    </div>
                </div>
                {if strpos($b.abt__ut2_class, "b--button-position-bottom") !== false}
                    {if $b.abt__ut2_button_use === "YesNo::YES"|enum && $b.abt__ut2_url|trim}
                        <div class="ut2-a__button">
                            <a class="{strip}ty-btn ty-btn{if $b.abt__ut2_button_style === "normal"}__primary
                                {if $b.abt__ut2_button_text_color_use === "YesNo::YES"|enum} --tc{/if}
                                {if $b.abt__ut2_button_color_use === "YesNo::YES"|enum} --bc{/if}{/if}
                                {if $b.abt__ut2_button_style === "outline"}__outline{if $b.abt__ut2_button_text_color_use === "YesNo::YES"|enum} --tc{/if}
                                {if $b.abt__ut2_button_color_use === "YesNo::YES"|enum} --bc{/if}{/if}
                                {if $b.abt__ut2_button_style === "text"}__text{if $b.abt__ut2_button_text_color_use === "YesNo::YES"|enum} --tc{/if}{if $b.abt__ut2_button_color_use === "YesNo::YES"|enum} --bc{/if}{/if}
                                {/strip}" href="{$b.abt__ut2_url|fn_url}"{if $b.abt__ut2_how_to_open === 'in_new_window'} target="_blank"{/if}>{$b.abt__ut2_button_text|default:"button"}</a>
                        </div>
                    {/if}
                {/if}
            {/if}

        </div>
    </div>

    {if $b.abt__ut2_button_use === "YesNo::NO"|enum && $b.abt__ut2_url|trim && $b.abt__ut2_object !== 'products'}
        </a>
    {/if}
</div>
{/hook}
