{capture name="mainbox"}
<p>{__('ab__dotd_help.docs')}</p>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__deal_of_the_day"}
{include
file="common/mainbox.tpl"
title_start=__("ab__deal_of_the_day")|truncate:40
title_end=__("ab__dotd.help")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
sidebar=$smarty.capture.sidebar
}