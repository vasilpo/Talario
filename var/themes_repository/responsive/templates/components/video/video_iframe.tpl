{*
    $block_id
    $video
    $use_aspect_ratio
    $video_additional_attrs
*}

{$video_additional_attrs.frameborder = "0"}
{$video_additional_attrs.enablejsapi="1"}
{$video_additional_attrs.allow = ($product.autoplay_videos === "Y") ? "autoplay; fullscreen; encrypted-media;" : "fullscreen; encrypted-media;"}

<div class="ty-video {if $use_aspect_ratio}ty-video--aspect-ratio{/if}">
    <div class="ty-video__content">
    <iframe class="ty-video__iframe js-video"
            data-ca-video-source="{$video.source}"
            id="{$block_id}_{$video.video_id}"
            src="{$video.video_urls.embed_video_url}"
            {$video_additional_attrs|render_tag_attrs nofilter}
        ></iframe>
    </div>
</div>
