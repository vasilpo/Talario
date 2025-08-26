{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="ab__stickers_demo_data_form" id="ab__stickers_demo_data_form">
<p>{__("ab__stickers.demodata.description")}</p>
<div class="table-responsive-wrapper">
<table class="table table-middle table-responsive" width="100%">
<thead>
<tr>
<th width="80%">{__("ab__stickers.demodata.table.description")}</th>
<th width="20%" style="text-align: center">{__("action")}</th>
</tr>
</thead>
<tbody>
{hook name="ab__stickers:demo_table"}
{foreach $demo_schema as $demo}
<tr>
<td data-th="{__("ab__stickers.demodata.table.description")}">{__($demo@key)}</td>
<td data-th="{__("action")}" style="text-align: center">
<a class="cm-ajax cm-post btn btn-primary" href="{"ab__stickers.demodata.stickers?path=`$demo.path`"|fn_url}">{__("add")}</a>
</td>
</tr>
{/foreach}
{/hook}
</tbody>
</table>
<hr style="margin-top:0" />
<p>{__("ab__stickers.demodata.download_graphic_stickers_source")}</p>
</div>
</form>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__stickers"}
{include file="common/mainbox.tpl"
title_start = __("ab__stickers")|truncate:40
title_end = __("ab__stickers.demodata")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
select_storefront=true
show_all_storefront=false
adv_buttons=$smarty.capture.adv_buttons
content_id="ab__stickers_demo_data_form"}