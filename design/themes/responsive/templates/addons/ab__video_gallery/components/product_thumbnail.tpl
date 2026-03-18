{strip}
    {$product_id = $product.product_id|default:$product_id|default:0}
    {$ab__vg_videos = $ab__vg_videos|default:($product_id|fn_ab__vg_get_videos)}
    {$ab__vg_settings = $ab__vg_settings|default:($product_id|fn_ab__vg_get_setting)}

    {$th_size = $th_size|default:60}
    {$thumbnail_type = $thumbnail_type|default:'image'}
    {$image_counter = $image_counter|default:fn_ab__vg_get_template_iterator('image_counter')}
    {$image_iterator = $image_iterator|default:fn_ab__vg_get_template_iterator('image_iterator')}
    {$video_iterator = $video_iterator|default:fn_ab__vg_get_template_iterator('video_iterator')}
    {$replace_image = $replace_image|default:($ab__vg_settings.replace_image === 'YesNo::YES'|enum && $ab__vg_videos)}
    {$is_thumbnails_gallery = $is_thumbnails_gallery|default:($settings.Appearance.thumbnails_gallery === 'YesNo::YES'|enum)}

    {$item_class = "ty-product-thumbnails__item cm-thumbnails-mini"}

    {if $is_thumbnails_gallery}
        {$item_class = "`$item_class` cm-gallery-item gallery"}
    {/if}

    {if $thumbnail_type === 'video'}
        {$video = $video|default:[]}
        {$item_class = "`$item_class` ab__vg-image_gallery_item"}

        {if ($replace_image && $video_iterator === 1) || (!$image_iterator && $video_iterator === 1)}
            {$item_class = "`$item_class` active"}
        {/if}

        {capture name="product_thumbnail"}
            <a href="javascript:void(0)"
               class="{$item_class}"
               data-ca-image-order="{$image_counter}"
               data-ca-parent="#product_images_{$obj_prefix}{$preview_id}" style="width:{$th_size}px;height:{$th_size}px;"
               data-ca-gallery-large-id="det_img_link_{$obj_prefix}{$preview_id}_{$video.video_id}_{$video.unique_id}"
            >
                {if $video.icon_type === 'none' || ($video.icon_type === 'icon' && !$video.icon) || true} {* TODO *}
                    {include file="addons/ab__video_gallery/components/video_icon.tpl"
                        icon_width=$th_size
                        icon_height=$th_size
                        icon_alt=$video.title|strip_tags
                        obj_id="`$obj_prefix``$preview_id`_`$video.video_id`_mini"
                    }
                {else}
                    {include file="addons/ab__video_gallery/components/thumbnail.tpl" video=$video width=$th_size height=$th_size}
                {/if}
            </a>
        {/capture}
    {else}
        {$image = $image|default:[]}
        
        {if $image_iterator === 1 && !$replace_image}
            {$item_class = "`$item_class` active"}
        {/if}

        {if $thumbnail_type === 'core_video'}
            {$item_class = "`$item_class` ty-video-thumbnail"}
        {/if}

        {capture name="product_thumbnail"}
            <a href="javascript:void(0)"
               class="{$item_class}"
               style="width:{$th_size}px;height:{$th_size}px;"
               data-ca-image-order="{$image_counter}"
               data-ca-parent="#product_images_{$obj_prefix}{$preview_id}"
               data-ca-gallery-large-id="det_img_link_{$obj_prefix}{$preview_id}_{$image_id}"
            >
                {include file="common/image.tpl"
                    ab__vg_gallery_image=true
                    images=$image
                    image_width=$th_size
                    image_height=$th_size
                    show_detailed_link=false
                    obj_id=$obj_id|default:"`$obj_prefix``$preview_id`_`$image_id`_mini"
                }
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
