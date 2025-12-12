{* block-description:tmpl_scroller *}
{$tmpl='products_scroller'}

{if $settings.Appearance.enable_quick_view == "YesNo::YES"|enum && $block.properties.enable_quick_view == "YesNo::YES"|enum && $settings.ab__device !== "mobile" || $settings.Appearance.enable_quick_view == "YesNo::YES"|enum && $settings.abt__ut2.product_list.$tmpl.show_quick_view_button[$settings.ab__device] === "YesNo::YES"|enum}
    {$quick_nav_ids = $items|fn_fields_from_multi_level:"product_id":"product_id"}
{/if}

{if $block.properties.hide_add_to_cart_button == "Y"}
    {$_show_add_to_cart=false}
{else}
    {$_show_add_to_cart=true}
{/if}
{if $block.properties.show_price == "Y"}
    {$_hide_price=false}
{else}
    {$_hide_price=true}
{/if}

{if !empty($block.properties.thumbnail_width)}
    {assign var="tbw" value=$block.properties.thumbnail_width}
    {assign var="tbh" value=$block.properties.thumbnail_width}
{else}
    {assign var="tbw" value=$settings.Thumbnails.product_lists_thumbnail_width}
    {assign var="tbh" value=$settings.Thumbnails.product_lists_thumbnail_height}
{/if}

{$show_old_price = true}
{$show_product_amount = $show_product_amount|default:false}
{$show_list_discount = $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] !== "none"}
{$show_labels_in_title = false}

{$obj_prefix="`$block.block_id`000"}
{$block.block_id = "{$block.block_id}_{uniqid()}"}
{$item_quantity = $block.properties.item_quantity|default:5}
{$item_quantity_sm_desktop = $block.properties.item_quantity_sm_desktop|default:4}
{$item_quantity_tablet = $block.properties.item_quantity_tablet|default:4}

{if $block.properties.item_quantity_tablet < 5}
    {$item_quantity_sm_tablet = $block.properties.item_quantity_tablet}
{else}
    {$item_quantity_sm_tablet = 3}
{/if}

{if $block.properties.item_quantity_mobile < 4}
    {$item_quantity_mobile = $block.properties.item_quantity_mobile}
{else}
    {$item_quantity_mobile = 2}
{/if}

{if $settings.ab__device === "desktop"}
    {$hide_quick_view_button = $block.properties.enable_quick_view === "YesNo::NO"|enum}
{else}
    {$hide_quick_view_button = true}
{/if}

{if $block.properties.outside_navigation == "YesNo::YES"|enum}
    <div class="owl-theme ty-owl-controls">
        <div class="owl-controls clickable owl-controls-outside"  id="owl_outside_nav_{$block.block_id}">
            <div class="owl-buttons">
                <div id="owl_prev_{$obj_prefix}" class="owl-prev">{include_ext file="common/icon.tpl" class="ut2-icon-arrow_back_black"}</div>
                <div id="owl_next_{$obj_prefix}" class="owl-next">{include_ext file="common/icon.tpl" class="ut2-icon-arrow_forward_black"}</div>
            </div>
        </div>
    </div>
{/if}

<div id="scroll_list_{$block.block_id}" class="owl-carousel ty-scroller-list ty-scroller active-scroll">

    {foreach from=$items item="product" name="for_products"}
        {hook name="products:product_scroller_list"}
            <div class="ty-scroller-list__item ty-scroller__item">
                {hook name="products:product_scroller_list_item"}
                {$obj_id="scr_`$block.block_id`000`$product.product_id`"}
                    <div class="ty-scroller-list__img-block">
                        {include file="common/image.tpl" assign="object_img" images=$product.main_pair image_width=$tbw image_height=$tbh no_ids=true}
                        <a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{$object_img nofilter}</a>
                    </div>
                    <div class="ty-scroller-list__description" style="--scroller-list-img-height: {$tbh}px">
                        {strip}
                            {include file="blocks/list_templates/simple_list.tpl"
                            product=$product
                            show_name=true
                            show_price=true
                            show_add_to_cart=$_show_add_to_cart
                            but_role="action"
                            hide_price=$_hide_price
                            hide_qty=true
                            show_product_labels=true
                            show_discount_label=true
                            show_shipping_label=true
                            hide_quick_view_button=$hide_quick_view_button
                            }
                        {/strip}
                    </div>
                {/hook}
            </div>
        {/hook}
    {/foreach}
</div>

{include file="common/scroller_init.tpl" prev_selector="#owl_prev_`$obj_prefix`" next_selector="#owl_next_`$obj_prefix`" block=$block
itemsDesktop=$item_quantity
itemsDesktopSmall=$item_quantity_sm_desktop
itemsTablet=$item_quantity_tablet
itemsTabletSmall=$item_quantity_sm_tablet
itemsMobile=$item_quantity_mobile
}