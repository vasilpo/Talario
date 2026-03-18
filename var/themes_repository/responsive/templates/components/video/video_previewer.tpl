{*
    $video
    $preview_id
    $obj_id
*}

{if !empty($video)}
    <a id="det_img_link_{$preview_id}_{$video.video_id}" {""}
        class="cm-image-previewer cm-previewer ty-previewer ty-product-images-gallery-video-link"
        data-ca-image-id="preview[product_images_{$preview_id}]"
    >
        <div class="ty-product-images-gallery-video" id="det_img_link_{$preview_id}_{$video.video_id}">
            <div class="ty-product-images-gallery-video__video-container" id="youtubeVideoContainer{$obj_id}">
                {include "components/video/video_iframe.tpl"
                    block_id               = "product_images_gallery_video_{$preview_id}"
                    video                  = $video
                    use_aspect_ratio       = true
                    video_additional_attrs = ["width" => "100%"]
                }
                <div class="hidden">
                    <img class="cm-image js-preview-for-previewers"
                        data-video-container="#youtubeVideoContainer{$obj_id}"
                        src="{$video.preview.detailed.image_path}"
                    />
                </div>
            </div>
        </div>
    </a>
{/if}
