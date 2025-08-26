{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="abt__ut2_demo_data_form" id="abt__ut2_demo_data_form">
<p>{__("abt__ut2.demodata.description")}</p>
<div class="table-responsive-wrapper">
<table class="table table-middle table-responsive" width="100%">
<thead>
<tr>
<th width="80%">{__("abt__ut2.demodata.table.description")}</th>
<th width="20%" style="text-align: center">{__("action")}</th>
</tr>
</thead>
<tbody>
{foreach $demo_data_types as $demo}
<tr>
<td data-th="{__("abt__ut2.demodata.table.description")}">{__("abt__ut2.demodata.table.add_{$demo}")}</td>
<td data-th="{__("action")}" class="abt__ut2_dbutton">
{if !in_array($demo, ["fly_menu", "blog"])}
<label><input type="checkbox" name="place_into_blocks[{$demo}]">&nbsp;&nbsp;{__("abt__ut2.demo.place_into_blocks")}</label>
{/if}
<span style="margin-left: auto;">
{btn type="list" class="cm-ajax cm-post btn btn-primary" text=__("add") dispatch="dispatch[abt__ut2.demodata.{$demo}]"}
</span>
</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
</form>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="abt__unitheme2"}
{include
file="common/mainbox.tpl"
title_start = __("abt__unitheme2")|truncate:40
title_end = __("abt__ut2.demodata")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
select_storefront=true
show_all_storefront=false
content_id="abt__ut2_demo_data_form"
}