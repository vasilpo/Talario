{$text_style = "Tygh\Enum\Addons\Ab_stickers\StickerStyles::TEXT"|constant}
{$pictogram_style = "Tygh\Enum\Addons\Ab_stickers\StickerStyles::PICTOGRAM"|constant}
{if $sticker.style == $text_style && !$for_preview}
{if !$hide_link}
<a class="row-status ab-stickers-container__{$sticker.output_position_list}{$addons.ab__stickers.output_position}" href="{"ab__stickers.update?sticker_id=`$sticker.sticker_id`"|fn_url}">
{/if}
<span class="ab-sticker T-sticker {$sticker.appearance.appearance_style|default:$addons.ab__stickers.ts_appearance}" style="{if $sticker.appearance.uppercase_text == "Y"}text-transform:uppercase;font-size:.8em;{/if}color:{$sticker.appearance.text_color};background-color:{fn_ab__stickers_hex_to_rgba($sticker.appearance.sticker_bg, $sticker.appearance.sticker_bg_opacity)};{if $sticker.appearance.border_width != "0" && $addons.ab__stickers.ts_appearance != "beveled_angle"}box-shadow: inset 0 0 0 {$sticker.appearance.border_width} {$sticker.appearance.border_color}{/if}">{fn_ab__stickers_get_sticker_string_value($sticker.name_for_desktop|default:$sticker.name_for_admin, $ab__stickers_default_placeholders|default:[]) nofilter}</span>
{if !$hide_link}
</a>
{/if}
{else}
{$href = $sticker.main_pair.icon.https_image_path}
{if $sticker.style == $pictogram_style && !empty($sticker.main_pair_p)}
{$href = $sticker.main_pair_p.icon.https_image_path}
{/if}
{if !$hide_link && !$for_preview}
{$href = "ab__stickers.update?sticker_id=`$sticker.sticker_id`"|fn_url}
{/if}
{$image = $sticker.main_pair}
{$image_width=48}
{$image_hieght=48}
{if $sticker.style == $pictogram_style && !empty($sticker.main_pair_p)}
{$image = $sticker.main_pair_p}
{if $for_preview}
{$image_width=64}
{$image_hieght=64}
{/if}
{/if}
{include file="common/image.tpl" image=$image image_width=$image_width image_hieght=$image_hieght no_ids=true href=$href}
{/if}