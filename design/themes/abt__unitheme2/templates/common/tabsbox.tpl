{if !$active_tab}
    {assign var="active_tab" value=$smarty.request.selected_section}
{/if}

{hook name="tabsbox:ab__tabs_list"}
{if $navigation.tabs || $navigation[$grid.grid_id]}
    {assign var="empty_tab_ids" value=$content|empty_tabs}
    {assign var="_tabs" value=false}
    {$abt__show_in_tabs = in_array($grid.abt__ut2_show_blocks_in_tabs, ["Addons\\Abt_unitheme2\\BlockInTabsTypes::TABS_WITHOUT_LAZY_LOAD"|enum, "Addons\\Abt_unitheme2\\BlockInTabsTypes::TABS_WITH_LAZY_LOAD"|enum])}
    {if $top_order_actions}{$top_order_actions nofilter}{/if}

    {$id="simple_products_scroller_{$grid.grid_id}"}
    {$elements_to_scroll = 1}

    {script src="js/tygh/tabs.js"}

    {strip}
        <div class="ty-tabs cm-j-tabs{if $track} cm-track{/if} {if $abt__show_in_tabs}cm-j-tabs-disable-convertation{/if} ut2-scroll-container" id="{$id}">
            <button class="ut2-scroll-left" type="button"><span class="ut2-icon-arrow_back_black"></span></button>
            {if $abt__show_in_tabs}
                <ul class="ty-tabs__list ut2-scroll-content">
                    {foreach from=$navigation[$grid.grid_id] item=tab key=key name=tabs}
                        {if ((!$tabs_section && !$tab.section) || ($tabs_section == $tab.section)) && !$key|in_array:$empty_tab_ids && $key|strpos:'abt__ut2_grid_tab' !== false}
                            {if !$active_tab}
                                {assign var="active_tab" value=$key}
                            {/if}
                            {assign var="_tabs" value=true}
                            <li id="{$key}" data-block="{$tab.block}" class="{if $abt__show_in_tabs}abt__ut2_grid_tabs {/if}ty-tabs__item{if $tab.js} cm-js{elseif $tab.ajax} cm-js{if $key != $active_tab} cm-ajax{/if}{/if}{if $key == $active_tab} active{/if} ut2-scroll-item">
                                <span class="ty-tabs__span">{$tab.title}{if $tab@key == 'discussion' && $product.discussion.search.total_items > 0} <bdi><i class="ut2-ti-{$tab@key}"></i>{$product.discussion.search.total_items}</bdi>{/if}</span>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            {else}
                <ul class="ty-tabs__list ut2-scroll-content" {if $tabs_section}id="tabs_{$tabs_section}"{/if}>
                    {foreach from=$navigation.tabs item=tab key=key name=tabs}
                        {if ((!$tabs_section && !$tab.section) || ($tabs_section == $tab.section)) && !$key|in_array:$empty_tab_ids}
                            {if !$active_tab}
                                {assign var="active_tab" value=$key}
                            {/if}
                            {assign var="_tabs" value=true}
                            <li id="{$key}" class="ty-tabs__item{if $tab.js} cm-js{elseif $tab.ajax} cm-js cm-ajax{/if}{if $key == $active_tab} active{/if} ut2-scroll-item">
                                <a class="ty-tabs__a" {if $tab.href} href="{$tab.href|fn_url}"{/if}>
                                    {$tab.title}
                                    {if $settings.ab__device !== "mobile"}
                                        {if $tab@key == "discussion" && $product.discussion.search.total_items > 0}&nbsp;<span class="count">{$product.discussion.search.total_items}</span>{/if}
                                        {if $tab@key == "product_reviews" && $product.product_reviews_count}&nbsp;<span class="count">{$product.product_reviews_count}</span>{/if}
                                    {/if}
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            {/if}
            <button class="ut2-scroll-right" type="button"><span class="ut2-icon-arrow_forward_black"></span></button>
        </div>
    {/strip}

    {if $_tabs}
        <div class="cm-tabs-content ty-tabs__content clearfix"{if !$abt__show_in_tabs} id="tabs_content"{/if}>
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