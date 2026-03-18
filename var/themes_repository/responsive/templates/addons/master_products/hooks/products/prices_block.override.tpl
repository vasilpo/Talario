{if !$product.company_id && !$product.master_product_id}
{strip}
    {if $product.price|floatval || $product.zero_price_action == "P" || ($hide_add_to_cart_button == "Y" && $product.zero_price_action == "A")}
        {$show_other_offers_link = $show_other_offers_link|default:true}
        <span class="ty-master-products-products-prices-block__price ty-price{if !$product.price|floatval && !$product.zero_price_action} hidden{/if}" {""}
                id="line_discounted_price_{$obj_prefix}{$obj_id}"
        >
            {include file="common/price.tpl"
                value=$product.price
                span_id="discounted_price_`$obj_prefix``$obj_id`"
                class="ty-price-num"
                live_editor_name="product:price:{$product.product_id}"
                live_editor_phrase=$product.base_price
            }
        </span>

        {capture name="price_only_secondary_`$obj_id`" assign="price_only_secondary_`$obj_id`"}
            <span class="{if $product.zero_price_action !== "A"}cm-reload-{$obj_prefix}{$obj_id}{/if} ty-price-update" id="price_update_secondary_{$obj_prefix}{$obj_id}">
                <span class="ty-master-products-products-prices-block__price ty-price{if !$product.price|floatval && !$product.zero_price_action} hidden{/if}" {""}
                        id="line_discounted_price_{$obj_prefix}{$obj_id}"
                >
                    {include file="common/price.tpl"
                        value=$product.price
                        span_id="discounted_price_`$obj_prefix``$obj_id`"
                        class="ty-price-num"
                        live_editor_name="product:price:{$product.product_id}"
                        live_editor_phrase=$product.base_price
                    }
                </span>
            <!--price_update_secondary_{$obj_prefix}{$obj_id}--></span>
        {/capture}

        {* Export *}
        {$price_only_secondary_override_{$obj_id} = $price_only_secondary_{$obj_id} scope=parent}
        {* /Export *}

        {if $show_other_offers_link}
            <span class="ty-master-products-products-prices-block__other-offers">
                {$other_offers_link = "products.view&product_id=`$product.product_id`"|fn_url|cat:"#tabs_content"}
                {$other_offers_href = (!$details_page || $quick_view) ? "href=\"`$other_offers_link`\"" : ""}

                (<a class="cm-scroll" data-ca-scroll="#tabs_content" {$other_offers_href nofilter}>{__("master_products.and_other_offers")}</a>)
            </span>
        {/if}
    {/if}
{/strip}
{/if}
