{strip}
    {$replace_image_by_video = $ab__vg_settings.replace_image === "YesNo::YES"|enum}
    {$item_class = "ty-product-thumbnails__item cm-thumbnails-mini"}

    {if $is_thumbnails_gallery}
        {$item_class = "`$item_class` cm-gallery-item gallery"}
    {/if}

    {if $thumbnail_type === 'video'}
        {$item_class = "`$item_class` ab__vg-image_gallery_item"}

        {if ($replace_image_by_video && $video_iterator === 1) || (!$image_iterator && $video_iterator === 1)}
            {$item_class = "`$item_class` active"}
        {/if}

        {capture name="product_thumbnail"}
            <a href="javascript:void(0)" class="{$item_class}" data-ca-gallery-large-id="det_img_link_{$preview_id}_{$video.video_id}_{$video.unique_id}" data-ca-image-order="{$image_counter}" data-ca-parent="#product_images_{$preview_id}" style="width:{$th_size}px;height:{$th_size}px;">
                {include file="addons/ab__video_gallery/components/video_icon.tpl" icon_width=$th_size icon_height=$th_size icon_alt=$video.title|strip_tags obj_id="`$preview_id`_`$video.video_id`_mini"}
            </a>
        {/capture}
    {else}
        {if $image_iterator === 1 && (!$ab__vg_videos || (!$ab__vg_videos && $replace_image_by_video))}
            {$item_class = "`$item_class` active"}
        {/if}

        {capture name="product_thumbnail"}
            <a href="javascript:void(0)" class="{$item_class}" data-ca-gallery-large-id="det_img_link_{$preview_id}_{$image_id}" data-ca-image-order="{$image_counter}" data-ca-parent="#product_images_{$preview_id}" style="width:{$th_size}px;height:{$th_size}px;">
                {include file="common/image.tpl" ab__vg_gallery_image=true images=$image image_width=$th_size image_height=$th_size show_detailed_link=false obj_id="`$preview_id`_`$image_id`_mini"}
            </a>
        {/capture}
    {/if}

    {if $is_thumbnails_gallery}
        {capture name="product_thumbnail"}
            <div class="cm-item-gallery">{$smarty.capture.product_thumbnail nofilter}</div>
        {/capture}
    {/if}

    {$smarty.capture.product_thumbnail nofilter}
{/strip}