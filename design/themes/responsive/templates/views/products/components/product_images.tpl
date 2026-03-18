{assign var="th_size" value=$thumbnails_size|default:35}

{if $product.main_pair.icon || $product.main_pair.detailed}
    {assign var="image_pair_var" value=$product.main_pair}
{elseif $product.option_image_pairs}
    {assign var="image_pair_var" value=$product.option_image_pairs|reset}
{/if}

{if $image_pair_var.image_id}
    {assign var="image_id" value=$image_pair_var.image_id}
{else}
    {assign var="image_id" value=$image_pair_var.detailed_id}
{/if}

{if !$preview_id}
    {$preview_id = $product.product_id}
{/if}

{$product_videos = $product.videos}
{$style_video_thumbnails = "width: {$th_size}px; height: {$th_size}px"}

{hook name="product:product_videos"}
    {capture name="product_videos"}
        {if (!empty($product_videos))}
            {foreach $product_videos as $video}
                {include "components/video/video_previewer.tpl"
                    video      = $video
                    obj_id     = "{$obj_prefix}{$preview_id}_{$video.video_id}"
                    preview_id = $preview_id
                }
            {/foreach}
        {/if}

        {if empty($image_pair_var)}
            {include file="common/image.tpl" obj_id="`$obj_prefix``$preview_id`_`$image_id`" images=$image_pair_var link_class="cm-image-previewer" image_width=$image_width image_height=$image_height image_id="preview[product_images_`$obj_prefix``$preview_id`]"}
        {/if}
    {/capture}
{/hook}

<div class="ty-product-img cm-preview-wrapper" id="product_images_{$obj_prefix}{$preview_id}">

    {if $product.show_videos_before_images === "YesNo::YES"|enum}
        {$smarty.capture.product_videos nofilter}
    {/if}

    {if !empty($image_pair_var)}
        {include file="common/image.tpl" obj_id="`$obj_prefix``$preview_id`_`$image_id`" images=$image_pair_var link_class="cm-image-previewer" image_width=$image_width image_height=$image_height image_id="preview[product_images_`$obj_prefix``$preview_id`]"}
    {/if}

    {foreach from=$product.image_pairs item="image_pair"}
        {if $image_pair}
            {if $image_pair.image_id}
                {assign var="img_id" value=$image_pair.image_id}
            {else}
                {assign var="img_id" value=$image_pair.detailed_id}
            {/if}
            {include file="common/image.tpl" images=$image_pair link_class="cm-image-previewer hidden" obj_id="`$obj_prefix``$preview_id`_`$img_id`" image_width=$image_width image_height=$image_height image_id="preview[product_images_`$obj_prefix``$preview_id`]"}
        {/if}
    {/foreach}

    {if $product.show_videos_before_images === "YesNo::NO"|enum}
        {$smarty.capture.product_videos nofilter}
    {/if}
</div>

