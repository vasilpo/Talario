{$id="scroll_list_`$block.block_id`"}

{$popup_url = fn_url("abt__ut2_product_reviews.get_product_reviews_gallery_popup&product_id=`$product_id`&abt__ut2_reviews_period=`$selected_period`&abt__ut2_reviews_rating=`$selected_rating`&only_buyers={$product_reviews_only_buyers}")}

<div class="ty-subheader">{__('abt__ut2.product_reviews.customer_photos')}
    <a data-ca-target-id="abt__ut2_product_reviews_gallery_popup_{$product_id}"
       href="{$popup_url}"
       class="cm-dialog-opener cm-dialog-auto-size cm-dialog-destroy-on-close ut2-scroll-item ty-product-reviews-gallery-popup-link"
       data-ca-dialog-title="{__("abt__ut2.product_reviews.customer_photos")}"
       data-ca-dialog-class="ut2-customer_review"
       rel="nofollow">{__('abt__ut2.product_reviews.all_photos')}<i class="ut2-icon-arrow_forward_black"></i></a>
</div>

<div id="{$id}" class="ut2-gl__simple-scroller ut2-scroll-container ty-product-reviews-view__images-scroller">
    <button class="ut2-scroll-left" type="button"><span class="ut2-icon-arrow_back_black"></span></button>
    <div class="ut2-gl__simple-scroller-wrap ut2-scroll-content">
        {foreach $abt__ut2_product_reviews_gallery as $image}
            {$thumb_h= ($image.detailed.image_y < $image.detailed.image_x)?120:0}
            {$thumb_w= ($image.detailed.image_y >= $image.detailed.image_x)?120:0}
            <a data-ca-target-id="abt__ut2_product_reviews_gallery_popup_{$product_id}"
               href="{fn_url("abt__ut2_product_reviews.get_product_reviews_gallery_popup&product_id=`$product_id`&product_review_id=`$image.product_review_id`&abt__ut2_reviews_period=`$selected_period`&abt__ut2_reviews_rating=`$selected_rating`&only_buyers={$product_reviews_only_buyers}")}"
               class="cm-dialog-opener cm-dialog-auto-size cm-dialog-destroy-on-close ut2-scroll-item ty-product-review-thumbnail-link"
               rel="nofollow"
               data-rating="{$image.product_review_rating}"
               data-ca-dialog-title="{__("abt__ut2.product_reviews.customer_photos")}"
               data-ca-dialog-class="ut2-customer_review"
               data-image-index="{$image.index}"
            >
                {include file="common/image.tpl" images=$image image_width=$thumb_w image_height=$thumb_h no_ids=true lazy_load=false}
            </a>
        {/foreach}
        {if $abt__ut2_product_reviews_gallery|count > $MAX_ITEMS}
            <a data-ca-target-id="abt__ut2_product_reviews_gallery_popup_{$product_id}"
               href="{$popup_url}"
               class="cm-dialog-opener cm-dialog-auto-size cm-dialog-destroy-on-close ut2-scroll-item ty-product-review-thumbnail-link"
               data-ca-dialog-title="{__("abt__ut2.product_reviews.customer_photos")}"
               data-ca-dialog-class="ut2-customer_review"
               rel="nofollow">{__('abt__ut2.product_reviews.all_photos')}</a>
        {/if}
    </div>
    <button class="ut2-scroll-right" type="button"><span class="ut2-icon-arrow_forward_black"></span></button>
</div>

{include file="common/simple_scroller_init.tpl" block_id=$id elements_to_scroll=$elements_to_scroll|default: 3}
