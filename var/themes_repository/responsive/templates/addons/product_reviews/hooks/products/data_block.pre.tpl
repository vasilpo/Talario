{*
    $show_rating
    $product
*}

{if $show_rating}

    {include file="addons/product_reviews/views/product_reviews/components/product_reviews_stars.tpl"
        rating=$product.average_rating
        link=true
        product=$product
        show_empty_rating=$show_empty_rating
    }

    {if $show_total_product_reviews}
    {$reviews_count = $product.reviews_count}
    {if empty($product.reviews_count) && !empty($product.product_reviews_count)}
        {$reviews_count = $product.product_reviews_count}
    {/if}
    
        {include file="addons/product_reviews/views/product_reviews/components/product_reviews_total_reviews.tpl"
            total_product_reviews=$reviews_count
            link=true
        }
    {/if}
{/if}