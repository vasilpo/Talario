{capture name="mainbox_title"}
{__("abt__unitheme2")}: {__("abt__ut2.help")}
{/capture}
{capture name="mainbox"}
<p>{__('abt__ut2.help.doc')}</p>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="abt__unitheme2"}
{include
file="common/mainbox.tpl"
title_start=__("abt__unitheme2")|truncate:40
title_end=__("abt__ut2.help")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
sidebar=$smarty.capture.sidebar
}
