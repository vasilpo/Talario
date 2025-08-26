<div class="controls" id="{$name}">
<div class="btn-group btn-group-checkbox">
{foreach [
"phone" => ["hidden_class" => "hidden-phone"],
"tablet" => ["hidden_class" => "hidden-tablet"],
"desktop" => ["hidden_class" => "hidden-desktop"]
] as $device_key => $device}
{if $device_key == "phone"}
{$devices_icon = "icon-mobile-phone"}
{elseif $device_key == "tablet"}
{$devices_icon = "icon-tablet"}
{elseif $device_key == "desktop"}
{$devices_icon = "icon-desktop"}
{/if}
<input type="checkbox"
id="{$name}_{$device@key}"
class="cm-text-toggle btn-group-checkbox__checkbox"
{if $sticker_data.user_class.{$tpl}|strpos:$device.hidden_class === false}checked="checked"{/if}
data-ca-toggle-text="{$device.hidden_class}"
data-ca-toggle-text-mode="onDisable"
data-ca-toggle-text-target-elem-id="ab__stickers_user_class_{$place}"
/>
<label class="btn btn-group-checkbox__label" for="{$name}_{$device@key}">
{include_ext file="common/icon.tpl" class=$devices_icon}
{__("block_manager.availability.{$device@key}")}
</label>
{/foreach}
</div>
</div>