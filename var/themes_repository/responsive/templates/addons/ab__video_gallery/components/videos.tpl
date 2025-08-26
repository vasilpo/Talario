{if $ab__vg_videos}
    {foreach $ab__vg_videos as $video}
        {include file="addons/ab__video_gallery/components/video.tpl" video=$video}
    {/foreach}
{/if}