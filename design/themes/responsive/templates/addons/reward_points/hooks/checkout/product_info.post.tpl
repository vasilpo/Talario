{if !$cart.products.$key.extra.configuration}
    {if $cart.products.$key.extra.points_info.price}
    <div class="ty-reward-points__product-info">
        <strong class="ty-control-group__label ty-reward-points-product-info__label ty-reward-points-product-info__label--price-in-points">{__("price_in_points")}:</strong>
        <span class="ty-control-group__item ty-reward-points-product-info__item ty-reward-points-product-info__item--price-in-points" id="price_in_points_{$key}">{__("points_lowercase", [$cart.products.$key.extra.points_info.display_price])}</span>
    </div>
    {/if}
    {if $cart.products.$key.extra.points_info.reward}
    <div class="ty-reward-points__product-info">
        <strong class="ty-control-group__label ty-reward-points-product-info__label ty-reward-points-product-info__label--reward-points">{__("reward_points")}:</strong>
        <span class="ty-control-group__item ty-reward-points-product-info__item ty-reward-points-product-info__item--reward-points" id="reward_points_{$key}">{__("points_lowercase", [$cart.products.$key.extra.points_info.reward])}</span>
    </div>
    {/if}
{/if}