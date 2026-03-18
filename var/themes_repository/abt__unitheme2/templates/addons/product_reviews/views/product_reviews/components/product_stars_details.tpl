{*
    $ratings_stats
    $quantity
    $rating
    $total_product_reviews
*}

{if $product.product_reviews_count > 0}
    <section class="ty-product-review-product-stars-details">

        {foreach $ratings_stats as $quantity => $rating}

            {include file="addons/product_reviews/views/product_reviews/components/product_star_line.tpl"
                quantity=$quantity
                percentage=$rating.percentage
                count=$rating.count
            }

        {/foreach}

    </section>
{/if}
