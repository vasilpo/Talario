{*
    $product_review
*}
{if $product_review}

    <article class="ty-product-review-post" id="product_review_{$product_review.product_review_id}_{$product_review.product_id}">

        {include file="addons/product_reviews/views/product_reviews/components/post_customer.tpl"
            product_review=$product_review
        }

        <section class="ty-product-review-post__content ty-dialog-caret">

            {hook name="product_reviews:post_content"}

                {include file="addons/product_reviews/views/product_reviews/components/post_header.tpl"
                    product_review=$product_review
                }

                {include file="addons/product_reviews/views/product_reviews/components/post_message.tpl"
                    product_review=$product_review
                }

                {include file="addons/product_reviews/views/product_reviews/components/post_footer.tpl"
                    product_review=$product_review
                    sw_id_postfix=($is_most_helpful)?'_01':'_1'
                }

            {/hook}

        </section>

        {include file="addons/product_reviews/views/product_reviews/components/post_vendor_reply.tpl"
            product_review=$product_review
        }
    </article>
{/if}
