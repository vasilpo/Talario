{capture name="mainbox"}
<p>{__('ab__scroll_to_top.help.doc')}</p>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__scroll_to_top"}
{include file="common/mainbox.tpl"
title_start=__("ab__scroll_to_top")|truncate:40
title_end=__("ab__scroll_to_top.help")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
sidebar=$smarty.capture.sidebar}