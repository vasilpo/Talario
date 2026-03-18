{$ratings_best = $product.product_reviews_rating_stats.ratings|@count}
{$empty_stars_count = $ratings_best - $full_stars_count}
<span class="ty-product-review-reviews-stars__content ty-product-review-reviews-stars__content--long">
    {section name="full_star" loop=$full_stars_count}
        {include_ext file="common/icon.tpl"
            class="ty-icon-star ty-product-review-reviews-stars__icon"
        }
    {/section}

    {if $is_half_rating}
        {include_ext file="common/icon.tpl"
            class="ty-icon-star-half ty-product-review-reviews-stars__icon"
        }
    {/if}

    {section name="full_star" loop=$empty_stars_count}
        {include_ext file="common/icon.tpl"
            class="ty-icon-star-empty ty-product-review-reviews-stars__icon"
        }
    {/section}
</span>
<span class="ty-product-review-reviews-stars__content ty-product-review-reviews-stars__content--short">
    {include_ext file="common/icon.tpl"
        class="ty-icon-star ty-product-review-reviews-stars__icon"
    }
    <span class="ty-product-review-reviews-stars__decimals-rating">
        {$decimals_rating}
    </span>
</span>