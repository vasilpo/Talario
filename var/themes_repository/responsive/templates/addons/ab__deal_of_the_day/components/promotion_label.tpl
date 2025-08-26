{if $product.promotions && fn_ab__dotd_filter_applied_promotions(array_keys($product.promotions), ['show_label_in_products_lists' => true, 'exclude_hidden' => true])}
    {include file="views/products/components/product_label.tpl"
        label_mini=$product_labels_mini
        label_static=$product_labels_static
        label_rounded=$product_labels_rounded
        label_meta="ab_dotd_product_label"
        label_text=__("ab__dotd_product_label")
    }
{/if}