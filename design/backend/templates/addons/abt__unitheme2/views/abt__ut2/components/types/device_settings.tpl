{strip}
{capture name="abt__ut2_devices"}
{foreach $settings as $s}
{if (!$s.is_for_all_devices or $s.is_for_all_devices != "YesNo::YES"|enum) and (!$s.is_group or $s.is_group != "YesNo::YES"|enum)}
{$f_id="settings.abt__ut2.`$section`.`$f_group``$s@key`"}
{$r_id="`$section`.`$f_group``$s@key`"}
<tr id="{$r_id}"{if $smarty.request.highlighted === $r_id} class="ut2-highlighted-setting-row"{/if}>
<td data-th="{__("abt__ut2.settings.name")}">
{include file="addons/abt__unitheme2/views/abt__ut2/components/setting_label.tpl" label_suffix='.desktop'}
</td>
{if $enable_position_fields == "Y"}
<td>
{if $s.exclude_position_field != "Y"}
{include file="addons/abt__unitheme2/views/abt__ut2/components/value.tpl"
f_type="position"
f_id=$f_id
f_section=$section
f_name=$s@key
f=array_merge($s, ["type"=>"input", "class" => "input-micro"])
label=$label
}
{/if}
</td>
{/if}
{foreach ["desktop", "tablet", "mobile"] as $device_type}
<td data-th="{__("abt__ut2.settings.value.`$device_type`")}">
{include file="addons/abt__unitheme2/views/abt__ut2/components/value.tpl"
f_type=$device_type
f_id=$f_id
f_section=$section
f_name=$s@key
f=$s
label=$label
}
</td>
{/foreach}
</tr>
{/if}
{/foreach}
{/capture}
{if $smarty.capture.abt__ut2_devices|trim|strlen}
<table class="table table-middle table-responsive">
<thead>
<tr>
<th width="25%">{__("abt__ut2.settings.name")}</th>
{if $enable_position_fields == "Y"}
<th>
{__("abt__ut2.settings.position")}
</th>
{/if}
<th width="25%">{__("abt__ut2.settings.value.desktop")}</th>
<th width="25%">{__("abt__ut2.settings.value.tablet")}</th>
<th width="25%">{__("abt__ut2.settings.value.mobile")}</th>
</tr>
</thead>
<tbody>
{$smarty.capture.abt__ut2_devices nofilter}
</tbody>
</table>
{/if}
{/strip}