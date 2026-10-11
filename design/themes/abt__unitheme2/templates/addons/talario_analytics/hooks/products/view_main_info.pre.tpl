{if $product.product_id|intval == 1238}
    {* Temporary dev_copy acceptance rule: keep the stock geo_maps shipping-estimation block hidden for this card. *}
    {$show_shipping_estimation = false scope=parent}
{/if}
