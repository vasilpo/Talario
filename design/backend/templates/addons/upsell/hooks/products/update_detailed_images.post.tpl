{capture "demonstration"}
    {if $smarty.const.CART_LANGUAGE === "ru"}
        {$video_av1 = "upsell_features_product_videos_ru.av1.mp4"}
        {$video_mp4 = "upsell_features_product_videos_ru.mp4"}
    {else}
        {$video_av1 = "upsell_features_product_videos_en.av1.mp4"}
        {$video_mp4 = "upsell_features_product_videos_en.mp4"}
    {/if}
    <div class="shift-button">
        <video autoplay loop width="1098" height="634" muted style="max-width: 100%; height: 100%; border: 1px solid var(--cs-table-border);" class="img-rounded">
            <source src="{$config.current_location}/design/backend/media/videos/{$video_av1}" type="video/mp4; codecs=av01.0.08M.08" />
            <source src="{$config.current_location}/design/backend/media/videos/{$video_mp4}" type="video/mp4" />
        </video>
    </div>
{/capture}

{include file="addons/upsell/components/tooltip.tpl"
    feature=constant("\Tygh\Licensing\Features::PRODUCT_VIDEOS")
    text="{__("upsell.product_videos.product_page")}"
    popup_content_pre=$smarty.capture.demonstration
}
