{** Detecting grid item height **}

{* Grid padding *}
{assign var="pd" value=1}

{if $settings.ab__device === "mobile"}{assign var="mc" value=1.075}{else}{assign var="mc" value=1}{/if}

{* Thumb *}
{if !empty($block.properties.abt__ut2_thumbnail_height)}
    {assign var="t1" value=$block.properties.abt__ut2_thumbnail_height}
{else}
    {assign var="t1" value=$settings.abt__ut2.product_list.$tmpl.image_height[$settings.ab__device]|default:$settings.Thumbnails.product_lists_thumbnail_height|intval}
{/if}

{* Show rating *}
{if $show_rating}
    {assign var="t2" value=($mc * 20)}
{/if}

{* Show sku *}
{if $settings.abt__ut2.product_list.$tmpl.show_sku[$settings.ab__device] === "YesNo::YES"|enum}
    {assign var="t3" value=($mc * 20)}
{/if}

{* Show name *}
{assign var="nl" value=($settings.abt__ut2.product_list.$tmpl.lines_number_in_name_product[$settings.ab__device] * 16.5)}
{assign var="t4" value=($mc * ($nl + 5))}

{* Show amount *}
{if $settings.abt__ut2.product_list.$tmpl.show_amount[$settings.ab__device] === "YesNo::YES"|enum}
    {assign var="t5" value=($mc * 20)}
{/if}

{* Show price *}
{if $settings.abt__ut2.product_list.price_position_top == "YesNo::YES"|enum}
    {assign var="t6" value=($mc * 50)}
{else}
    {assign var="t6" value=($mc * 46)}
{/if}

{* Show buttons *}
{if $show_add_to_cart}
    {if $settings.abt__ut2.product_list.$tmpl.show_buttons_on_hover[$settings.ab__device] === "YesNo::NO"|enum && ($button_type_add_to_cart === 'icon_and_text' || $button_type_add_to_cart === 'text')}
        {assign var="t7" value=46}
    {else}
        {if $settings.abt__ut2.product_list.$tmpl.show_qty[$settings.ab__device] === "YesNo::YES"|enum && $settings.ab__device !== "desktop"}
            {assign var="t7" value=46}
        {else}
            {assign var="t7" value=0}
        {/if}
    {/if}
{elseif !empty($block.properties.hide_add_to_cart_button) && $block.properties.hide_add_to_cart_button === "YesNo::NO"|enum}
    {assign var="t7" value=36}
{/if}

{* Show your save *}
{if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "full"}
    {assign var="t8" value=($mc * 28)}
{/if}

{* Show tax *}
{if $settings.Appearance.show_prices_taxed_clean === "YesNo::YES"|enum}
    {assign var="t9" value=($mc * 16)}
{/if}

{hook name="products:ut2__grid_list_settings"}{/hook}

{if empty($block.properties) && $settings.abt__ut2.product_list.$tmpl.show_content_on_hover[$settings.ab__device] === "YesNo::NO"|enum}
    {* Show features *}
    {if $settings.abt__ut2.product_list.$tmpl.grid_item_bottom_content[$settings.ab__device] === "features"
    || $settings.abt__ut2.product_list.$tmpl.grid_item_bottom_content[$settings.ab__device] === "features_and_description"
    || $settings.abt__ut2.product_list.$tmpl.grid_item_bottom_content[$settings.ab__device] === "features_and_variations"}
        {assign var="t10" value=$settings.abt__ut2.product_list.max_features[$settings.ab__device]*($mc * 20)}
        {$fth = $t10|default:0}
    {/if}

    {* Show s.description *}
    {if $settings.abt__ut2.product_list.$tmpl.grid_item_bottom_content[$settings.ab__device] === "description"
    || $settings.abt__ut2.product_list.$tmpl.grid_item_bottom_content[$settings.ab__device] === "features_and_description"}
        {assign var="t11" value=(($mc * 50) + 10)}
    {/if}

    {* Show variations *}
    {if $settings.abt__ut2.product_list.$tmpl.grid_item_bottom_content[$settings.ab__device] === "variations"
    || $settings.abt__ut2.product_list.$tmpl.grid_item_bottom_content[$settings.ab__device] === "features_and_variations"}
        {assign var="t12" value=($mc * 0)}
    {/if}
{/if}
{capture name="abt__ut2_gl_features_height"}{$fth|default:0}{/capture}

{* Show color separately *}
{if $settings.abt__ut2.product_list.price_position_top == "YesNo::YES"|enum && $settings.abt__ut2.product_list.product_variations.display_color_separately === 'color'}
    {assign var="t13" value=($mc * 26)}
{/if}

{* ut2-gl__price height size*}
{$pth = $t6|default:0 + $t8|default:0 + $t9|default:0}
{capture name="abt__ut2_pr_block_height"}{$pth}{/capture}

{* ut2-gl__content height size *}
{if $settings.abt__ut2.product_list.price_position_top === "YesNo::YES"|enum}
    {$thc = $t2|default:0 + $t3|default:0 + $t4|default:0 + $t5|default:0 + $t7|default:0 + $t10|default:0 + $t11|default:0 + $t12|default:0 + $t13|default:0 + $pd}
{else}
    {$thc = $t2|default:0 + $t3|default:0 + $t4|default:0 + $t5|default:0 + $t7|default:0 + $t10|default:0 + $t11|default:0 + $t12|default:0 + $t13|default:0 + $pth + $pd}
{/if}
{capture name="abt__ut2_gl_content_height"}{$thc}{/capture}

{* ut2-gl__item height size *}
{hook name="products:product_multicolumns_list_item_height"}
{$th = $t1|default:0 + $thc + $pd}
{/hook}
{capture name="abt__ut2_gl_item_height"}{$th}{/capture}
