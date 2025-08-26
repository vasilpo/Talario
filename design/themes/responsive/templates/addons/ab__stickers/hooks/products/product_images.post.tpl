{$display_places = fn_ab__stickers_get_display_places(['places' => 'Addons\Ab_stickers\StickerPlaces::PRODUCT_IMAGE'|enum, 'hook_themes' => 'YesNo::YES'|enum])}

{if $display_places}
    {include file="addons/ab__stickers/views/ab__stickers/components/stickers_wrapper.tpl" display_places=$display_places is_hook=true}
{/if}
