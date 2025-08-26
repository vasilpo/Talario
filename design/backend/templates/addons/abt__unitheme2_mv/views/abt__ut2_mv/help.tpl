{capture name="mainbox"}
<p>{__('abt__ut2_mv.help.doc')}</p>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="abt__unitheme2_mv"}
{include file="common/mainbox.tpl"
title_start=__("abt__unitheme2_mv")|truncate:40
title_end=__("abt__ut2_mv.help")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
sidebar=$smarty.capture.sidebar}