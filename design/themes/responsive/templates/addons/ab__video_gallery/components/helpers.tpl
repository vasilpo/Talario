{function name="fn_ab__vg_get_videos_by_pos" position="$product_pos_enum::BOTTOM"|enum}
    {$total_images = $total_images|default:0}
    {$image_iterator = fn_ab__vg_get_template_iterator('image_iterator')}

    {$videos = $videos|default:fn_ab__vg_get_template_videos()}
    {$_videos = fn_ab__vg_get_videos_by_position($videos, $position, $image_iterator, $total_images)}

    {fn_ab__vg_set_template_videos($_videos[1])}

    {foreach $_videos[0] as $video}
        {fn_ab__vg_increase_template_iterator('image_counter')}
        {fn_ab__vg_increase_template_iterator('video_iterator')}

        {include file="addons/ab__video_gallery/components/video.tpl" video=$video}
    {/foreach}
{/function}

{function name="fn_ab__vg_get_videos_thumbs_by_pos" position="$product_pos_enum::BOTTOM"|enum}
    {$total_images=$total_images|default:0}
    {$image_iterator = fn_ab__vg_get_template_iterator('image_iterator')}

    {$videos = $videos|default:fn_ab__vg_get_template_videos()}
    {$_videos = fn_ab__vg_get_videos_by_position($videos, $position, $image_iterator, $total_images)}

    {fn_ab__vg_set_template_videos($_videos[1])}

    {foreach $_videos[0] as $video}
        {fn_ab__vg_increase_template_iterator('image_counter')}
        {fn_ab__vg_increase_template_iterator('video_iterator')}

        {include file="addons/ab__video_gallery/components/product_thumbnail.tpl" video=$video thumbnail_type="video"}
    {/foreach}
{/function}

{function name="fn_ab__vg_get_core_videos"}
    {$total_images = $total_images|default:0}
    {$core_videos = $core_videos|default:[]}

    {foreach $core_videos as $video}
        {fn_ab__vg_increase_template_iterator('image_counter')}
        {fn_ab__vg_increase_template_iterator('image_iterator')}

        {include "components/video/video_previewer.tpl"
            video=$video
            obj_id="{$obj_prefix}{$preview_id}_{$video.video_id}"
            preview_id=$preview_id
            link_class=$_link_class
        }

        {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::CUSTOM"|enum total_images=$total_images}
    {/foreach}
{/function}

{function name="fn_ab__vg_get_core_videos_thumbs" core_videos=$core_videos|default:[]}
    {foreach $core_videos as $video}
        {fn_ab__vg_increase_template_iterator('image_counter')}
        {fn_ab__vg_increase_template_iterator('image_iterator')}

        {include file="addons/ab__video_gallery/components/product_thumbnail.tpl"
            image=$video.preview
            thumbnail_type="core_video"
            obj_id="`$obj_prefix``$preview_id`_`$video.video_id`_mini"
        }

        {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::CUSTOM"|enum}
    {/foreach}
{/function}

{function name="fn_ab__vg_get_video_icon"}{strip}
    {$video = $video|default:['type' => 'Addons\Ab_videoGallery\VideoTypes::RESOURCE'|enum, 'icon_type' => 'icon', 'icon' => []]}

    {$allowed_icon_types = []}

    {if $video.icon}
        {$allowed_icon_types = ['icon']}
    {/if}

    {if in_array($video.type, ['Addons\Ab_videoGallery\VideoTypes::YOUTUBE'|enum, 'Addons\Ab_videoGallery\VideoTypes::VIMEO'|enum])}
        {$allowed_icon_types = fn_ab__vg_array_unshift($allowed_icon_types, 'snapshot')}
    {/if}

    {if $autoplay}
        {$allowed_icon_types[] = 'none'}
    {/if}

    {if !in_array($video.icon_type, $allowed_icon_types)}
        {if $allowed_icon_types}
            {$video.icon_type = $allowed_icon_types[0]}
        {else}
            {$video.icon_type = 'icon'}
        {/if}
    {/if}

    {$video.icon_type}
{/strip}{/function}