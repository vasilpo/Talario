{style src="addons/ab__stickers/styles.less"}
{foreach fn_ab__stickers_sticker_get_ts_appearance_styles() as $appearance_style}
    {style src="addons/ab__stickers/`$appearance_style@key`_stickers.less"}
{/foreach}
{style src="addons/ab__stickers/theme.less"}
{if $language_direction == "rtl"}
    {style src="addons/ab__stickers/rtl.less"}
{/if}