{if $product.image_pairs || !empty($product.videos)}
    {if $settings.Appearance.thumbnails_gallery == "Y"}
        {$image_counter = 0}
        <input type="hidden" name="no_cache" value="1" />
        {strip}
        <div class="ty-center ty-product-bigpicture-thumbnails_gallery">
            <div class="cm-image-gallery-wrapper ty-thumbnails_gallery ty-inline-block">
                {strip}
                <div class="ty-product-thumbnails owl-carousel cm-image-gallery" id="images_preview_{$obj_prefix}{$preview_id}">
                    {if $product.show_videos_before_images === "YesNo::YES"|enum  && !empty($product_videos)}
                        {foreach $product_videos as $video}
                            {include "components/video/video_thumbnails_gallery.tpl"
                                class         = "cm-gallery-item"
                                style         = $style_video_thumbnails
                                video         = $video
                                image_counter = $image_counter
                                th_size       = $th_size
                                obj_id        = "{$obj_prefix}{$preview_id}_{$video.video_id}_mini"
                            }
                            {$image_counter = $image_counter + 1}
                        {/foreach}
                    {/if}
                    {if $image_pair_var}
                        <div class="cm-item-gallery ty-float-left">
                            <a data-ca-gallery-large-id="det_img_link_{$obj_prefix}{$preview_id}_{$image_id}" {""}
                               class="cm-gallery-item cm-thumbnails-mini {if $product.show_videos_before_images === "YesNo::NO"|enum}active{/if} ty-product-thumbnails__item" {""}
                               style="width: {$th_size}px" {""}
                               data-ca-image-order="{$image_counter}" {""}
                               data-ca-parent="#product_images_{$obj_prefix}{$preview_id}"
                            >
                           {include file="common/image.tpl" images=$image_pair_var image_width=$th_size image_height=$th_size show_detailed_link=false obj_id="`$obj_prefix``$preview_id`_`$image_id`_mini"}
                            </a>
                        </div>
                    {/if}
                    {if $product.image_pairs}
                        {foreach from=$product.image_pairs item="image_pair"}
                            {$image_counter = $image_counter + 1}
                            {if $image_pair}
                                <div class="cm-item-gallery ty-float-left">
                                    {if $image_pair.image_id}
                                        {assign var="img_id" value=$image_pair.image_id}
                                    {else}
                                        {assign var="img_id" value=$image_pair.detailed_id}
                                    {/if}
                                    <a data-ca-gallery-large-id="det_img_link_{$obj_prefix}{$preview_id}_{$img_id}" {""}
                                       class="cm-gallery-item cm-thumbnails-mini ty-product-thumbnails__item" {""}
                                       data-ca-image-order="{$image_counter}" {""}
                                       data-ca-parent="#product_images_{$obj_prefix}{$preview_id}"
                                    >
                                    {include file="common/image.tpl" images=$image_pair image_width=$th_size image_height=$th_size show_detailed_link=false obj_id="`$obj_prefix``$preview_id`_`$img_id`_mini"}
                                    </a>
                                </div>
                            {/if}
                        {/foreach}
                    {/if}
                    {if $product.show_videos_before_images === "YesNo::NO"|enum  && !empty($product_videos)}
                        {if $image_counter === 0}
                            {$image_counter = (empty($image_pair_var)) ? 0 : 1}
                        {elseif !empty($product.image_pairs)}
                            {$image_counter = $image_counter + 1}
                        {/if}
                        {foreach $product_videos as $video}
                            {include "components/video/video_thumbnails_gallery.tpl"
                                class         = "cm-gallery-item"
                                style         = $style_video_thumbnails
                                video         = $video
                                image_counter = $image_counter
                                th_size       = $th_size
                                obj_id        = "{$obj_prefix}{$preview_id}_{$video.video_id}_mini"
                            }
                            {$image_counter = $image_counter + 1}
                        {/foreach}
                    {/if}
                </div>
                {/strip}
            </div>
        </div>
        {/strip}
    {else}
        {if $product.details_layout === "bigpicture_template"}
            {$style_video_thumbnails = "aspect-ratio: {$th_size} / {$th_size};"|cat:$style_video_thumbnails}
        {/if}
        {$image_counter = 0}
        <div class="ty-product-thumbnails ty-center cm-image-gallery" id="images_preview_{$obj_prefix}{$preview_id}" style="width: {$image_width}px;">
            {strip}
                {if $product.show_videos_before_images === "YesNo::YES"|enum  && !empty($product_videos)}
                    {foreach $product_videos as $video}
                        {include "components/video/video_thumbnails_gallery.tpl"
                            video         = $video
                            image_counter = $image_counter
                            th_size       = $th_size
                            obj_id        = "{$obj_prefix}{$preview_id}_{$video.video_id}_mini"
                            style         = $style_video_thumbnails
                        }
                        {$image_counter = $image_counter + 1}
                    {/foreach}
                {/if}
                {if $image_pair_var}
                    <a data-ca-gallery-large-id="det_img_link_{$obj_prefix}{$preview_id}_{$image_id}" {""}
                        class="cm-thumbnails-mini {if $product.show_videos_before_images === "YesNo::NO"|enum}active{/if} ty-product-thumbnails__item" {""}
                        data-ca-image-order="{$image_counter}" {""}
                        data-ca-parent="#product_images_{$obj_prefix}{$preview_id}"
                    >
                        {include file="common/image.tpl" images=$image_pair_var image_width=$th_size image_height=$th_size show_detailed_link=false obj_id="`$obj_prefix``$preview_id`_`$image_id`_mini"}
                    </a>
                {/if}

                {if $product.image_pairs}
                    {foreach from=$product.image_pairs item="image_pair"}
                        {$image_counter = $image_counter + 1}
                        {if $image_pair}
                                {if $image_pair.image_id == 0}
                                    {assign var="img_id" value=$image_pair.detailed_id}
                                {else}
                                    {assign var="img_id" value=$image_pair.image_id}
                                {/if}
                                <a data-ca-gallery-large-id="det_img_link_{$obj_prefix}{$preview_id}_{$img_id}" {""}
                                        class="cm-thumbnails-mini ty-product-thumbnails__item" {""}
                                        data-ca-image-order="{$image_counter}" {""}
                                        data-ca-parent="#product_images_{$obj_prefix}{$preview_id}"
                                >
                                {include file="common/image.tpl" images=$image_pair image_width=$th_size image_height=$th_size show_detailed_link=false obj_id="`$obj_prefix``$preview_id`_`$img_id`_mini"}
                                </a>
                        {/if}
                    {/foreach}
                {/if}

                {if $product.show_videos_before_images === "YesNo::NO"|enum && !empty($product_videos)}
                    {if $image_counter === 0}
                        {$image_counter = (empty($image_pair_var)) ? 0 : 1}
                    {elseif !empty($product.image_pairs)}
                        {$image_counter = $image_counter + 1}
                    {/if}
                    {foreach $product_videos as $video}
                        {include "components/video/video_thumbnails_gallery.tpl"
                            video         = $video
                            image_counter = $image_counter
                            th_size       = $th_size
                            obj_id        = "{$obj_prefix}{$preview_id}_{$video.video_id}_mini"
                            style         = $style_video_thumbnails
                        }
                        {$image_counter = $image_counter + 1}
                    {/foreach}
                {/if}
            {/strip}
        </div>
    {/if}
{/if}

{include file="common/previewer.tpl"}

{foreach $video_sources as $video_source => $source}
    {$source_name = $video_source|lower}
    {include "components/video/providers/{$source_name}.tpl"}
{/foreach}

{script src="js/tygh/product_image_gallery.js"}
{script src="js/tygh/video_state_manager.js"}
{script src="js/tygh/enlarge_video_viewers.js"}

{hook name="products:product_images"}{/hook}
