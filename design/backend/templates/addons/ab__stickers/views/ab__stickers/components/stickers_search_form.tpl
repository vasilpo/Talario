{if $in_popup}
<div class="adv-search">
<div class="group">
{else}
<div class="sidebar-row">
<h6>{__("search")}</h6>
{/if}
<form action="{""|fn_url}" name="ab__stickers_search_form" method="get" class="{$form_meta}" id="ab__stickers_search_form">
{if $put_request_vars}
{array_to_fields data=$smarty.request skip=["callback"]}
{/if}
{capture name="simple_search"}
{hook name="ab__stickers:simple_search"}
{include file="addons/ab__stickers/views/ab__stickers/components/stickers_simple_search.tpl"}
{/hook}
{/capture}
{capture name="advanced_search"}
{hook name="ab__stickers:advanced_search"}
{include file="addons/ab__stickers/views/ab__stickers/components/stickers_advanced_search.tpl"}
{/hook}
{/capture}
{include file="common/advanced_search.tpl" simple_search=$smarty.capture.simple_search advanced_search=$smarty.capture.advanced_search dispatch=$dispatch view_type="ab__stickers" in_popup=$in_popup}
<!--ab__stickers_search_form--></form>
{if $in_popup}
</div></div>
{else}
</div><hr>
{/if}