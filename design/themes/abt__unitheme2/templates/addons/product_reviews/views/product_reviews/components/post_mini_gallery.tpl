<div class="ty-product-review-popup-mini-gallery"
     data-review="{$product_review.product_review_id}"
     id="ty-product-review-mini-gallery">
    {foreach $product_review.images as $image}
        {$thumb_h= ($image.detailed.image_y < $image.detailed.image_x)?120:0}
        {$thumb_w= ($image.detailed.image_y >= $image.detailed.image_x)?120:0}
        <div class="ty-product-review-popup-mini-gallery-img" data-index="{$image@index}">
            {include file="common/image.tpl" images=$image image_width=$thumb_w image_height=$thumb_h no_ids=true lazy_load=false
            }</div>
    {/foreach}
<!--ty-product-review-mini-gallery--></div>