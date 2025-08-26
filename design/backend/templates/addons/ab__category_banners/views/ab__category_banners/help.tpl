{capture name="mainbox"}
{* main help *}
<p>{__('ab__cb.help.doc')}</p>
{* cron links *}
{assign var="links" value=''|fn_ab__cb_cron_links}
{include file="common/subheader.tpl" meta="" title="{__('ab__cb.help.cron.title')}" target="#ab__cb.help.cron"}
<div id="ab__cb.help.cron" class="in collapse" style="padding: 0 20px">{$links nofilter}</div>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__category_banners"}
{include file="common/mainbox.tpl"
title_start = __("ab__category_banners")|truncate:40
title_end = __("ab__category_banners.help")
content = $smarty.capture.mainbox
buttons = $smarty.capture.buttons
adv_buttons = $smarty.capture.adv_buttons
sidebar = $smarty.capture.sidebar
}