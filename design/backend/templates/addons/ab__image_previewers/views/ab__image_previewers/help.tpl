{capture name="mainbox"}
<p>{__('ab__image_previewers.help.docs')}</p>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__image_previewers"}
{include file="common/mainbox.tpl"
title_start=__("ab__image_previewers")|truncate:40
title_end=__("ab__image_previewers.help")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
sidebar=$smarty.capture.sidebar}