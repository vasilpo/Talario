{*
    $product
    $product_review
*}

{if $product_review}
    <footer class="ty-product-review-post-footer">
        <div class="ty-product-review-post-footer__primary">
            {if !$no_images && !$mini_gallery}
            {include file="addons/product_reviews/views/product_reviews/components/post_images.tpl"
                images=$product_review.images
                preview_id=$product.product_id|uniqid
            }{/if}
            {if $mini_gallery}
                {include file="addons/product_reviews/views/product_reviews/components/post_mini_gallery.tpl"}
            {/if}
        </div>

        <div class="ty-product-review-post-footer__secondary">
            {if $most_helpful}
                <span class="ty-muted ut2-customer_review-useful">
                    <svg class="ty-valign" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 17.5H6.667V6.667L12.5.833l1.042 1.042c.097.097.177.23.24.396.062.167.093.326.093.479v.292l-.916 3.625H17.5c.445 0 .834.166 1.167.5.333.333.5.722.5 1.166V10a1.694 1.694 0 0 1-.125.625l-2.5 5.875a1.683 1.683 0 0 1-.625.708A1.628 1.628 0 0 1 15 17.5ZM5 6.667V17.5H1.667V6.667H5Z" fill="currentColor"/></svg>
                    &nbsp;<span class="ty-valign">{__("abt__ut2.product_reviews.found_this_helpfull",[$product_review.helpfulness.vote_up])} </span>
                </span>
            {else}
                <span class="ty-muted">{__("abt__ut2.product_reviews.is_helpfull")}</span>
                {include file="addons/product_reviews/views/product_reviews/components/post_votes.tpl"
                product_review_id=$product_review.product_review_id
                vote_up=$product_review.helpfulness.vote_up
                vote_down=$product_review.helpfulness.vote_down
                id_postfix=$sw_id_postfix
                }
            {/if}
            {function name="copy_link_content"}
                {$hash="#product_review_`$product_review.product_review_id`_`$product_review.product_id`"}
                {$review_link="products.view&product_id=`$product_review.product_id`"|fn_url|cat:$hash}
                <div class="ut2-popup-box-copy-link-content">
                    <input type="text" value="{$review_link}" class="ty-mb-s ty-block">
                    {strip}
                        <button
                                type="button"
                                class="ty-btn ty-btn__outline  ty-product-review-copy-link"
                                data-link="{$review_link}"
                        ><i class="ut2-icon-copy"></i>&nbsp;{__("copy")}</button>
                    {/strip}
                </div>
            {/function}

            {if $settings.ab__device != "mobile"}
            <span class="ty-muted cm-combination ty-hand ut2-customer_review-copy-link" id="sw_copy_review_link_{$product_review.product_review_id}_{$product_review.product_id}_{$sw_id_postfix}">
                <svg width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 19.17a1.6 1.6 0 0 1-1.18-.5 1.6 1.6 0 0 1-.49-1.17V8.33c0-.45.17-.85.5-1.17A1.6 1.6 0 0 1 5 6.66h2.5v1.67H5v9.17h10V8.33h-2.5V6.67H15c.46 0 .85.16 1.18.49.32.32.49.72.49 1.17v9.17c0 .46-.17.85-.5 1.18a1.6 1.6 0 0 1-1.17.49H5Zm4.17-5.84v-9.3L7.83 5.34 6.67 4.17 10 .83l3.33 3.34-1.16 1.18-1.34-1.33v9.31H9.17Z" fill="currentColor"/></svg>
                <span>{__("abt__ut2.addon_social_buttons.share")}</span>
            </span>
            <div id="copy_review_link_{$product_review.product_review_id}_{$product_review.product_id}_{$sw_id_postfix}"  class="cm-popup-box ty-dropdown-box__content ut2-customer_review-copy-link-wrap" style="display: none">

                <div class="ut2-popup-box-title">{__('abt__ut2.product_reviews.copy_link')}
                    <div class="ut2-btn-close cm-combination"  id="off_copy_review_link_{$product_review.product_review_id}_{$product_review.product_id}_{$sw_id_postfix}" ><i class="ut2-icon-baseline-close"></i></div>
                </div>
                {copy_link_content}
            </div>
            {else}
                <span class="ut2-customer_review-copy-link cm-dialog-opener ty-muted"
                      data-ca-target-id="copy_review_link_{$product_review.product_review_id}_{$product_review.product_id}_{$sw_id_postfix}"
                      data-ca-dialog-title="{__('abt__ut2.product_reviews.copy_link')}"
                >
                     <svg width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 19.17a1.6 1.6 0 0 1-1.18-.5 1.6 1.6 0 0 1-.49-1.17V8.33c0-.45.17-.85.5-1.17A1.6 1.6 0 0 1 5 6.66h2.5v1.67H5v9.17h10V8.33h-2.5V6.67H15c.46 0 .85.16 1.18.49.32.32.49.72.49 1.17v9.17c0 .46-.17.85-.5 1.18a1.6 1.6 0 0 1-1.17.49H5Zm4.17-5.84v-9.3L7.83 5.34 6.67 4.17 10 .83l3.33 3.34-1.16 1.18-1.34-1.33v9.31H9.17Z" fill="currentColor"/></svg>
                    <span>{__("abt__ut2.addon_social_buttons.share")}</span>
                </span>
                <div id="copy_review_link_{$product_review.product_review_id}_{$product_review.product_id}_{$sw_id_postfix}" data-ca-keep-in-place="true" style="display: none">
                {copy_link_content}
                </div>
            {/if}
            {*review-post-customer-{$product_review.product_review_id}*}
        </div>
    </footer>
{/if}
