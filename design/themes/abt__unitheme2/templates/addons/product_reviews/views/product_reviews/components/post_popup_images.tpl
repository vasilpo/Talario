{$curl = $curl|default:fn_url("abt__ut2_product_reviews.get_product_reviews_gallery_popup&product_id=`$product_id`&abt__ut2_reviews_period=`$selected_period`&abt__ut2_reviews_rating=`$selected_rating`&only_buyers={$product_reviews_only_buyers}")}
{$th_size = ($settings.ab__device == "mobile")?300:140}
{strip}
{foreach $product_reviews as $review}
    {foreach $review.images as $image}
        {$thumb_h= ($image.detailed.image_y < $image.detailed.image_x)?$th_size:0}
        {$thumb_w= ($image.detailed.image_y >= $image.detailed.image_x)?$th_size:0}
        <a data-ca-target-id="abt__ut2_product_reviews_gallery_popup_{$product_id}"
           class="cm-ajax ty-product-review-thumbnail-link ut2-customer_review_popup-link"
           href="{$curl|fn_link_attach:"product_review_id=`$review.product_review_id`"|fn_url}"
           rel="nofollow"
           data-ca-dialog-title="{__("abt__ut2.product_reviews.customer_photos")}"
           data-rating="{$review.rating_value}"
           data-ca-dialog-class="ut2-customer_review"
           data-image-index="{$image@index}"
        >
            {include file="common/image.tpl" images=$image image_width=$thumb_w image_height=$thumb_h no_ids=true lazy_load=false}
        </a>
    {/foreach}
{/foreach}
{/strip}