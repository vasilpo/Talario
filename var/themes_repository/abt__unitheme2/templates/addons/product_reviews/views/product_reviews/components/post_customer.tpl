{*
    $product_review
*}

{if $product_review}
    {$date_machine_format = "%Y-%m-%dT%H:%M:%S"}
    {if $review_header}
        <div class="ty-product-review-post-type" data-type="{$review_header}">
            {($review_header=="positive")?"{__("abt__ut2.product_reviews.most_helpfull_reviews.positive")}":"{__("abt__ut2.product_reviews.most_helpfull_reviews.critical")}"}
        </div>
    {/if}
    <section class="ty-product-review-post-customer" id="review-post-customer-{$product_review.product_review_id}">
        <address class="ty-product-review-post-customer__address ty-address">
            {hook name="product_reviews:post_customer"}

                <div class="ty-product-review-post-customer__name ut2-ellipsis">

                    {if $addons.product_reviews.review_ask_for_customer_location !== "none"}
                        {if $addons.product_reviews.review_ask_for_customer_location === "country"
                        && ($product_review.user_data.country_code || $product_review.user_data.country)
                        }
                            <div class="ty-product-review-post-customer__location">
                                <div class="ty-product-review-post-customer__location-flag cm-tooltip" title="{$product_review.user_data.country}">
                                    {include_ext file="common/icon.tpl"
                                    class="ty-flag ty-flag-`$product_review.user_data.country_code|lower` ty-product-review-post-customer__location-flag-content"
                                    }
                                </div>
                            </div>
                        {/if}
                    {/if}

                    {if $product_review.user_data.name}
                        {$product_review.user_data.name}
                    {else}
                        {__("anonymous")}
                    {/if}

                    {if $addons.product_reviews.review_ask_for_customer_location !== "none" && $addons.product_reviews.review_ask_for_customer_location === "city" && $product_review.user_data.city}
                        <div class="ty-product-review-post-customer__location">
                            ({$product_review.user_data.city})
                        </div>
                    {/if}
                </div>

                {if $product_review.user_data.is_buyer === "YesNo::YES"|enum}

                    {$time = fn_abt__ut2_get_dates_diff($product_review.abt__ut2_first_buy_date,$product_review.product_review_timestamp)}

                    <div class="ty-product-review-post-customer__verified">
                        {__("product_reviews.verified_purchase")}
                    </div>
                {/if}

            {/hook}
        </address>

        {if $time}
            <div class="ab__ut2--customer__verified-in-use">
                {__("abt__ut2.product_reviews.more_than_x_time_in_use", ['[time]' => $time])}
            </div>
        {/if}
        {if $product_review.product_review_timestamp}
            <time class="ty-product-review-post-customer__date" datetime="{$product_review.product_review_timestamp|date_format:$date_machine_format}">
                {$product_review.product_review_timestamp|date_format:"`$settings.Appearance.date_format`"}
            </time>
        {/if}

    </section>
{/if}
