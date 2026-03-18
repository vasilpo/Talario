{*
    $class
    $style
    $product_videos
    $video
    $preview_id
    $image_counter
    $th_size
    $obj_id
*}

{$parent_class = $class|default: ""}
{$style = $style|default: ""}

<a data-ca-gallery-large-id="det_img_link_{$preview_id}_{$video.video_id}" {""}
    class="{$parent_class} cm-thumbnails-mini {if $image_counter === 0}active{/if} ty-product-thumbnails__item ty-video-thumbnail" {""}
    {if !empty($style)}style="{$style}" {""}{/if}
    data-ca-image-order="{$image_counter}" {""}
    data-ca-parent="#product_images_{$preview_id}"
>
    {include "common/image.tpl"
        images             = $video.preview
        image_width        = $th_size
        image_height        = $th_size
        show_detailed_link = false
        obj_id             = $obj_id
    }
</a>