{assign var="title_end" value=__("ab__motivation_block.help")}
{capture name="mainbox"}
<p>{__('ab__mb_help.docs')}</p>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__motivation_block"}
{include file="common/mainbox.tpl"
title_start=__("ab__motivation_block")|truncate:40
title_end=$title_end
adv_buttons=$smarty.capture.adv_buttons
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
sidebar=$smarty.capture.sidebar}