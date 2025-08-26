{strip}
{if $videos}
    <div class="ab-vg-videos-wrapper ab__vg-videos">
        {foreach $videos as $video}
            <div class="ty-column{$block.properties.number_of_columns} ab__vg-video">
                <a href="javascript:void(0)" id="det_img_link_{$preview_id}_{$video.video_id}" class="cm-dialog-opener" data-ca-target-id="ab__vg_video_{$video.video_id}" data-ca-dialog-title="{$video.title}" title="{$video.title}" rel="nofollow">
                    <span class="ab__vg-video_thumb ab-{$addons.ab__video_gallery.video_icon}-icon">
                        {include file="addons/ab__video_gallery/components/thumbnail.tpl" video=$video width=$block.properties.ab__vg_image_width height=$block.properties.ab__vg_image_height}
                    </span>
                </a>
                {if $block.properties.ab__vg_show_video_title == "YesNo::YES"|enum}
                    <div class="ab__vg-video_title"><p><strong>{$video.title}</strong></p></div>
                {/if}

                {if $block.properties.ab__vg_show_video_description == "YesNo::YES"|enum}
                    <div class="ab__vg-video_description">{$video.description nofilter}</div>
                {/if}
                {if $block.properties.ab__vg_show_product_link == "YesNo::YES"|enum}
                    <a href="{"products.view?product_id=`$video.product_id`"|fn_url}">{__("ab__vg.view_videos_product")}</a>
                {/if}

                {include file="addons/ab__video_gallery/components/video_popups.tpl" ab__vg_videos=[$video]}
            </div>
        {/foreach}
    </div>
{/if}
{/strip}