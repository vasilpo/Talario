<div id="container_{$elm_id}">
{if $smarty.request.condition}
{include file="addons/ab__stickers/views/ab__stickers/components/conditions/condition.tpl"}
{elseif $smarty.request.group}
{include file="addons/ab__stickers/views/ab__stickers/components/group.tpl"}
{/if}
<!--container_{$elm_id}--></div>