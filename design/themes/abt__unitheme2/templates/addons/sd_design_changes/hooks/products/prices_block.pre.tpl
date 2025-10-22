{if $show_price_prefix|default:false && $product.price|floatval || $product.zero_price_action == "P" || ($hide_add_to_cart_button == "YesNo::YES"|enum && $product.zero_price_action == "A")}
    <span class="sd-price-prefix">
        {__("from")}
    </span>
{/if}