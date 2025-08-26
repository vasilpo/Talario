{** block-description:ab__deal_of_the_day_title_product **}

{if $product.promotions}
    {$promotions_ids = fn_ab__dotd_filter_applied_promotions(array_keys($product.promotions), ["exclude_hidden" => true])}
    {if count($promotions_ids) > 1}
        {$items = fn_ab__dotd_get_promotions(['promotion_id' => $promotions_ids])}
        {include file="views/promotions/list.tpl" promotions=$items.0 show_chains=false}
    {/if}
{/if}