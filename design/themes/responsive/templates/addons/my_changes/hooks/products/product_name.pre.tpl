{if $product.company_id == 32}
    {$show_price_values = false scope="parent"}
    {$product.prices = false scope="parent"}
    {$show_qty = false scope="parent"}
    {$show_add_to_cart_block = false scope="parent"}
{/if}