{strip}
{capture name="mainbox"}
<p>{__('abt__ut2.icons.info')}</p>
<div class="table-responsive-wrapper">
<p>{__('abt__ut2.icons.ut2_icons')}</p>
<table class="table table-middle table-responsive abt-ut2-table" width="100%">
<thead>
<tr>
<th width="20%">{__("icon")}</th>
<th width="20%">{__("copy")}</th>
<th width="60%">{__("abt__ut2.icons.class")}</th>
</tr>
</thead>
<tbody>
{foreach $icons.ut2_icons as $ut2_icon}
{include file="addons/abt__unitheme2/views/abt__ut2/components/icon_row.tpl"}
{/foreach}
</tbody>
</table>
{hook name="abt__ut2:print_icons"}{/hook}
</div>
{/capture}
{/strip}
<style>
.abt-ut2-table th, .abt-ut2-table td {
text-align: center !important;
}
</style>
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="abt__unitheme2"}
{include
file="common/mainbox.tpl"
title_start=__("abt__unitheme2")|truncate:40
title_end=__("abt__ut2.icons")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
sidebar=$smarty.capture.sidebar
}
