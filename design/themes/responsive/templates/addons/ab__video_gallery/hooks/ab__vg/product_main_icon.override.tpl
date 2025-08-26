{if !$product.image_pairs || !$show_gallery}
    {$ab__vg_videos = $product.product_id|fn_ab__vg_get_videos:['autoplay' => 'YesNo::YES'|enum, 'show_in_list' => 'YesNo::YES'|enum, 'limit' => 1]}

    {if $ab__vg_videos}
        {capture name="main_icon"}
            <a href="{"$product_detail_view_url"|fn_url}">
                {include file="addons/ab__video_gallery/components/list_video.tpl" video=$ab__vg_videos|reset}
            </a>
        {/capture}
    {/if}
{/if}