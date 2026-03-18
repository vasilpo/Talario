{include file="addons/ab__video_gallery/components/helpers.tpl"}
{call name=fn_ab__vg_get_video_icon video=$video assign='icon_type'}

{$video = $video|default:[]}
{$autoplay = $video.autoplay === 'YesNo::YES'|enum}
{$video_url = fn_ab__vg_get_video_embed_url($video)}
{$video_hover_image = $video_hover_image|default:false}
{$video_object_id = "det_img_link_`$obj_prefix``$preview_id`_`$video.video_id`_`$video.unique_id`"}

{if $video}
    {$video.icon_type = $icon_type}
{/if}

{if
    $smarty.request.dispatch === 'products.quick_view' ||
    ($smarty.request.dispatch === 'products.view' && ($block.type === 'main' || (empty($block) && !$smarty.request.full_render)))
}
    {$is_detailed = true}
    {$image_iterator = $image_iterator|default:fn_ab__vg_get_template_iterator('image_iterator')}
    {$video_iterator = $video_iterator|default:fn_ab__vg_get_template_iterator('video_iterator')}

    {$width = $settings.Thumbnails.product_details_thumbnail_width}
    {$height = $settings.Thumbnails.product_details_thumbnail_height}
{else}
    {$width = $image_width|default:$settings.Thumbnails.product_lists_thumbnail_width}
    {$height = $image_height|default:$settings.Thumbnails.product_lists_thumbnail_height}
{/if}

{$video_params = json_encode([
    'url'       => $video_url,
    'type'      => $video.type,
    'path'      => $video.video_path,
    'autoplay'  => $autoplay,
    'width'     => $width,
    'height'    => $height
])}

<div class="ab__vg-image_gallery_video-wrapper" style="--vaspect:{if !$width || !$height}auto{else}{$width/$height}{/if}" {if $autoplay}inert{/if}>
    {if $video_hover_image}
        {$has_image_hover = $video.icon_type === 'snapshot' || ($video.icon_type === 'icon' && $video.icon)}

        {if $has_image_hover}
            <div class="ab__vg-image_gallery_image">
                {include file="addons/ab__video_gallery/components/thumbnail.tpl"
                    video=$video
                    width=$image_width|default:$width
                    height=$image_height|default:$height
                }
            </div>
        {/if}
    {/if}

    {$video_class = "ab__vg-image_gallery_video"}

    {if $is_detailed && (($replace_image && $video_iterator !== 1) || (!$replace_image && $image_iterator))}
        {$video_class = "$video_class hidden"}
    {/if}

    {if $addons.ab__video_gallery.on_thumbnail_click === 'image_replace' || $quick_view || $autoplay}
        {if $autoplay && $video.type === "Addons\Ab_videoGallery\VideoTypes::RESOURCE"|enum}
            {$video_class = "$video_class ab__vg-image_gallery_video-autoplay"}

            <video class="{$video_class}"
                   src="{$video_url}"
                   autoplay="autoplay"
                   loop="loop"
                   muted="muted"
                   playsinline="playsinline"
                   width="{$width}"
                   height="{$height}"
                   data-ab-vg-video-params="{$video_params}"
                   {if $video.settings.iframe_attributes} {$video.settings.iframe_attributes nofilter} {/if}
                   id="{$video_object_id}"
            ></video>
        {else}
            {if $is_detailed && !$autoplay}
                {$video_class = "$video_class ab__vg_loading"}
            {/if}

            {if $autoplay}
                {$video_class = "$video_class ab__vg-image_gallery_video-autoplay"}
            {else}
                {$video_class = "$video_class ab-`$addons.ab__video_gallery.video_icon`-icon"}
            {/if}

            <div class="{$video_class}"
                 data-src="{$video_url}"
                 data-frameborder="0"
                 data-ab-vg-video-params="{$video_params}"
                 {if $video.settings.controls === 'YesNo::YES'|enum} data-allowfullscreen="1" {/if}
                 {if $video.settings.iframe_attributes} {$video.settings.iframe_attributes nofilter} {/if}
                 id="{$video_object_id}"
            >
                {include file="addons/ab__video_gallery/components/thumbnail.tpl"
                    video=$video
                    width=$image_width
                    height=$image_height
                }
            </div>
        {/if}
    {else}
        {$video_class = "$video_class ty-previewer cm-dialog-opener ab-{$addons.ab__video_gallery.video_icon}-icon"}

        <a class="{$video_class}"
           data-ca-target-id="ab__vg_video_{$video.video_id}"
           data-ca-dialog-title="{$video.title}"
           title="{$video.title}"
           rel="nofollow"
           id="{$video_object_id}"
        >
            {include file="addons/ab__video_gallery/components/thumbnail.tpl"
                video=$video
                width=$image_width
                height=$image_height
            }
        </a>
    {/if}
</div>
