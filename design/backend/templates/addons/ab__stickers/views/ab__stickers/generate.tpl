{capture name="mainbox"}
{$limit = 20}
{$cron_limit = $limit * 10}
{if $runtime.is_multiple_storefronts}
{if $smarty.request.storefront_id}
{$storefront_id=$smarty.request.storefront_id}
{else}
{$storefront_id=$app.storefront->storefront_id}
{/if}
{/if}
{$cron_company_id = fn_get_runtime_company_id()}
{$cron_types = [
[
"name" => "generate_links",
"dispatch" => "ab__stickers.cron.generate_links",
"has_button" => true,
"time" => "5 1 * * *",
"no_limit" => true,
"commet" => true
],
[
"name" => "generate_pictograms",
"dispatch" => "ab__stickers.cron.generate_pictograms",
"has_button" => true,
"time" => "25 1 * * *",
"commet" => true
]
]}
{foreach $cron_types as $key => $cron_data}
{$minutes = 5 * ($key % 12)}
{$cron_time = $cron_data.time|default:"`$minutes` 11 * * * "}
{$cron_cmd = "`$cron_time` php `$config.dir.root`/`$config.admin_index` -p --dispatch=`$cron_data.dispatch`"}
{if !$cron_data.no_limit}
{$cron_limit_text = " --limit=`$cron_limit`"}
{else}
{$cron_limit_text = ""}
{/if}
{if $cron_company_id}
{$cron_cmd = "`$cron_cmd` --switch_company_id=`$cron_company_id`"}
{/if}
{$cron_cmd = "`$cron_cmd` --storefront_id=`$app['storefront']->storefront_id``$cron_limit_text`"}
<div class="cron-block">
{include file="common/subheader.tpl" title=__("ab__stickers.cron.`$cron_data.name`_title")}
{include file="common/widget_copy.tpl" widget_copy_text=__("ab__stickers.cron.`$cron_data.name`_text") widget_copy_code_text=$cron_cmd}
</div>
{/foreach}
<div class="table-responsive-wrapper">
<table class="table table-middle table-responsive" width="100%">
<thead>
<tr>
<th width="80%">{__("description")}</th>
<th width="20%" style="text-align: center">{__("action")}</th>
</tr>
</thead>
<tbody>
{foreach $cron_types as $cron_data}
{if $cron_data.has_button}
{$suffix = ""}
{$name_suffix = ""}
{if (!$cron_data.no_limit)}
{$limit_text = "&limit=$limit"}
{else}
{$limit_text = ""}
{/if}
<tr>
<td data-th="{__("description")}">
<div>
{__("ab__stickers.notify_`$cron_data.name`")}
</div>
{if (!$cron_data.no_limit)}
<small class="muted">
{__("ab__stickers.notify_limit",["[limit]"=>$limit])}
</small>
{/if}
</td>
<td data-th="{__('action')}" style="text-align: center;">
{include file="buttons/button.tpl" but_href="`$cron_data.dispatch`?storefront_ids=`$app['storefront']->storefront_id``$limit_text``$suffix`"|fn_url but_text=__("go") but_role="action" but_meta="cm-ajax cm-post {if ($cron_data.commet)}cm-comet{/if}"}
</td>
</tr>
{/if}
{/foreach}
</tbody>
</table>
</div>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__stickers"}
{include file="common/mainbox.tpl"
title_start = __("ab__stickers")|truncate:40
title_end = __("ab__stickers.cron_jobs")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
select_storefront=true
show_all_storefront=false
adv_buttons=$smarty.capture.adv_buttons
content_id="ab__stickers_generation_form"}