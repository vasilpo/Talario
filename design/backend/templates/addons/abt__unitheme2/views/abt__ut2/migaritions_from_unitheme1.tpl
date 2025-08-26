{include file="addons/abt__unitheme2/views/abt__ut2/components/uni1_to_uni2/{$abt__ut2_action}.tpl"}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="abt__unitheme2"}
{include
file="common/mainbox.tpl"
title_start = __("abt__unitheme2")|truncate:40
title_end = __("abt__ut2.migaritions_from_unitheme1.{$abt__ut2_action}")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
content_id="abt__ut2_po_update_form"
}