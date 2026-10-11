{if $product.product_id|intval == 1238}
    {* Temporary dev_copy acceptance rule: hide the duplicate vendor-block address from the owned Talario layer. *}
    <style>
        .ut2-vendor-block .sd-company-info {
            display: none !important;
        }
    </style>
{/if}
