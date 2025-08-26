{if $runtime.action == 'change_licenses'}
<div id="ab__am_tools_change_licenses">
<form action="{""|fn_url}" method="post" name="ab__am_change_licenses_form" class="cm-disable-empty form-horizontal form-edit ">
<table class="table ab-am-table">
<thead>
<tr>
<th width="1%" nowrap>ID</th>
<th width="1%" nowrap>Ver.</th>
<th width="1%" nowrap>Build</th>
<th width="1%" nowrap>Code</th>
<th width="90">New code</th>
</tr>
</thead>
<tbody>
{foreach $d.installed_addons as $addon_id => $addon}
{if !preg_match('#^([0-9]{1,2})[.]([0-9]{1,2})[.]([0-9]{1,2})|([0-9]{1,2})[.]([0-9]{1,2})[.]([0-9]{1,2})[.]([a-z]{1})$#', $addon.v)}
{continue}
{/if}
<tr>
<td>{$addon_id}</td>
<td>v{$addon.v}</td>
<td>{$addon.b}</td>
<td><code>{$addon.c}</code><input type="hidden" name="ab__am[{$addon_id}][current]" value="{$addon.c}"></td>
<td><input type="text" name="ab__am[{$addon_id}][new]" value="" class="cm-trim"></td>
</tr>
{/foreach}
</tbody>
</table>
<div class="buttons-container">
{include file="buttons/button.tpl" but_text=__("ab__am.tools.change_licenses.btn") but_name="dispatch[ab__am.tools.change_licenses]" but_meta="btn btn-primary"}
</div>
</form>
<!--ab__am_tools_change_licenses--></div>
{elseif $runtime.action == 'fix_licenses'}
<div id="ab__am_tools_fix_licenses">
<form action="{""|fn_url}" method="post" name="ab__am_change_licenses_form" class="cm-disable-empty form-horizontal form-edit ">
<table class="table ab-am-table">
<thead>
<tr>
<th width="1%" nowrap>ID</th>
<th width="50%" nowrap>Addon XML</th>
<th width="50%" nowrap>Addon DB</th>
<th width="1%" nowrap>Fix?</th>
</tr>
</thead>
<tbody>
{foreach $bad_licenses as $addon_id => $addon}
{if !preg_match('#^([0-9]{1,2})[.]([0-9]{1,2})[.]([0-9]{1,2})|([0-9]{1,2})[.]([0-9]{1,2})[.]([0-9]{1,2})[.]([a-z]{1})$#', $addon.xml.v)}
{continue}
{/if}
<tr>
<td>{$addon_id}</td>
<td>
Ver.: v{$addon.xml.v}<br>
Code: <code>{$addon.xml.c}</code><br>
Build: {$addon.xml.b}
</td>
<td>
Ver.: v{$addon.db.v}<br>
Code: <code>{$addon.db.c}</code><br>
Build: {$addon.db.b}
</td>
<td>
<input type="checkbox" name="ab__am[{$addon_id}][fix]" value="Y">
<input type="hidden" name="ab__am[{$addon_id}][xml][v]" value="{$addon.xml.v}">
<input type="hidden" name="ab__am[{$addon_id}][xml][c]" value="{$addon.xml.c}">
<input type="hidden" name="ab__am[{$addon_id}][xml][b]" value="{$addon.xml.b}">
</td>
</tr>
{/foreach}
</tbody>
</table>
<div class="buttons-container">
{include file="buttons/button.tpl" but_text=__("ab__am.tools.fix_licenses.btn") but_name="dispatch[ab__am.tools.fix_licenses]" but_meta="btn btn-primary"}
</div>
</form>
<!--ab__am_tools_fix_licenses--></div>
{/if}
