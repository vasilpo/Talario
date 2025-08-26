{if !$active_tab}
    {assign var="active_tab" value=$smarty.request.selected_section}
{/if}

{hook name="tabsbox:ab__tabs_list"}
{if $navigation.tabs}

{assign var="empty_tab_ids" value=$content|empty_tabs}
{assign var="_tabs" value=false}

{if $top_order_actions}{$top_order_actions nofilter}{/if}
{script src="js/tygh/tabs.js"}

{$id="simple_products_scroller_{$grid.grid_id}"}
{$elements_to_scroll = 1}

{strip}
<div class="ty-tabs cm-j-tabs{if $track} cm-track{/if} ut2-scroll-container" id="{$id}">
    <button class="ut2-scroll-left" type="button"><span class="ut2-icon-arrow_back_black"></span></button>
    <ul class="ty-tabs__list ut2-scroll-content" {if $tabs_section}id="tabs_{$tabs_section}"{/if}>
    {foreach from=$navigation.tabs item=tab key=key name=tabs}
        {if ((!$tabs_section && !$tab.section) || ($tabs_section == $tab.section)) && !$key|in_array:$empty_tab_ids}
	        {if !$active_tab}
	            {assign var="active_tab" value=$key}
	        {/if}
			{assign var="_tabs" value=true}
            <li id="{$key}" class="ty-tabs__item{if $tab.js} cm-js{elseif $tab.ajax} cm-js cm-ajax{/if}{if $key == $active_tab} active{/if} ut2-scroll-item">
            	<a class="ty-tabs__a" {if $tab.href} href="{$tab.href|fn_url}"{/if}>{$tab.title}</a>
            </li>
        {/if}
    {/foreach}
    </ul>
    <button class="ut2-scroll-right" type="button"><span class="ut2-icon-arrow_forward_black"></span></button>
</div>
{/strip}

{if $_tabs}
<div class="cm-tabs-content ty-tabs__content clearfix" id="tabs_content">
    {$content nofilter}
</div>
{/if}

{include file="common/simple_scroller_init.tpl" block_id=$id elements_to_scroll=$elements_to_scroll}

{if $onclick}
<script>
    var hndl = {$ldelim}
        'tabs_{$tabs_section}': {$onclick}
    {$rdelim}
</script>
{/if}
{else}
    {$content nofilter}
{/if}
{/hook}