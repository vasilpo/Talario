{capture name="mainbox_title"}
{__("ab__landing_categories")}: {__("ab__lc.help")}
{/capture}
{capture name="mainbox"}
<p>{__('ab__lc.help.doc')}</p>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__landing_categories"}
{include file="common/mainbox.tpl" title_start=__("ab__landing_categories")|truncate:40 title_end = __("ab__lc.help") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons sidebar=$smarty.capture.sidebar}
