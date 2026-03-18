{$tmpl='small_items'}

{assign var="thumbnail_width" value=$block.properties.thumbnail_width|default: 100}
{assign var="thumbnail_height" value=$block.properties.thumbnail_height|default: 100}
{assign var="show_price" value=true}
{assign var="show_old_price" value=true}
{assign var="show_clean_price" value=true}
{assign var='show_list_discount' value=$settings.abt__ut2.product_list.show_you_save[$settings.ab__device] !== "none"}
{assign var="show_product_labels" value=true}
{assign var="show_discount_label" value=true}
{assign var="show_shipping_label" value=false}
{assign var='show_amount_label' value=false}
{assign var="hide_qty_label" value=true}
{assign var="show_sku" value=$settings.abt__ut2.product_list.$tmpl.show_sku[$settings.ab__device] === "YesNo::YES"|enum}
{assign var="show_product_amount" value=$settings.abt__ut2.product_list.$tmpl.show_amount[$settings.ab__device] === "YesNo::YES"|enum}
{assign var="show_qty" value=$settings.abt__ut2.product_list.$tmpl.show_qty[$settings.ab__device] === "YesNo::YES"|enum}

{$button_type_add_to_cart = $settings.abt__ut2.product_list.$tmpl.show_button_add_to_cart[$settings.ab__device]}
{$show_labels_in_title = false}

{if $products_scroller}
{$id="simple_products_scroller_{$obj_prefix}"}
<div class="ut2-scroll-container{if $rows !== "0"} one-row{else} multi-row{/if}" id="{$id}"><button class="ut2-scroll-left" type="button"><span class="ut2-icon-arrow_back_black"></span></button>
{/if}

<ul class="ut2-template-small{if $products_scroller} ut2-scroll-content{/if}" style="--si-lines-in-name-product: {$settings.abt__ut2.product_list.$tmpl.lines_number_in_name_product[$settings.ab__device]};--si-product-image-size: {$thumbnail_width}px;--si-columns: {$columns|default:1};">
{foreach from=$products item="product" name="products"}
    {assign var="obj_id" value=$product.product_id}
    {assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
    {include file="common/product_data.tpl" product=$product show_labels_in_title=$show_labels_in_title}
    {assign var="name" value="name_$obj_id"}

    {hook name="products:product_small_item"}
    <li class="ut2-template-small__item clearfix{if $products_scroller} ut2-scroll-item{/if}">
        {assign var="form_open" value="form_open_`$obj_id`"}
        {$smarty.capture.$form_open nofilter}

        <div class="ut2-template-small__item-img">
            {assign var="product_labels" value="product_labels_`$obj_prefix``$obj_id`"}
            {$smarty.capture.$product_labels nofilter}

            <a href="{"products.view?product_id=`$product.product_id`"|fn_url}" title="{$product.product}">{include file="common/image.tpl" image_width=$thumbnail_width image_height=$thumbnail_height images=$product.main_pair obj_id=$obj_id_prefix no_ids=true}</a>
        </div>

        {if $block.properties.item_number == "YesNo::YES"|enum}<span class="ut2-hit">{$smarty.foreach.products.iteration}</span>{/if}

        <div class="ut2-template-small__item-description">

                {$smarty.capture.$name nofilter}

                {if $product.product_code}
                    {assign var="sku" value="sku_$obj_id"}
                    {$smarty.capture.$sku nofilter}
                {/if}

                {include file="blocks/product_list_templates/components/average_rating.tpl" meta="" show_label_in_title=""}

                {assign var="product_amount" value="product_amount_`$obj_id`"}
                {$smarty.capture.$product_amount nofilter}

                {capture name="small_items_list_control_data_wrapper"}
                    {if $show_add_to_cart && $button_type_add_to_cart != 'none'}

                        {assign var="qty" value="qty_`$obj_id`"}

                        <div class="ut2-template-small__control {if $settings.abt__ut2.product_list.$tmpl.show_qty[$settings.ab__device] === "YesNo::YES"|enum && $smarty.capture.$qty|strip_tags:false|replace:"&nbsp;":""|trim|strlen} ut2-view-qty{/if}{if $button_type_add_to_cart != 'none'} {$button_type_add_to_cart}{/if}">

                            {capture name="small_items_list_control_data"}
                                {hook name="products:small_items_list_control"}
                                {$add_to_cart = "add_to_cart_`$obj_id`"}
                                {$smarty.capture.$add_to_cart nofilter}

                                {if $show_qty && $smarty.capture.$qty|strip_tags:false|replace:"&nbsp;":""|trim|strlen}
                                    {$smarty.capture.$qty nofilter}
                                {/if}
                                {/hook}
                            {/capture}
                            {$smarty.capture.small_items_list_control_data nofilter}
                        </div>
                    {/if}
                {/capture}

                {if $button_type_add_to_cart == 'icon' || $button_type_add_to_cart == 'icon_button'}
                    <div class="ut2-template-small__mix-price-and-button {if $show_qty}qty-wrap{/if}">
                {/if}

                {if $show_price}
                    <div class="ut2-template-small__item-price pr-{$settings.abt__ut2.product_list.price_display_format}{if $product.list_discount || $product.discount} pr-color{/if}">
                        <div>
                            {assign var="price" value="price_`$obj_id`"}
                            {$smarty.capture.$price nofilter}

                            {if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "short"}<span>{/if}
                            {assign var="old_price" value="old_price_`$obj_id`"}
                            {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}

                            {assign var="list_discount" value="list_discount_`$obj_id`"}
                            {$smarty.capture.$list_discount nofilter}
                            {if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "short"}</span>{/if}
                        </div>
                        {assign var="clean_price" value="clean_price_`$obj_id`"}
                        {$smarty.capture.$clean_price nofilter}
                    </div>
                {/if}

                {if $smarty.capture.small_items_list_control_data|trim}
                    {$smarty.capture.small_items_list_control_data_wrapper nofilter}
                {/if}

                {if $button_type_add_to_cart == 'icon' || $button_type_add_to_cart == 'icon_button'}
                     </div>
                {/if}
            </div>

        {hook name="products:product_list_form_close_tag"}
            {assign var="form_close" value="form_close_`$obj_id`"}
            {$smarty.capture.$form_close nofilter}
        {/hook}
    </li>
    {/hook}
{/foreach}
</ul>

{if $products_scroller}<button class="ut2-scroll-right" type="button"><span class="ut2-icon-arrow_forward_black"></span></button>
</div>
{include file="common/simple_scroller_init.tpl" block_id=$id elements_to_scroll=$elements_to_scroll}
{/if}