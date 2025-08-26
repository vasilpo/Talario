{$ab__vg_videos = $product.product_id|fn_ab__vg_get_videos:['autoplay' => 'YesNo::YES'|enum, 'show_in_list' => 'YesNo::YES'|enum, 'limit' => 1]}

{if $ab__vg_videos}
    <div class="ab__vg-product_list-wrapper">
        <div class="ab__vg-product_list-image">
            {include file="common/image.tpl"
                obj_id=$obj_id_prefix
                images=$product.main_pair
                image_width=$image_width
                image_height=$image_height
                class="img-ab-hover-gallery"
            }
        </div>

        {include file="addons/ab__video_gallery/components/list_video.tpl" video=$ab__vg_videos|reset}

        {if !$product.main_pair}
            {$product.main_pair = [true] scope="parent"}
        {else}
            {if !is_array($product.image_pairs)}
                {$product.image_pairs = []}
            {/if}

            {$_junk = array_unshift($product.image_pairs, $product.main_pair)}
            {$product.image_pairs = $product.image_pairs scope="parent"}
        {/if}
    </div>
{/if}