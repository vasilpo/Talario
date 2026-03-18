{** Use this variables to check sticker style **}
{$text_style = 'Addons\Ab_stickers\StickerStyles::TEXT'|enum}
{$graphic_style = 'Addons\Ab_stickers\StickerStyles::GRAPHIC'|enum}
{$product_labels_place = 'Addons\Ab_stickers\StickerPlaces::PRODUCT_IMAGE'|enum}

{$show_stickers = $show_stickers|default:true}
{$controller_mode = "`$runtime.controller`.`$runtime.mode`"}

{if $controller_mode === "product_features.compare" && $block.type === "main"}
    {$show_stickers = false}
{/if}

{hook name="ab__stickers:show_stickers"}
{if $show_stickers}
    {if fn_ab__stickers_get_view_type($smarty.request, $block|default:[]) === 'detailed_page'}
        {$template = "detailed_page"}
    {elseif $block.type !== "main" && $block.properties.template}
        {$template = $block.properties.template}
    {elseif $ab__stickers_current_tmpl}
        {$template = $ab__stickers_current_tmpl}
    {/if}

    {$template_id = ''}
    {$displays = fn_ab__stickers_get_templates_displays()}

    {foreach $displays as $display}
        {if $display.tpl === $template}
            {$template_id = $display.id}
        {/if}
    {/foreach}

    {$product_id = 0}
    {$used_obj_id = $ab__stickers_obj_id|default:$obj_id}

    {if $product.product_id}
        {$product_id = $product.product_id}
    {elseif $smarty.request.product_id}
        {$product_id = $smarty.request.product_id}
    {/if}

    {$display_places = $display_places|default:[]}

    {foreach $display_places as $display_place}
        {$stickers_capture_name = "ab__stickers_`$display_place['id']`_`$used_obj_id`"}

        {capture name=$stickers_capture_name}{/capture}
    {/foreach}

    {foreach $product.ab__stickers as $sticker_display_place_key => $sticker_display_place}
        {if $obj_prefix && $sticker_display_place_key === $product_labels_place}
            {$obj_real_id = "`$obj_prefix``$used_obj_id`"}
        {else}
            {$obj_real_id = "$used_obj_id"}
        {/if}

        {capture name="ab__stickers_`$sticker_display_place_key`_`$obj_real_id`"}
            {hook name="ab__stickers:product_stickers"}
                {hook name="ab__stickers:product_stickers_pre"}
                {if $details_page && $sticker_display_place_key === $product_labels_place}
                    <div class="ab-stickers-wrapper ab-hidden">
                {/if}
                {/hook}
                {foreach $sticker_display_place as $position}
                    {$pos_counter = 0}
                    {$container_class = "ab-stickers-container__{str_replace('.', '_', $sticker_display_place_key)}"}

                    {if $sticker_display_place_key === $product_labels_place}
                        {$container_class = "$container_class ab-stickers-container__`$position@key``$addons.ab__stickers.output_position`"}
                        {$container_class = "$container_class {$addons.ab__stickers.{"`$position@key``$addons.ab__stickers.output_position`_output_type"}}-filling"}
                    {/if}

                    <div class="ab-stickers-container {$container_class}">
                        {foreach $position as $sticker}
                            {if $pos_counter < $addons.ab__stickers.{"`$position@key``$addons.ab__stickers.output_position`_max_count"} || $sticker_display_place_key !== $product_labels_place}
                                {if $sticker_display_place_key === $product_labels_place}
                                    {$pos_counter = $pos_counter + 1}
                                {/if}

                                {$view_type = $sticker.display_on[$template]|default:('Addons\Ab_stickers\StickerSizes::SMALL'|enum)}
                                {$sticker_class = ""}

                                {if $sticker.style == $graphic_style}
                                    {$sticker_class = " small-image-size-`$sticker.appearance.small_size_image_size`"}
                                    {$sticker_class = "$sticker_class full-image-size-`$sticker.appearance.full_size_image_size`"}
                                {/if}

                                <div class="ab-sticker-{$view_type}{$sticker_class}" data-ab-sticker-id="{$sticker.sticker_id}-{$product_id}{if $template_id}-{$template_id}{/if}"></div>
                            {/if}
                        {/foreach}
                    </div>
                {/foreach}
                {if $details_page && $sticker_display_place_key === $product_labels_place}
                    </div>
                {/if}
            {/hook}
        {/capture}
    {/foreach}

    {foreach $display_places as $display_place}
        {$display_place_id = $display_place.id}
        {$display_place_pos = $display_place.position}
        {$display_place_capture_key = explode('.', $display_place_id)}
        {$display_place_capture_key = $display_place_capture_key[0]|default:$product_labels_place}

        {if $obj_prefix && $display_place_capture_key === $product_labels_place}
            {$obj_real_id = "`$obj_prefix``$used_obj_id`"}
        {else}
            {$obj_real_id = "$used_obj_id"}
        {/if}

        {$capture_name = "`$display_place_capture_key`_`$obj_real_id`"}

        {if $is_hook}
            {$capture_name = "`$capture_name`_hook"}
        {/if}

        {$sticker_exists = false}
        {$stickers_capture_name = "ab__stickers_`$display_place_id`_`$obj_real_id`"}
        {$stickers_capture_content = $smarty.capture[$stickers_capture_name]}

        {if trim(fn_ab__stickers_strip_tags($stickers_capture_content, "<div>"))}
            {$sticker_exists = true}
        {/if}

        {if $sticker_exists}
            {capture name=$capture_name}
                {if $is_hook || !$display_place_pos|in_array:['before', 'after']}
                    {$stickers_capture_content nofilter}
                {elseif $display_place_pos === 'before'}
                    {$stickers_capture_content nofilter}
                    {$smarty.capture.$capture_name nofilter}
                {elseif $display_place_pos === 'after'}
                    {$smarty.capture.$capture_name nofilter}
                    {$stickers_capture_content nofilter}
                {/if}
            {/capture}

            {if $is_hook}
                {$smarty.capture.$capture_name nofilter}
            {/if}
        {elseif $display_place_capture_key === $product_labels_place}
            {capture name=$capture_name}{/capture}
        {/if}

        {capture name=$stickers_capture_name}{/capture}
    {/foreach}
{/if}
{/hook}