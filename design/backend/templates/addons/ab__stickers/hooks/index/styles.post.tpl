{style src="addons/ab__stickers/styles.less"}
{style src="../../themes/responsive/css/addons/ab__stickers/styles.less"}
{foreach fn_ab__stickers_sticker_get_ts_appearance_styles() as $appearance_style}
{style src="../../themes/responsive/css/addons/ab__stickers/`$appearance_style@key`_stickers.less"}
{/foreach}
{$theme = "responsive"}
{if $runtime.layout.theme_name == "abt__unitheme2" || $runtime.layout.theme_name == "abt__youpitheme"}
{$theme == $runtime.layout.theme_name}
{/if}
{style src="../../themes/`$theme`/css/addons/ab__stickers/theme.less"}