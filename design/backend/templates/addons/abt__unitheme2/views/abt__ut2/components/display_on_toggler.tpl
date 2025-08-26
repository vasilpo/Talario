<div class="btn-group btn-group-checkbox" style="margin-top:10px">
{foreach [
"phone" => ["hidden_class" => "hidden-phone"],
"tablet" => ["hidden_class" => "hidden-tablet"],
"desktop" => ["hidden_class" => "hidden-desktop"]
] as $device}
{if $device == "phone"}
{$devices_icon = "icon-mobile-phone"}
{elseif $device == "tablet"}
{$devices_icon = "icon-tablet"}
{elseif $device == "desktop"}
{$devices_icon = "icon-desktop"}
{/if}
<input type="checkbox"
id="{$name}_{$device@key}"
class="cm-text-toggle btn-group-checkbox__checkbox"
{if $sticker_data.appearance.{$place}.user_class|strpos:$device.hidden_class === false}checked="checked"{/if}
data-ca-toggle-text="{$device.hidden_class}"
data-ca-toggle-text-mode="onDisable"
data-ca-toggle-text-target-elem-id="{$id}"
/>
<label class="btn btn-group-checkbox__label" for="{$name}_{$device@key}">
<i class="{$devices_icon}"></i>
{__("block_manager.availability.{$device@key}")}
</label>
{/foreach}
</div>