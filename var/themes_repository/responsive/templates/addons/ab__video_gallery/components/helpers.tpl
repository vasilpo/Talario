{function name="fn_ab__vg_get_videos_by_pos" videos=$videos|default:[] position="$product_pos_enum::BOTTOM"|enum image_iterator=$image_iterator|default:0 total_images=$total_images|default:0}
    {$videos_by_pos = fn_ab__vg_get_videos_by_position($videos, $position, $image_iterator, $total_images)}
    {$videos = $videos scope="parent"}

    {foreach $videos_by_pos as $video}
        {include file="addons/ab__video_gallery/components/video.tpl" video=$video}
    {/foreach}

    {include file="addons/ab__video_gallery/components/videos.tpl" ab__vg_videos=fn_ab__vg_get_videos_by_position($videos, $position, $image_iterator, $total_images)}
{/function}

{function name="fn_ab__vg_get_videos_thumbs_by_pos" videos=$videos|default:[] position="$product_pos_enum::BOTTOM"|enum image_iterator=$image_iterator|default:0 total_images=$total_images|default:0 video_iterator = $video_iterator|default:0}
    {$videos_by_pos = fn_ab__vg_get_videos_by_position($videos, $position, $image_iterator, $total_images)}
    {$videos = $videos scope="parent"}

    {foreach $videos_by_pos as $video}
        {$video_iterator = $video_iterator + 1 scope="parent"}
        {$image_counter = $image_counter + 1 scope="parent"}

        {include file="addons/ab__video_gallery/components/product_thumbnail.tpl" video=$video thumbnail_type="video"}
    {/foreach}
{/function}