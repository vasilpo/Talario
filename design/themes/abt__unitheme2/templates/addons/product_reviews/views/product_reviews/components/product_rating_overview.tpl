{*
    $average_rating
    $total_product_reviews
    $out_of_five
*}

{if $product.product_reviews_count}
    {$out_of_five = $out_of_five|default:true}

    <section class="ty-product-review-product-rating-overview">

        <div class="ty-product-review-product-rating-overview__primary">
                <div class="ty-product-review-product-rating-overview__rating">
                    <strong class="ty-product-review-product-rating-overview__rating-current">
                        {$average_rating|default:0|round:1}
                    </strong>
                </div>
        </div>

        <div class="ty-product-review-product-rating-overview__secondary">
            {include file="addons/product_reviews/views/product_reviews/components/product_reviews_stars.tpl"
                rating=$average_rating
                size="large"
                show_rating_num='NO'
            }
            {include file="addons/product_reviews/views/product_reviews/components/product_reviews_total_reviews.tpl"
                total_product_reviews=$total_product_reviews
                secondary=true
            }
        </div>
    </section>
{/if}
