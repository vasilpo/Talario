{if $product.promotions && fn_ab__dotd_filter_applied_promotions(array_keys($product.promotions), ['show_label_in_products_lists' => true, 'exclude_hidden' => true])}
    <div class="ab_dotd_product_label">{__('ab__dotd_product_label')}</div>
{/if}