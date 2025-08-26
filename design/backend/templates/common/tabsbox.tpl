{strip}
{script src="js/tygh/tabs.js"}

{if !$active_tab}
    {assign var="active_tab" value=$smarty.request.selected_section}
{/if}

{assign var="empty_tab_ids" value=$content|empty_tabs:$navigation.tabs}
{$show_tabs_navigation = $show_tabs_navigation|default:true}

{* Hide tab items if only one is displayed. Always show Ajax tabs *}
{$tabs_count = 0}
{$has_ajax_tab = false}
{foreach $navigation.tabs as $navigation_tab_key => $navigation_tab}
    {if $navigation_tab.ajax}
        {$has_ajax_tab = true}
    {/if}
    {foreach $empty_tab_ids as $empty_tab_id}
        {if $navigation_tab_key === $empty_tab_id}
            {$tabs_count = $tabs_count + 1}
        {/if}
    {/foreach}
{/foreach}
{$is_show_tab_items = ($navigation.tabs && ($has_ajax_tab || ($navigation.tabs|count - $tabs_count) > 1))}

{* Fill tabs if there are more than 7 *}
{$enable_fill = $enable_fill|default:true}

{if $navigation.tabs}

{$with_conf = false}
{/strip}{capture name="tab_items"}
    {foreach $navigation.tabs as $key => $tab}
        {if (!$tabs_section || $tabs_section == $tab.section) && ($tab.hidden || !$key|in_array:$empty_tab_ids)}
        <li id="{$key}{$id_suffix}" class="{if $tab.hidden == "YesNo::YES"|enum}hidden {/if}{if $tab.js}cm-js{elseif $tab.ajax}cm-js cm-ajax{if $tab.ajax_onclick} cm-ajax-onclick{/if}{/if}{if $key == $active_tab} active{/if} {if $tab.properties}extra-tab{/if}">
            {if $key == $active_tab}{$active_tab_extra nofilter}{/if}

            {if $tab.properties}
                {$with_conf = true}
                {btn type="dialog" class="cm-ajax-force hand icon-cog" title=$tab.properties.title target_id="content_properties_`$key``$id_suffix`" href=$tab.properties.href}
            {/if}

            <a {if $tab.href}href="{$tab.href|fn_url}"{/if}>{$tab.title}</a>
        </li>
        {/if}
    {/foreach}
{/capture}

{capture name="tabs_navigation" assign=tabs_navigation}
<div class="cm-j-tabs{if $track} cm-track{/if} tabs {if !$is_show_tab_items}hidden{/if} {if $with_conf}tabs-with-conf{/if} {if $enable_fill}tabs--enable-fill{/if} tabs--count-{$navigation.tabs|@count} {$meta_tabs}">
    <ul class="nav nav-tabs">
        {$smarty.capture.tab_items nofilter}
    </ul>
</div>
{/capture}
{if $show_tabs_navigation}
    {$smarty.capture.tabs_navigation nofilter}
{/if}
<div class="cm-tabs-content">
    {$content nofilter}
</div>
{else}
    {$content nofilter}
{/if}
{if !$show_tabs_navigation}
    {* Export *}
    {$tabs_navigation = $tabs_navigation scope=parent}
{/if}