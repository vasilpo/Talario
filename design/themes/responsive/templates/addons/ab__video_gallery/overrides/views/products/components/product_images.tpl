{include file="addons/ab__video_gallery/components/helpers.tpl"}

{assign var="th_size" value=min($addons.ab__video_gallery.th_size|default:60,100)}

{$ab__vg_videos = $product.product_id|fn_ab__vg_get_videos}
{$ab__vg_settings = $product.product_id|fn_ab__vg_get_setting}
{$is_vertical = (($runtime.mode != 'quick_view') && ($addons.ab__video_gallery.vertical == 'Y'))}
{$is_thumbnails_gallery = $settings.Appearance.thumbnails_gallery === "YesNo::YES"|enum}
{$total_count = ($product.image_pairs|count + $ab__vg_videos|count + 1)}
{$total_images = $product.image_pairs|count}
{$product_pos_enum = "Addons\Ab_videoGallery\VideoProductPositionTypes"}

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

{if $image_pair_var || empty($ab__vg_videos)}
    {$total_images = $total_images + 1}
{/if}

{if !$preview_id}
    {assign var="preview_id" value=$product.product_id|uniqid}
{/if}

{if $total_count == 0 || $total_count == 1}
    {$v_gal_width = 0}
{elseif $total_count >= 6 && $settings.Appearance.thumbnails_gallery == "N"}
    {$v_gal_width = ($th_size * 2 + 16)}
{else}
    {$v_gal_width = ($th_size + 15)}
{/if}

<div class="ab_vg-images-wrapper{if $ab__vg_videos && $ab__vg_settings.replace_image === "YesNo::YES"|enum} ab_vg-replace_image{/if}" data-ca-previewer="true">
    <div id="product_images_{$preview_id}" class="ty-product-img cm-preview-wrapper{if $is_vertical} ab-vertical{/if}"{if $is_vertical} style="width: -webkit-calc(100% - {$v_gal_width}px);width: calc(100% - {$v_gal_width + 12}px);"{/if}>
        {$videos = $ab__vg_videos}
        {$image_iterator = 0}

        {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::TOP"|enum}
        {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::CUSTOM"|enum}

        {if $image_pair_var || empty($ab__vg_videos)}
            {$image_iterator = $image_iterator + 1}

            {include file="common/image.tpl" image_link_additional_attrs=["data-ca-image-order" => 0]
                     obj_id="`$preview_id`_`$image_id`" images=$image_pair_var
                     link_class="cm-image-previewer{if $ab__vg_videos && $ab__vg_settings.replace_image == 'Y'} hidden{/if}"
                     image_width=$image_width image_height=$image_height image_id="preview[product_images_`$preview_id`]"}

            {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::CUSTOM"|enum}
        {/if}

        {foreach from=$product.image_pairs item="image_pair"}
            {$image_iterator = $image_iterator + 1}

            {if $image_pair.image_id}
                {assign var="img_id" value=$image_pair.image_id}
            {else}
                {assign var="img_id" value=$image_pair.detailed_id}
            {/if}

            {include file="common/image.tpl" images=$image_pair link_class="cm-image-previewer hidden"
                     obj_id="`$preview_id`_`$img_id`" image_width=$image_width image_height=$image_height
                     image_id="preview[product_images_`$preview_id`]" image_link_additional_attrs=["data-ca-image-order"=>$image_iterator-1]}

            {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::CUSTOM"|enum}
        {/foreach}

        {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::CUSTOM"|enum}
        {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::BOTTOM"|enum}
    </div>

    {* Video popups content. For tab with videos or for popup onclick *}
    {include file="addons/ab__video_gallery/components/video_popups.tpl"}

    {if $product.image_pairs || $ab__vg_videos}
        {$image_counter = -1}
        {$videos = $ab__vg_videos}
        {$image_iterator = 0}
        {$video_iterator = 0}
        {$image_classes="ty-product-thumbnails__item cm-thumbnails-mini"}

        {if $is_thumbnails_gallery}
            {$image_classes="`$image_classes` cm-gallery-item gallery"}

            <input type="hidden" name="no_cache" value="1" />
        {/if}

        {capture name="product_thumbnails"}
            {strip}
                {$product_thumbnails_styles = ""}
                {$product_thumbnails_data = ""}
                {$product_thumbnails_classes = "ab__vg-product-thumbnails"}

                {if $is_thumbnails_gallery}
                    {$is_vertical_char = "YesNo::NO"|enum}

                    {if $is_vertical}
                        {$is_vertical_char = "YesNo::YES"|enum}
                    {/if}

                    {$product_thumbnails_classes = "`$product_thumbnails_classes` cm-image-gallery"}
                    {$product_thumbnails_data = "data-ca-cycle=`$addons.ab__video_gallery.cycle` data-ca-vertical=`$is_vertical_char`"}
                {else}
                    {if $is_vertical}
                        {$product_thumbnails_classes = "`$product_thumbnails_classes` ab-vg-vertical-thumbnails"}
                        {$product_thumbnails_styles = "`$product_thumbnails_styles` width: `$v_gal_width + 6`px;"}
                    {else}
                        {$product_thumbnails_classes = "`$product_thumbnails_classes` ty-center"}
                    {/if}
                {/if}

                <div class="{$product_thumbnails_classes}" {$product_thumbnails_data}{if $product_thumbnails_styles} style="{$product_thumbnails_styles}"{/if} id="images_preview_{$preview_id}">
                    {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::TOP"|enum}
                    {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::CUSTOM"|enum}

                    {if $image_pair_var}
                        {$image_counter = $image_counter + 1}
                        {$image_iterator = $image_iterator + 1}

                        {include file="addons/ab__video_gallery/components/product_thumbnail.tpl" image=$image_pair_var image_id=$image_pair_var.image_id|default:$image_pair_var.detailed_id thumbnail_type="image"}

                        {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::CUSTOM"|enum}
                    {/if}

                    {if $product.image_pairs}
                        {foreach from=$product.image_pairs item="image_pair"}
                            {$image_counter = $image_counter + 1}
                            {$image_iterator = $image_iterator + 1}

                            {if $image_pair.image_id}
                                {assign var="img_id" value=$image_pair.image_id}
                            {else}
                                {assign var="img_id" value=$image_pair.detailed_id}
                            {/if}

                            {include file="addons/ab__video_gallery/components/product_thumbnail.tpl" image=$image_pair image_id=$img_id thumbnail_type="image"}

                            {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::CUSTOM"|enum}
                        {/foreach}
                    {/if}

                    {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::CUSTOM"|enum}
                    {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::BOTTOM"|enum}
                </div>
            {/strip}
        {/capture}

        {if $is_thumbnails_gallery}
            {capture name="product_thumbnails"}
                <div class="ty-center ty-product-bigpicture-thumbnails_gallery{if $is_vertical} ab-vg-vertical-thumbnails{else} ab-vg-horizontal-thumbnails{/if}"{if $is_vertical}style="width: {$v_gal_width - 2}px"{/if}>
                    <div class="cm-image-gallery-wrapper ty-thumbnails_gallery ty-inline-block">
                        {$smarty.capture.product_thumbnails nofilter}
                    </div>
                </div>
            {/capture}
        {/if}

        {$smarty.capture.product_thumbnails nofilter}
    {/if}
</div>

{include file="common/previewer.tpl"}

{script src="js/addons/ab__video_gallery/product_image_gallery.js"}

{hook name="products:product_images"}{/hook}