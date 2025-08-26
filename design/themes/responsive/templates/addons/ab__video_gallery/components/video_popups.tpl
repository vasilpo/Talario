{* Video popups content. For tab with videos or for popup onclick *}
{foreach $ab__vg_videos as $video}
    {$autoplay = $video.autoplay === "YesNo::YES"|enum}
    {$video_url = fn_ab__vg_get_video_embed_url($video)}
    {$video_params = json_encode([
        'url'       => $video_url,
        'type'      => $video.type,
        'path'      => $video.video_path,
        'autoplay'  => $autoplay
    ])}

    <div id="ab__vg_video_{$video.video_id}" class="ab__vg_video_popup cm-popup-box hidden" data-ca-keep-in-place="true" title="{$video.title}">
        <div id="ab__vg_iframe_video_{$video.video_id}_{$video.unique_id}" class="ab__vg_loading ab__vg_video_player"{if $video.settings.iframe_attributes} {$video.settings.iframe_attributes nofilter}{else} data-frameborder="0"{if $video.settings.controls === "YesNo::YES"|enum} data-allowfullscreen="1"{/if}{/if} data-ab-vg-video-params="{$video_params}" data-src="{$video_url}">
            {include file="addons/ab__video_gallery/components/thumbnail.tpl" video=$video}
        </div>
    </div>
{/foreach}