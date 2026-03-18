{if $settings.abt__ut2.product_list.product_variations.display_color_separately == 'color'}
    {if $settings.ab__device !="mobile"}
        {$limit = 6}
    {else}
        {$limit = 5}
    {/if}
{elseif $settings.abt__ut2.product_list.product_variations.display_color_separately == 'thumbnails'}
    {if $settings.ab__device !="mobile"}
        {$limit = 5}
    {else}
        {$limit = 4}
    {/if}
{/if}

{$setting_variantions_limit = $settings.abt__ut2.product_list.product_variations.limit}

{if $setting_variantions_limit < $limit}
    {$setting_variantions_limit = $limit}
{/if}

{strip}
    {if $settings.abt__ut2.product_list.product_variations.display_color_separately !== 'dont_display'}

        {$product.variation_features_variants=fn_abt__ut2_prepare_variation_features_variants($product.variation_features_variants, $product.abt__ut2_features, $product.product_id)}

        {$show_block = false}

        {foreach $product.variation_features_variants as $variation_feature}
            {if $variation_feature.display_on_catalog === "YesNo::YES"|enum && $variation_feature.filter_style == 'color' && ($variation_feature.purpose == 'group_variation_catalog_item' || $variation_feature.purpose == 'group_catalog_item')}
                {$show_block = true}
                {break}
            {/if}
        {/foreach}

        {if $show_block && $product.variation_features_variants}
            {$show_as_links = $settings.abt__ut2.product_list.product_variations.display_as_links[$settings.ab__device] === "YesNo::YES"|enum}

            <div class="ut2-lv__features-item lv-hover-items" data-display="{$settings.abt__ut2.product_list.product_variations.display_color_separately}">
                {foreach $product.variation_features_variants as $variation_feature}
                    {$display = ''}
                    {if $variation_feature.display_on_catalog === "YesNo::YES"|enum && $variation_feature.filter_style == 'color' && ($variation_feature.purpose == 'group_variation_catalog_item' || $variation_feature.purpose == 'group_catalog_item')}

                        {foreach $variation_feature.variants as $variant}

                            {if (($variant.product_id || $addons.product_variations.variations_show_all_possible_feature_variants === "YesNo::YES"|enum) && $printed_variations < $setting_variantions_limit)}

                                {if $printed_variations >= $limit}
                                    {$display = 'hidden'}
                                {/if}
                                {$active_class= ($variant.active)?"active":""}
                                {if $settings.abt__ut2.product_list.product_variations.display_color_separately == 'color'}
                                    {$color1 = $variant.color|default:$white_color}
                                    {$color2 = ($variant.abt__ut2_color_style === 'multicolor') ? ($variant.abt__ut2_multicolor|default:$color1) : $color1}
                                    {if $variant.abt__ut2_color_style == 'thumbnail'}
                                        {if $show_as_links && $variant.product_id}
                                            <a href="{fn_url("products.view?product_id=`$variant.product_id`")}"
                                               data-abt--ut2-variations-variant-id="{$variant.product_id}"
                                               class="ut2-lv__color-variant {$active_class} {$display}"
                                               style="background: linear-gradient(120deg,{$color1} 50%, {$color2} 51%);">
                                                {include file="common/image.tpl" images=$variant.image_pair image_width=64 height=64 no_ids=true lazy_load=false}
                                            </a>
                                        {else}
                                            <span class="ut2-lv__color-variant {$active_class} {$display}"
                                                  style="background: linear-gradient(120deg,{$color1} 50%, {$color2} 51%);"
                                                  data-abt--ut2-variations-variant-id="{$variant.product_id}">
                                                {include
                                                file="common/image.tpl"
                                                images=$variant.image_pair
                                                image_width=$settings.Thumbnails.product_variant_mini_icon_width
                                                height=$settings.Thumbnails.product_variant_mini_icon_height
                                                no_ids=true
                                                lazy_load=false}
                                            </span>
                                        {/if}
                                    {else}
                                        {if $show_as_links && $variant.product_id}
                                            <a href="{fn_url("products.view?product_id=`$variant.product_id`")}"
                                               data-abt--ut2-variations-variant-id="{$variant.product_id}"
                                               class="ut2-lv__color-variant {$active_class} {$display}"
                                               style="background: linear-gradient(120deg,{$color1} 50%, {$color2} 51%);">&nbsp;</a>
                                        {else}
                                            <span class="ut2-lv__color-variant {$active_class} {$display}"
                                                  style="background: linear-gradient(120deg,{$color1} 50%, {$color2} 51%);"
                                                  data-abt--ut2-variations-variant-id="{$variant.product_id}">&nbsp;</span>
                                        {/if}
                                    {/if}


                                {elseif $settings.abt__ut2.product_list.product_variations.display_color_separately == 'thumbnails'}
                                    {if $show_as_links && $variant.product_id}
                                        <a href="{fn_url("products.view?product_id=`$variant.product_id`")}"
                                    {else}
                                        <span {/if}
                                    data-abt--ut2-variations-variant-id="{$variant.product_id}"
                                    class="ty-product-options__image--wrapper {$display} {$active_class}">
                                    {include file="common/image.tpl"
                                    obj_id="image_feature_variant_{$feature.feature_id}_{$variant.variant_id}_{$obj_prefix}{$obj_id}"
                                    class="ty-product-options__image"
                                    images=$variant.product.main_pair
                                    image_width=$settings.Thumbnails.product_variant_mini_icon_width
                                    image_height=$settings.Thumbnails.product_variant_mini_icon_height
                                    image_additional_attrs = [
                                    "width" => $settings.Thumbnails.product_variant_mini_icon_width,
                                    "height" => $settings.Thumbnails.product_variant_mini_icon_height
                                    ]
                                    }
                                    {if $show_as_links && $variant.product_id}</a>{else}</span>{/if}
                                {/if}
                            {/if}
                            {$printed_variations = $printed_variations +1}
                        {/foreach}
                        {if $variation_feature.variants|count > $limit}
                            {if $variation_feature.variants|count > $setting_variantions_limit}
                                {$count_more = $setting_variantions_limit - $limit}
                            {else}
                                {$count_more =$variation_feature.variants|count - $limit}
                            {/if}
                            {if $count_more > 0}
                                <span class="ut2-lv__more" data-more="+{$count_more}"></span>
                            {/if}
                        {/if}
                    {/if}
                {/foreach}
            </div>

        {elseif $settings.abt__ut2.product_list.product_variations.display_color_separately === 'color' && $settings.abt__ut2.product_list.price_position_top === "YesNo::NO"|enum}
            <div class="ut2-lv__features-item lv-hover-items" data-display="{$settings.abt__ut2.product_list.product_variations.display_color_separately}"></div>
        {/if}
    {/if}
{/strip}

