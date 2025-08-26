{$controller_mode = "`$runtime.controller`.`$runtime.mode`"}

{$skip = false}
{if $controller_mode == "product_features.compare" && $block.type == "main"}
    {$skip = true}
{/if}

{if fn_ab__stickers_get_view_type($smarty.request, $block|default:[]) === 'detailed_page'}
    {$template = "detailed_page"}
{elseif $block.type != "main" && $block.properties.template}
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

{$pictogram_views = [
    'Addons\Ab_stickers\StickerSizes::SMALL'|enum => [0],
    'Addons\Ab_stickers\StickerSizes::FULL'|enum => [0]
]}

{foreach $product.ab__s_pictograms as $pictogram}
    {$view_type = $pictogram.display_on[$template]|default:('Addons\Ab_stickers\StickerSizes::FULL'|enum)}
    {$view_type_var = "`$view_type`_image_size"}
    {$pictogram_views[$view_type][] = $pictogram.appearance.$view_type_var}
{/foreach}

{$pictogram_views = array_unique(array_merge(
    $pictogram_views['Addons\Ab_stickers\StickerSizes::SMALL'|enum],
    $pictogram_views['Addons\Ab_stickers\StickerSizes::FULL'|enum]
))}

{if $skip === false}
    {hook name="ab__stickers:product_pictograms"}
        {if $product.ab__s_pictograms}
            {hook name="ab__stickers:product_pictograms_pre"}
            <div class="ab-s-pictograms-wrapper ab-s-pictograms-wrapper-h-{$pictogram_views|max}{if $position} ab-s-pictograms-wrapper-{$position}{/if}{if $key_1} ab-s-pictograms-wrapper-{$key_1}{/if}">
            {/hook}
            {if $product.ab__s_pictograms}
                {foreach $product.ab__s_pictograms as $pictogram}
                    {$view_type = $pictogram.display_on[$template]|default:('Addons\Ab_stickers\StickerSizes::SMALL'|enum)}

                    <div class="ab-s-pictogram ab-sticker-{$view_type|default:('Addons\Ab_stickers\StickerSizes::FULL'|enum)} small-image-size-{$pictogram.appearance.small_size_image_size} full-image-size-{$pictogram.appearance.full_size_image_size}" data-ab-sticker-id="{$pictogram@key}-{$product.product_id}{if $template_id}-{$template_id}{/if}"></div>
                {/foreach}
            {/if}
            </div>
        {/if}
    {/hook}
{/if}