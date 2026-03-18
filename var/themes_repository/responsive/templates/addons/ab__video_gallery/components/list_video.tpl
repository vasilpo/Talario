{if $video}
    {include file="addons/ab__video_gallery/components/helpers.tpl"}
    {call name=fn_ab__vg_get_video_icon video=$video assign='icon_type'}

    {$has_image_hover = $icon_type === 'snapshot' || ($icon_type === 'icon' && $video.icon)}

    {$image_width = $settings.Thumbnails.product_lists_thumbnail_width}
    {$image_height = $settings.Thumbnails.product_lists_thumbnail_height}

    <div class="ab__vg-product_list-video{if $has_image_hover} hover_image{else} ab__vg_loading{/if}"
         style="--ab-vg-list-video-max-width: {$image_width}px; --ab-vg-list-video-max-height: {$image_height}px;"
    >
        {include file="addons/ab__video_gallery/components/video.tpl" video_hover_image=true}
    </div>
{/if}
