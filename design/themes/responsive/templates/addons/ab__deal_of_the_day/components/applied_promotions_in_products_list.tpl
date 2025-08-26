{if $addons.ab__deal_of_the_day.amount_of_promos_in_prods_lists && $product.promotions}
    {$promotions_ids = fn_ab__dotd_filter_applied_promotions(array_keys($product.promotions), ["exclude_hidden" => true, "show_in_products_lists" => true])}
    {$promotions_ids = array_slice($promotions_ids, 0, $addons.ab__deal_of_the_day.amount_of_promos_in_prods_lists)}

    <div class="ab-dotd-promos">
        {foreach $promotions_ids as $promotion_id}
            <div class="ab-dotd-category-promo" data-ca-promotion-id="{$promotion_id}"></div>
        {/foreach}
    </div>
{/if}