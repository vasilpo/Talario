{** block-description:product_variations.variations_list **}

{if $items}
    {if $block.properties.hide_add_to_cart_button == "YesNo::YES"|enum}
        {$_show_add_to_cart=false}
    {else}
        {$_show_add_to_cart=true}
    {/if}
    {if $block.properties["product_variations.hide_add_to_wishlist_button"] == "YesNo::YES"|enum}
        {$_show_add_to_wishlist=false}
    {else}
        {$_show_add_to_wishlist=true}
    {/if}

    {$products=$items}
    {$obj_prefix="`$block.block_id`000"}
    {$show_add_to_wishlist=$_show_add_to_wishlist|default:true}
    {$show_sku=$block.properties["product_variations.show_product_code"]|default:{"YesNo::YES"|enum} == "YesNo::YES"|enum}
    {$show_variation_thumbnails=$block.properties["product_variations.show_variation_thumbnails"]|default:{"YesNo::YES"|enum} == "YesNo::YES"|enum}
    {$show_price=true}
    {$show_old_price=true}
    {$show_clean_price=true}
    {$show_add_to_cart=$_show_add_to_cart|default:true}
    {$but_role="action"}
    {$hide_form=true}
    {$show_product_amount=$settings.General.inventory_tracking !== "YesNo::NO"|enum}
    {$hide_stock_info=false}
    {$show_out_of_stock=true}
    {$show_amount_label=false}
    {$show_qty=true}
    {$hide_qty_label=true}
    {$show_variations=true}
    {$show_sku_label=false}
    {$image_width=$image_width|default:50}
    {$image_height=$image_height|default:50}

    {hook name="products:product_variations_list_settings"}{/hook}

    {$list_buttons="list_buttons_`$obj_id`"}

    {$smarty.capture.$list_buttons nofilter}

    {if $show_variations}
        {$first_product=reset($products)}
    {/if}

    {script src="js/tygh/exceptions.js"}
    <div class="ut2-variations-list__wrapper">

    {if $settings.ab__device !== "mobile"}
    <div class="ut2-variations-list__style"><input type="checkbox"/>{__("abt__ut2.menu.filling_variants.column_filling")}</div>
    {/if}

    <table class="ut2-variations-list{if $block.properties.hide_add_to_cart_button == "YesNo::NO"|enum} ut2-vl-cart-bt{/if}{if $block.properties["product_variations.show_variation_thumbnails"] == "YesNo::YES"|enum} ut2-vl-var-thumb{/if}" style="--ut2-variations-list-image-width: {$image_width}px;" data-ca-sortable="true" data-ca-sort-list="[[1, 0]]">
        <thead>
            <tr>
                <th class="ut2-variations-list__title cm-tablesorter" data-ca-sortable-column="true" aria-sort="ask">{__("sort_by")} {__("price")}</th>

                {if $show_product_amount}
                    <th class="ut2-variations-list__title cm-tablesorter" data-ca-sortable-column="true">{__("sort_by")} {__("availability")}</th>
                {/if}

                {if $show_sku}
                    <th class="ut2-variations-list__title cm-tablesorter" data-ca-sortable-column="true">{__("sort_by")} {__("sku")}</th>
                {/if}

                {if $show_variations}
                    {foreach $first_product.variation_features as $feature}
                        <th class="ut2-variations-list__title cm-tablesorter" data-ca-sortable-column="true">{__("sort_by")} {$feature.description}</th>
                    {/foreach}
                {/if}
            </tr>

        </thead>
        <tbody>
            {foreach $products as $key => $product name="variations_list"}

                {$variation_link="products.view?product_id={$product.product_id}"|fn_url}
                {$obj_id=$product.product_id}
                {$obj_id_prefix="`$obj_prefix``$product.product_id`"}

                {include file="common/product_data.tpl" product=$product show_select_variations_button=false}

                {hook name="products:product_variations_list"}

                    {$product_amount="product_amount_`$obj_id`"}

                    <tr class="ut2-variations-list__item">

                        <td class="ut2-variations-list__product-elem ut2-variations-list__price">
                            {if $show_variation_thumbnails}
                            <div class="ut2-variations-list__thumb">
                                <a href="{$variation_link}">
                                    {include file="common/image.tpl" image_width=$image_width image_height=$image_height images=$product.main_pair obj_id=$obj_id_prefix}
                                </a>
                            </div>
                            {/if}
                            <div class="ut2-vl__price pr-{$settings.abt__ut2.product_list.price_display_format}{if $product.list_discount || $product.discount} pr-color{/if}">
                                <div>
                                    {assign var="price" value="price_`$obj_id`"}
                                    {$smarty.capture.$price nofilter}

                                    {assign var="old_price" value="old_price_`$obj_id`"}
                                    {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}
                                </div>
                                {assign var="clean_price" value="clean_price_`$obj_id`"}
                                {$smarty.capture.$clean_price nofilter}
                            </div>
                        </td>

                        {if $show_product_amount}
                        <td class="ut2-variations-list__product-elem ut2-variations-list__product-elem-options">

                            {$smarty.capture.$product_amount nofilter}

                            <div class="ut2-variations-list__amount">
                                <form {if !$config.tweaks.disable_dhtml}class="cm-ajax cm-ajax-full-render"{/if} action="{""|fn_url}" method="post" name="variations_list_form{$obj_prefix}">

                                    <input type="hidden" name="result_ids" value="cart_status*,wish_list*,checkout*,account_info*,abt__ut2_wishlist_count" />
                                    <input type="hidden" name="redirect_url" value="{if $smarty.request.redirect_url}{$smarty.request.redirect_url}{else}{$config.current_url}{/if}" />
                                    <input type="hidden" name="product_data[{$obj_id}][product_id]" value="{$obj_id}">

                                    <div class="ut2-vl__control ut2-vl__mix-price-and-button icon_button">
                                        {hook name="variations_list:list_buttons"}
                                        {assign var="qty" value="qty_`$obj_id`"}
                                        {$smarty.capture.$qty nofilter}

                                        {$add_to_cart="add_to_cart_`$obj_id`"}
                                        {$smarty.capture.$add_to_cart nofilter}
                                        {/hook}
                                    </div>
                                </form>

                                {if !$product.company_id && !($product.inventory_amount|default:$product.amount)}
                                <span class="ty-qty-out-of-stock ty-control-group__item" id="out_of_stock_info_{$obj_prefix}{$obj_id}">{__("text_out_of_stock")}</span>
                                {/if}
                            </div>
                        </td>
                        {/if}

                        {if $show_sku && $product.product_code|trim}
                            <td class="ut2-variations-content__product-var ut2-variations-list__sku">
                                {if $settings.ab__device === "mobile"}<div class="label">{__("sku")}:</div>{/if}
                                {$sku = "sku_`$obj_id`"}
                                {$smarty.capture.$sku nofilter}
                            </td>
                        {/if}

                        {foreach $product.variation_features as $feature}
                            <td class="ut2-variations-content__product-var">
                                <div class="label">{$feature.description}:</div>
                                <bdi>
                                    <span class="ty-product-options">
                                        <span class="ty-product-options-content">
                                            {$feature.variant}
                                        </span>
                                    </span>
                                </bdi>
                            </td>
                        {/foreach}
                    </tr>
                {/hook}
            {/foreach}
        </tbody>
    </table>
    </div>
{/if}