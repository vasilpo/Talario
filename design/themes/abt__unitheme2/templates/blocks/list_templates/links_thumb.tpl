{* block-description:tmpl_links_thumb *}
{$tmpl='links_thumb'}

{$id="simple_products_scroller_{$obj_prefix}"}
{$button_type_add_to_cart = text}

{if $products_scroller}
<div class="ut2-scroll-container{if $rows !== "0"} one-row{else} multi-row{/if}" id="{$id}">
<button class="ut2-scroll-left" type="button"><span class="ut2-icon-arrow_back_black"></span></button>{/if}

    <div class="ut2-thumbnail-list{if $products_scroller} ut2-scroll-content{/if}" style="--tls-pr-count: {$columns|default:10}">
        {foreach from=$products item="product" name="products"}
            {assign var="obj_id" value=$product.product_id}
            {assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
            {include file="common/product_data.tpl" product=$product hide_links=$hide_links}

            {hook name="products:product_thumbnail_list"}
            <div class="ut2-thumbnail-list__item {if $products_scroller}ut2-scroll-item{/if}">
                {assign var="form_open" value="form_open_`$obj_id`"}
                {assign var="name" value="name_$obj_id"}
                {$smarty.capture.$form_open nofilter}

                <a class="ut2-thumbnail-list__img-block {if $products_scroller}cm-tooltip{/if}" {if $products_scroller}title="{$smarty.capture.$name}"{/if} href="{"products.view?product_id=`$product.product_id`"|fn_url}">{include file="common/image.tpl" image_height="100" images=$product.main_pair obj_id=$obj_id_prefix no_ids=true class="ty-thumbnail-list__img"}{if !$products_scroller}</a>{/if}

                {if !$products_scroller}
                    <div class="ut2-thumbnail-list__name">{if $block.properties.item_number == "Y"}{$smarty.foreach.products.iteration}.&nbsp;{/if}
                    <bdi>{$smarty.capture.$name nofilter}</bdi></div>
                {/if}

                {if $show_price}
                    <span class="ut2-template-small__item-price pr-{$settings.abt__ut2.product_list.price_display_format}{if $product.list_discount || $product.discount} pr-color{/if}">
                        <span>
                            {assign var="price" value="price_`$obj_id`"}
                            {$smarty.capture.$price nofilter}

                            {if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "short"}<span>{/if}
                                {assign var="old_price" value="old_price_`$obj_id`"}
                                {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}

                                {assign var="list_discount" value="list_discount_`$obj_id`"}
                                {$smarty.capture.$list_discount nofilter}
                                {if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "short"}</span>{/if}
                        </span>
                        {assign var="clean_price" value="clean_price_`$obj_id`"}
                        {$smarty.capture.$clean_price nofilter}
                    </span>
                {/if}

                {if $products_scroller}</a>{/if}

                {if $show_add_to_cart}
                    <div class="ut2-thumbnail-list__butons">
                        {assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
                        {$smarty.capture.$add_to_cart nofilter}
                    </div>
                {/if}

                {hook name="products:product_list_form_close_tag"}
                    {$form_close="form_close_`$obj_id`"}
                    {$smarty.capture.$form_close nofilter}
                {/hook}
            </div>
            {/hook}
        {/foreach}
    </div>

{if $products_scroller}<button class="ut2-scroll-right" type="button"><span class="ut2-icon-arrow_forward_black"></span></button>
</div>
{include file="common/simple_scroller_init.tpl" block_id=$id elements_to_scroll=$elements_to_scroll}
{/if}

