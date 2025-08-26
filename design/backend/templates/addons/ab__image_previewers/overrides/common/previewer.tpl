<!--override with ab__image_previewers-->
{if fn_ab__ip_is_ab_previewer()}
{script src="js/addons/ab__image_previewers/previewers/{$settings.Appearance.default_image_previewer}.previewer.js"}
{else}
{script src="js/tygh/previewers/`$settings.Appearance.default_image_previewer`.previewer.js"}
{/if}
