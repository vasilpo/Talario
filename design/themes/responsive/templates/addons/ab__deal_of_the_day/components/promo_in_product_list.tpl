{$image_width = 50}

{if $promotion}
    <a href="{fn_url("promotions.view?promotion_id=`$promotion.promotion_id`")}">
        <span class="ab-dotd-promo-category-wrapper">
            {if $promotion.promotion_id }
                <span class="ab-dotd-promo-header">{__("ab__dotd_product_label")}</span><span>{$promotion.name}</span>
            {/if}
        </span>
    </a>
{/if}