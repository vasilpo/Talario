{* Show "Show products" button on mobile *}
{strip}
<div class="ty-product-filters__outer-bottom">
    <div class="ty-product-filters__outer-bottom-inner">
        <button type="button" {""}
            class="ty-btn ty-btn__primary ty-product-filters__show-products cm-dialog-closer cm-scroll" {""}
            data-ca-scroll=".ty-mainbox-title" {""}
            {if !$products}disabled{/if} {""}
        >
            {if $products && $products_count}
                {__("show_n_products", [$products_count])}
            {elseif $products}
                {__("show_products")}
            {else}
                {__("text_no_products_found")}
            {/if}
        </button>
    </div>
</div>
{/strip}