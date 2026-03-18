{if $product.variation_features}
    <div class="ut2-customer_review-purchased-product">
        <a href="{"products.view&product_id=`$product.product_id`"|fn_url}" target="_blank">
            <span class="ty-muted">{__('abt__ut2.product_reviews.review_model')}:</span>

            {foreach $product.variation_features as $feature}
                <span class="ut2-lv__features-variant">
                    {if $feature.prefix}{$feature.prefix} {/if}
                    {$feature.variant}
                    {if $feature.suffix} {$feature.suffix}{/if}
                </span>
            {/foreach}

            <span class="ut2-customer_review-purchased-product-name">
                {$product.product}
                <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="18" height="18">
                    <path d="M3.8 15.8c-.5 0-.8-.2-1.1-.5-.3-.3-.5-.6-.5-1V3.7c0-.4.2-.7.5-1 .3-.3.6-.5 1-.5H9v1.5H3.7v10.6h10.6V9h1.4v5.3c0 .4-.1.7-.4 1-.3.3-.6.4-1 .4H3.7Zm3.5-4-1-1 6.9-7h-2.7V2.3h5.3v5.2h-1.6V4.8l-7 7Z"
                          fill="var(--color-links)"/>
                </svg>
            </span>
        </a>
    </div>
{/if}