{foreach $feature.variants as $variant}
    {if $variant.showed_product_id}
        {$variant_product_id = $variant.showed_product_id}
    {else}
        {$variant_product_id = $variant.product.product_id}
    {/if}
    {if $variant_product_id && $variant.product.status}

        {* Change main product image on variation image hover *}
        {if $settings.abt__ut2.general.change_main_image_on_variation_hover.{$settings.ab__device} == "YesNo::YES"|enum}
            {if $quick_view}
                {$image_width=$settings.Thumbnails.product_quick_view_thumbnail_width}
                {$image_height=$settings.Thumbnails.product_quick_view_thumbnail_height}
            {elseif $product.details_layout !='bigpicture_template'}
                {$image_width=$settings.Thumbnails.product_details_thumbnail_width}
                {$image_height=$settings.Thumbnails.product_details_thumbnail_height}
            {/if}
            {include file="common/image.tpl"
            images=$variant.product.main_pair
            capture_image=true
            }
        {/if}
        <a {if $variant.product.amount >= 1 || $allow_negative_amount || $details_page}href="{$product_url|fn_link_attach:"product_id={$variant_product_id}"|fn_url}"{/if}
           class="ty-product-options__image--wrapper {if $variant.product.abt__ut2_is_in_stock < 1 && $settings.abt__ut2.products.highlight_unavailable_variations[$settings.ab__device] == "YesNo::YES"|enum}ty-product-options__image--wrapper--disabled{/if} {if $settings.ab__device=='desktop'}cm-tooltip{/if} {if $variant.variant_id == $feature.variant_id}ty-product-options__image--wrapper--active{/if} {if $feature.purpose === $purpose_create_variations || $quick_view || $ut2_select_variation}cm-ajax {if !$ut2_select_variation}cm-history {/if}cm-ajax-cache{/if}" title="{$feature.prefix} {$variant.variant} {$feature.suffix}"
                {if $feature.purpose === $purpose_create_variations || $quick_view || $ut2_select_variation}data-ca-target-id="{$container}"{/if}
                {if $variant.variant_id != $feature.variant_id}
                    {if $smarty.capture.icon_image_path|trim} data-ca-variation-image="{$smarty.capture.icon_image_path}"{/if}
                    {if $smarty.capture.icon_image_path_hidpi|trim} data-ca-variation-image-hidpi="{$smarty.capture.icon_image_path_hidpi}"{/if}
                {/if}
        ></a>
    {elseif $show_all_possible_feature_variants}
        <label class="ty-product-options__radio--label ty-product-options__radio--label--disabled">
            <span class="ty-product-option-checkbox">{$feature.prefix}</span>
            <bdi>{$variant.variant}</bdi>
            <span class="ty-product-option-checkbox">{$feature.suffix}</span>
        </label>
    {/if}
{/foreach}