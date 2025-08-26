{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="ab__dotd_demo_data_form" id="ab__dotd_demo_data_form">
<input type="hidden" name="promotion_data[company_id]" value="{$runtime.forced_company_id}">
<p>{__("ab__dotd.demodata_description")}</p>
<div class="table-responsive-wrapper">
<table class="table table-middle table-responsive" width="100%">
<thead>
<tr>
<th width="60%">{__("ab__dotd.demodata.table.description")}</th>
<th width="20%">{__("ab__dotd.demodata.table.action")}</th>
</tr>
</thead>
<tbody>
<tr>
<td data-th="{__("ab__dotd.demodata.table.description")}">{__("ab__dotd.demodata.table.add_promotions")}</td>
<td data-th="{__("ab__dotd.demodata.table.action")}" class="ab__dotd_dbutton">
<label><input type="checkbox" name="place_into_blocks[promotions]">&nbsp;&nbsp;{__("ab__dotd.demodata.place_into_blocks")}</label>
{btn type="list" class="cm-ajax cm-post btn btn-primary" text=__("add") dispatch="dispatch[ab__dotd.demodata.promotions]"}
</td>
</tr>
</tbody>
</table>
</div>
</form>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__deal_of_the_day" }
{include
file="common/mainbox.tpl"
title_start=__("ab__deal_of_the_day")|truncate:40
title_end=__("ab__dotd.demodata")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
content_id="ab__dotd_demo_data_form"
}