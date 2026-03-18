{*
    $product_review
*}

<header class="ty-product-review-post-header">

    {include file="addons/product_reviews/views/product_reviews/components/product_reviews_stars.tpl"
        rating=$product_review.rating_value
    }

    {if $product_review.abt__ut2_purchased_variation_id}

        {$product_variation_data = fn_abt__ut2_get_purchased_variation_data($product_review.abt__ut2_purchased_variation_id)}
        {if $product_variation_data}
            {include file="addons/product_reviews/views/product_reviews/components/purchased_product.tpl" product=$product_variation_data}
        {/if}
    {/if}

</header>
