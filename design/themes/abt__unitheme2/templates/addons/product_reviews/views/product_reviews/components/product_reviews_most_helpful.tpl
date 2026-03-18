{if !empty($abt__ut2_product_reviews_most_helpful.positive) || !empty($abt__ut2_product_reviews_most_helpful.negative)}

<div class="ty-discussion-posts-most-rated">
    {foreach $abt__ut2_product_reviews_most_helpful as $type => $review}
        {if $review}
            {$is_most_helpful = true}
            <article class="ty-product-review-post">
                {include file="addons/product_reviews/views/product_reviews/components/post_customer.tpl"
                product_review=$review
                review_header=$type
                }

                <section class="ty-product-review-post__content ty-dialog-caret ty-product-review-post-most-helpful">

                    {hook name="product_reviews:post_content"}
                        {include file="addons/product_reviews/views/product_reviews/components/post_header.tpl"
                        product_review=$review
                        }

                        {include file="addons/product_reviews/views/product_reviews/components/post_message.tpl"
                        product_review=$review
                        }

                        {include file="addons/product_reviews/views/product_reviews/components/post_footer.tpl"
                        product_review=$review
                        no_images=true
                        most_helpful=true
                        }
                    {/hook}

                </section>

                {include file="addons/product_reviews/views/product_reviews/components/post_vendor_reply.tpl"
                product_review=$product_review
                }
            </article>
        {/if}
    {/foreach}
</div>
{/if}