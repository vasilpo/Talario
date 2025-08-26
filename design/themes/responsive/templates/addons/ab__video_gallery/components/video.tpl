{$autoplay = $video.autoplay === "YesNo::YES"|enum}
{$video_object_id = "det_img_link_`$preview_id`_`$video.video_id`_`$video.unique_id`"}
{$video_url = fn_ab__vg_get_video_embed_url($video)}
{$video_hover_image = $video_hover_image|default:false}

{if ($smarty.request.dispatch === 'products.view' && ($block.type === 'main' || (empty($block) && !$smarty.request.full_render))) || $smarty.request.dispatch === 'products.quick_view'}
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
        {$has_image_hover = $video.icon_type === 'snapshot' || ($video.icon_type === "icon" && $video.icon)}

        {if $has_image_hover}
            <div class="ab__vg-image_gallery_image">
                {include file="addons/ab__video_gallery/components/thumbnail.tpl" video=$video width=$image_width|default:$width height=$image_height|default:$height}
            </div>
        {/if}
    {/if}

    {if $addons.ab__video_gallery.on_thumbnail_click == 'image_replace' || $quick_view || $autoplay}
        {if $autoplay && $video.type === "Addons\Ab_videoGallery\VideoTypes::RESOURCE"|enum}
            <video id="{$video_object_id}" class="ab__vg-image_gallery_video ab__vg-image_gallery_video-autoplay" src="{$video_url}" {if $video.settings.iframe_attributes} {$video.settings.iframe_attributes nofilter}{/if} autoplay="autoplay" loop="loop" muted="muted" playsinline="playsinline" data-ab-vg-video-params="{$video_params}" width="{$width}" height="{$height}"></video>
        {else}
            <div id="{$video_object_id}" class="ab__vg_loading ab__vg-image_gallery_video{if $autoplay} ab__vg-image_gallery_video-autoplay{/if}{if !$autoplay} ab-{$addons.ab__video_gallery.video_icon}-icon{/if}"{if $video.settings.iframe_attributes} {$video.settings.iframe_attributes nofilter}{else} data-frameborder="0"{if $video.settings.controls === "YesNo::YES"|enum} data-allowfullscreen="1"{/if}{/if} data-src="{$video_url}" data-ab-vg-video-params="{$video_params}">
                {include file="addons/ab__video_gallery/components/thumbnail.tpl" video=$video width=$image_width height=$image_height}
            </div>
        {/if}
    {else}
        <a id="{$video_object_id}" class="ty-previewer ab__vg-image_gallery_video ab-{$addons.ab__video_gallery.video_icon}-icon cm-dialog-opener hidden" data-ca-target-id="ab__vg_video_{$video.video_id}" data-ca-dialog-title="{$video.title}" title="{$video.title}" rel="nofollow">
            {include file="addons/ab__video_gallery/components/thumbnail.tpl" video=$video width=$image_width height=$image_height}
        </a>
    {/if}
</div>