{if $video}
    {$has_image_hover = $video.icon_type === 'snapshot' || ($video.icon_type === "icon" && $video.icon)}

    <div class="ab__vg-product_list-video{if $has_image_hover} hover_image{else} ab__vg_loading{/if}">
        {include file="addons/ab__video_gallery/components/video.tpl" video_hover_image=true}
    </div>
{/if}
