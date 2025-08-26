{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="ab__fn_demo_data_form" id="ab__fn_demo_data_form">
<p>{__("ab__fn.demodata_description")}</p>
<div class="table-responsive-wrapper">
<table class="table table-middle table-responsive" width="100%">
<thead>
<tr>
<th width="60%">{__("ab__fn.demodata.table.description")}</th>
<th width="20%">{__("ab__fn.demodata.table.action")}</th>
</tr>
</thead>
<tbody>
{foreach ["menu"] as $demo_type}
<tr>
<td data-th="{__("ab__fn.demodata.table.description")}">{__("ab__fn.demodata.table.add_`$demo_type`")}</td>
<td data-th="{__("ab__fn.demodata.table.action")}" class="ab-fn-dbutton">
<input type="hidden" name="place_into_blocks[{$demo_type}]" value="{"YesNo::NO"|enum}">
<label><input type="checkbox" name="place_into_blocks[{$demo_type}]" value="{"YesNo::YES"|enum}">&nbsp;&nbsp;{__("ab__fn.demodata.place_into_blocks")}</label>
{btn type="list" class="cm-ajax cm-post btn btn-primary" text=__("add") dispatch="dispatch[ab__fast_navigation.update_demodata.add_`$demo_type`]"}
</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
</form>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__fast_navigation"}
{include file="common/mainbox.tpl"
title_start = __("ab__fast_navigation")|truncate:40
title_end = __("ab__fast_navigation.demodata")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
select_storefront=true
show_all_storefront=false
adv_buttons=$smarty.capture.adv_buttons
content_id="ab__fn_demo_data_form"}