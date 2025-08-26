{assign var="title_start" value=__("ab__search_motivation.help")}
{assign var="title_end" value=__("ab__search_motivation")}
{capture name="mainbox_title"}
{$title_start} {$title_end}
{/capture}
{capture name="mainbox"}
<p>{__('ab__search_motivation.help.doc')}</p>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__search_motivation"}
{include file="common/mainbox.tpl"
title=$smarty.capture.mainbox_title
title_start=$title_start
title_end=$title_end
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
sidebar=$smarty.capture.sidebar}