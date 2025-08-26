{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="ab__mb_demo_data_form" id="ab__mb_demo_data_form">
<p>{__("ab__mb.demodata.description")}</p>
<div class="table-responsive-wrapper">
<table class="table table-middle table-responsive" width="100%">
<thead>
<tr>
<th width="80%">{__("ab__mb.demodata.table.description")}</th>
<th width="20%" style="text-align: center">{__("action")}</th>
</tr>
</thead>
<tbody>
{$arr = ['blocks']}
{foreach $arr as $demo}
<tr>
<td data-th="{__("ab__mb.demodata.table.description")}">{__("ab__mb.demodata.table.add_{$demo}")}</td>
<td data-th="{__("action")}" style="text-align: center">{btn type="list" class="cm-ajax cm-post btn btn-primary" text=__("add") dispatch="dispatch[ab__motivation_block.demodata.{$demo}]"}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
</form>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__motivation_block"}
{include file="common/mainbox.tpl"
title_start = __("ab__motivation_block")|truncate:40
title_end = __("ab__motivation_block.demodata")
adv_buttons=$smarty.capture.adv_buttons
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
select_storefront=true
content_id="ab__mb_demo_data_form"}