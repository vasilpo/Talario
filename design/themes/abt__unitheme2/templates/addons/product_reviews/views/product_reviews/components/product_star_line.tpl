{*
    $quantity
    $percentage
    $count
*}

<div class="ty-product-review-product-star-line">

    <div class="ty-product-review-product-star-line__quantity"                 style="{if $selected_rating == $quantity}color: var(--color-links);font-weight: 600;{/if}">
        {__("product_reviews.n_stars", [$quantity])}
    </div>
        <a
                href="{$curl|fn_link_attach:"abt__ut2_reviews_rating={if $selected_rating == $quantity}0{else}{$quantity}{/if}&selected_section=product_reviews"|fn_url}"
                class="ty-product-review-reviews-navigation__filter-link cm-ajax"
                data-ca-target-id="product_reviews_list_{$product_id}"
                rel="nofollow"

        >
            <progress
                class="ty-product-review-product-star-line__line"
                max="100"
                value="{$percentage}"
                title="{__("product_reviews.reviews", [$count])}"
            ></progress>
        </a>

    <div class="ty-product-review-product-star-line__percentage">
        {$percentage|round}%
    </div>

</div>