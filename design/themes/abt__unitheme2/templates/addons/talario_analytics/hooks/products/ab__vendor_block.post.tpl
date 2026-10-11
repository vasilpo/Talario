{if $product.product_id|intval == 1238}
    {* Temporary dev_copy acceptance rule: hide the duplicate vendor-block address from the owned Talario layer. *}
    <style id="talario-hide-vendor-address-1238">
        .ut2-vendor-block .sd-company-info {
            display: none !important;
        }
    </style>
{/if}
