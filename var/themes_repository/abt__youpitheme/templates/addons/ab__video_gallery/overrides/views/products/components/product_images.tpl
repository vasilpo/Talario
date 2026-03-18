{include file="addons/ab__video_gallery/components/helpers.tpl"}

{assign var="th_size" value=min($addons.ab__video_gallery.th_size|default:60,100)}

{$product_pos_enum = "Addons\Ab_videoGallery\VideoProductPositionTypes"}

{$core_videos = []}

{if fn_ab__vg_core_is_allowed('product_videos')}
    {$core_videos = $product.videos|default:[]}
{/if}

{$ab__vg_videos = $product.product_id|fn_ab__vg_get_videos}
{$ab__vg_settings = $product.product_id|fn_ab__vg_get_setting}
{$total_images = $product.image_pairs|count + $core_videos|count}
{$total_count = ($product.image_pairs|count + $ab__vg_videos|count + $core_videos|count)}
{$is_thumbnails_gallery = $settings.Appearance.thumbnails_gallery === 'YesNo::YES'|enum}
{$replace_image = $ab__vg_settings.replace_image === 'YesNo::YES'|enum && $ab__vg_videos}
{$is_vertical = (($runtime.mode !== 'quick_view') && ($addons.ab__video_gallery.vertical === 'YesNo::YES'|enum) && $settings.abt__device !== 'mobile')}

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
    {$total_count = $total_count + 1}
    {$total_images = $total_images + 1}
{/if}

{if !$preview_id}
    {assign var="preview_id" value=$product.product_id|uniqid}
{/if}

{if $total_count == 0 || $total_count == 1}
    {$v_gal_width = 0}
{elseif $total_count >= 6 && $settings.Appearance.thumbnails_gallery === 'YesNo::NO'|enum}
    {$v_gal_width = ($th_size * 2 + 24)}
{else}
    {$v_gal_width = ($th_size + 12)}
{/if}

{if $product.product_id|fn_get_product_details_view != 'blocks/product_templates/bigpicture_template.tpl'}
	{$image_height_block = $settings.Thumbnails.product_details_thumbnail_height}
{/if}

<div class="ab_vg-images-wrapper clearfix{if $replace_image} ab_vg-replace_image{/if}" data-ca-previewer="true">
    {assign var="product_labels" value="product_labels_`$obj_prefix``$obj_id`"}
    {$smarty.capture.$product_labels nofilter}

    {$wrapper_styles = ""}
    {$wrapper_class = "ty-product-img cm-preview-wrapper ty-float-right"}

    {if $is_vertical}
        {$wrapper_class = "`$wrapper_class` ab-vg-vertical-thumbnails"}
        {$wrapper_styles = "width: -webkit-calc(100% - `$v_gal_width`px); width: calc(100% - `$v_gal_width`px);"}

        {if $image_height_block}
            {$wrapper_styles = "`$wrapper_styles` max-height: `$image_height_block + 44`px;"}
        {/if}

        {if $total_count >= 6}
            {$wrapper_class = "`$wrapper_class` two-col"}
        {elseif $total_count}
            {$wrapper_class = "`$wrapper_class` one-col"}
        {/if}
    {else}
        {$wrapper_class = "`$wrapper_class` ab-vg-horizontal-thumbnails"}
    {/if}

    <div id="product_images_{$obj_prefix}{$preview_id}" class="{$wrapper_class}"{if $wrapper_styles} style="{$wrapper_styles}"{/if}>
        {fn_ab__vg_set_template_videos($ab__vg_videos)}
        {fn_ab__vg_set_template_iterator('image_iterator')}
        {fn_ab__vg_set_template_iterator('video_iterator')}
        {fn_ab__vg_set_template_iterator('image_counter', -1)}

        {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::TOP"|enum}
        {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::CUSTOM"|enum}

        {$_link_class = "cm-image-previewer"}

        {if $replace_image || fn_ab__vg_get_template_iterator('image_iterator')}
            {$_link_class = "`$_link_class` hidden"}
        {/if}

        {if $core_videos && $product.show_videos_before_images === 'YesNo::YES'|enum}
            {call name=fn_ab__vg_get_core_videos}
        {/if}

        {if $image_pair_var || (empty($ab__vg_videos) && empty($core_videos))}
            {fn_ab__vg_increase_template_iterator('image_counter')}
            {fn_ab__vg_increase_template_iterator('image_iterator')}

            {include file="common/image.tpl"
                images=$image_pair_var
                obj_id="`$obj_prefix``$preview_id`_`$image_id`"
                image_id="preview[product_images_`$obj_prefix``$preview_id`]"
                image_width=$image_width
                image_height=$image_height
                link_class=$_link_class
                image_link_additional_attrs=[
                    "data-ca-image-order" => fn_ab__vg_get_template_iterator('image_counter') - 1
                ]
            }

            {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::CUSTOM"|enum}
        {/if}

        {foreach from=$product.image_pairs item="image_pair"}
            {if $image_pair.image_id}
                {assign var="img_id" value=$image_pair.image_id}
            {else}
                {assign var="img_id" value=$image_pair.detailed_id}
            {/if}

            {fn_ab__vg_increase_template_iterator('image_counter')}
            {fn_ab__vg_increase_template_iterator('image_iterator')}

            {include file="common/image.tpl"
                images=$image_pair
                obj_id="`$obj_prefix``$preview_id`_`$img_id`"
                image_id="preview[product_images_`$obj_prefix``$preview_id`]"
                image_width=$image_width
                image_height=$image_height
                link_class="cm-image-previewer hidden"
                image_link_additional_attrs=[
                    "data-ca-image-order" => fn_ab__vg_get_template_iterator('image_counter') - 1
                ]
            }

            {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::CUSTOM"|enum}
        {/foreach}

        {$_link_class = "cm-image-previewer"}

        {if $replace_image || fn_ab__vg_get_template_iterator('image_iterator')}
            {$_link_class = "`$_link_class` hidden"}
        {/if}

        {if $core_videos && $product.show_videos_before_images === 'YesNo::NO'|enum}
            {call name=fn_ab__vg_get_core_videos}
        {/if}

        {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::CUSTOM"|enum}
        {call name=fn_ab__vg_get_videos_by_pos position="$product_pos_enum::BOTTOM"|enum}
    </div>

    {* Video popups content. For tab with videos or for popup onclick *}
    {include file="addons/ab__video_gallery/components/video_popups.tpl"}

    {if $addons.image_zoom.status === 'ObjectStatuses::ACTIVE'|enum && $details_page && $settings.abt__device !== 'mobile'}
        <div class="ypi-text-image-zoom ty-center" {if $is_vertical}style="width: -webkit-calc(100% - {$v_gal_width}px); width: calc(100% - {$v_gal_width}px)"{/if}>
            <small>
                <i class="material-icons">&#xE8FF;</i>
                {__('abt__yt.hover_to_enlarge')}
            </small>
        </div>
    {/if}

    {if $product.image_pairs || $ab__vg_videos || $core_videos}
        {$image_classes="ty-product-thumbnails__item cm-thumbnails-mini"}

        {fn_ab__vg_set_template_videos($ab__vg_videos)}
        {fn_ab__vg_set_template_iterator('video_iterator')}
        {fn_ab__vg_set_template_iterator('image_iterator')}
        {fn_ab__vg_set_template_iterator('image_counter', -1)}

        {if $is_thumbnails_gallery}
            {$image_classes="`$image_classes` cm-gallery-item gallery"}

            <input type="hidden" name="no_cache" value="1" />
        {/if}

        {capture name="product_thumbnails"}
            {strip}
                {$product_thumbnails_styles = ""}
                {$product_thumbnails_classes = "ty-product-thumbnails"}

                {if $is_thumbnails_gallery}
                    {$is_vertical_char = 'YesNo::NO'|enum}

                    {if $is_vertical}
                        {$is_vertical_char = 'YesNo::YES'|enum}
                    {/if}

                    {$product_thumbnails_classes = "`$product_thumbnails_classes` cm-image-gallery"}
                    {$product_thumbnails_data = "`$product_thumbnails_data` data-ca-cycle=`$addons.ab__video_gallery.cycle`"}
                    {$product_thumbnails_data = "`$product_thumbnails_data` data-ca-vertical=`$is_vertical_char`"}
                {else}
                    {$product_thumbnails_classes = "`$product_thumbnails_classes` ty-center"}

                    {if $is_vertical}
                        {$product_thumbnails_classes = "`$product_thumbnails_classes` ab-vertical-thumbnails"}
                        {$product_thumbnails_styles = "`$product_thumbnails_styles` width: `$v_gal_width - 2`px;"}

                        {if $image_height_block}
                            {$product_thumbnails_styles = "`$product_thumbnails_styles` min-height: `$image_height_block - 24`px;"}
                        {/if}
                    {/if}
                {/if}

                <div class="{$product_thumbnails_classes}"{if $product_thumbnails_styles} style="{$product_thumbnails_styles}"{/if} {$product_thumbnails_data} id="images_preview_{$obj_prefix}{$preview_id}">
                    {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::TOP"|enum}
                    {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::CUSTOM"|enum}

                    {if $core_videos && $product.show_videos_before_images === 'YesNo::YES'|enum}
                        {call name=fn_ab__vg_get_core_videos_thumbs}
                    {/if}

                    {if $image_pair_var}
                        {fn_ab__vg_increase_template_iterator('image_counter')}
                        {fn_ab__vg_increase_template_iterator('image_iterator')}

                        {include file="addons/ab__video_gallery/components/product_thumbnail.tpl"
                            image=$image_pair_var
                            image_id=$image_pair_var.image_id|default:$image_pair_var.detailed_id
                            thumbnail_type="image"
                        }

                        {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::CUSTOM"|enum}
                    {/if}

                    {if $product.image_pairs}
                        {foreach from=$product.image_pairs item="image_pair"}
                            {fn_ab__vg_increase_template_iterator('image_counter')}
                            {fn_ab__vg_increase_template_iterator('image_iterator')}

                            {if $image_pair.image_id}
                                {assign var="img_id" value=$image_pair.image_id}
                            {else}
                                {assign var="img_id" value=$image_pair.detailed_id}
                            {/if}

                            {include file="addons/ab__video_gallery/components/product_thumbnail.tpl"
                                image=$image_pair
                                image_id=$img_id
                                thumbnail_type="image"
                            }

                            {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::CUSTOM"|enum}
                        {/foreach}
                    {/if}

                    {if $core_videos && $product.show_videos_before_images === 'YesNo::NO'|enum}
                        {call name=fn_ab__vg_get_core_videos_thumbs}
                    {/if}

                    {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::CUSTOM"|enum}
                    {call name=fn_ab__vg_get_videos_thumbs_by_pos position="$product_pos_enum::BOTTOM"|enum}
                </div>
            {/strip}
        {/capture}

        {if $is_thumbnails_gallery}
            {capture name="product_thumbnails"}
                <div class="ty-product-bigpicture-thumbnails_gallery ty-center clearfix{if $is_vertical} ab-vg-vertical-thumbnails{else} ab-vg-horizontal-thumbnails{/if}" {if $is_vertical}style="width: {$v_gal_width - 2}px;{if $image_height_block} max-height: {$image_height_block - 22}px;{/if}"{/if}>
                    <div class="cm-image-gallery-wrapper ty-thumbnails_gallery">
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