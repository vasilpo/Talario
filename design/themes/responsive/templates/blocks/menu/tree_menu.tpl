{** block-description:tree_menu **}

{$item1_url = true}
{$name = "item"}
{$item_id = "param_id"}
{$childs = "subitems"}

{hook name="blocks:tree_menu"}

{if $items}
    <ul class="ty-tree-menu__items cm-responsive-menu">
        {hook name="blocks:tree_menu_top_menu"}

        {foreach from=$items item="item1" name="item1"}
            {assign var="item1_url" value=$item1|fn_form_dropdown_object_link:$block.type}
            {assign var="unique_elm_id" value=$item1_url|md5}
            {assign var="unique_elm_id" value="topmenu_`$block.block_id`_`$unique_elm_id`"}

            {if $subitems_count}

            {/if}
            <li class="ty-tree-menu__item cm-menu-item-responsive {if !$item1.$childs} ty-tree-menu__item-nodrop{/if}{if $item1.active || $item1|fn_check_is_active_menu_item:$block.type} ty-tree-menu__item-active{/if}{if $item1.class} {$item1.class}{/if}">
                    {if $item1.$childs}
                        <a class="ty-tree-menu__item-toggle visible-phone cm-responsive-menu-toggle" data-ca-tree-menu="itemToggle">
                            {include_ext file="common/icon.tpl"
                                class="ty-icon-down-open ty-tree-menu__icon-open"
                            }
                            {include_ext file="common/icon.tpl"
                                class="ty-icon-up-open ty-tree-menu__icon-hide"
                            }
                        </a>
                    {/if}
                    <a {if $item1_url} href="{$item1_url}"{/if} class="ty-tree-menu__item-link" {if $item1.new_window}target="_blank"{/if}>
                        {$item1.$name}
                    </a>
                {if $item1.$childs}

                    {if !$item1.$childs|fn_check_second_level_child_array:$childs}
                    {* Only two levels. Vertical output *}
                        <div class="ty-tree-menu__submenu">
                            <ul class="ty-tree-menu__submenu-items ty-tree-menu__submenu-items-simple cm-responsive-menu-submenu">
                                {hook name="blocks:tree_menu_2levels_elements"}

                                {foreach from=$item1.$childs item="item2" name="item2"}
                                    {assign var="item_url2" value=$item2|fn_form_dropdown_object_link:$block.type}
                                    <li class="ty-tree-menu__submenu-item{if $item2.active || $item2|fn_check_is_active_menu_item:$block.type} ty-tree-menu__submenu-item-active{/if}{if $item2.class} {$item2.class}{/if}">
                                        <a class="ty-tree-menu__submenu-link" {if $item_url2} href="{$item_url2}"{/if} {if $item2.new_window}target="_blank"{/if}>{$item2.$name}</a>
                                    </li>
                                {/foreach}
                                {if $item1.show_more && $item1_url}
                                    <li class="ty-tree-menu__submenu-item ty-tree-menu__submenu-alt-link">
                                        <a href="{$item1_url}"
                                           class="ty-tree-menu__submenu-alt-link">{__("text_topmenu_view_more") nofilter}</a>
                                    </li>
                                {/if}

                                {/hook}
                            </ul>
                        </div>
                    {else}
                        <div class="ty-tree-menu__submenu" id="{$unique_elm_id}">
                            {hook name="blocks:tree_menu_3levels_cols"}
                                <ul class="ty-tree-menu__submenu-items cm-responsive-menu-submenu">
                                    {foreach from=$item1.$childs item="item2" name="item2"}
                                        <li class="ty-top-mine__submenu-col">
                                            {assign var="item2_url" value=$item2|fn_form_dropdown_object_link:$block.type}
                                            <div class="ty-tree-menu__submenu-item-header{if $item2.active || $item2|fn_check_is_active_menu_item:$block.type} ty-tree-menu__submenu-item-header-active{/if}{if $item2.class} {$item2.class}{/if}">
                                                <a{if $item2_url} href="{$item2_url}"{/if} class="ty-tree-menu__submenu-link" {if $item2.new_window}target="_blank"{/if}>{$item2.$name}</a>
                                            </div>
                                            {if $item2.$childs}
                                                <a class="ty-tree-menu__item-toggle visible-phone cm-responsive-menu-toggle" data-ca-tree-menu="itemToggle">
                                                    {include_ext file="common/icon.tpl"
                                                        class="ty-icon-down-open ty-tree-menu__icon-open"
                                                    }
                                                    {include_ext file="common/icon.tpl"
                                                        class="ty-icon-up-open ty-tree-menu__icon-hide"
                                                    }
                                                </a>
                                            {/if}
                                            <div class="ty-tree-menu__submenu">
                                                <ul class="ty-tree-menu__submenu-list cm-responsive-menu-submenu">
                                                    {if $item2.$childs}
                                                        {hook name="blocks:tree_menu_3levels_col_elements"}
                                                        {foreach from=$item2.$childs item="item3" name="item3"}
                                                            {assign var="item3_url" value=$item3|fn_form_dropdown_object_link:$block.type}
                                                            <li class="ty-tree-menu__submenu-item{if $item3.active || $item3|fn_check_is_active_menu_item:$block.type} ty-tree-menu__submenu-item-active{/if}{if $item3.class} {$item3.class}{/if}">
                                                                <a{if $item3_url} href="{$item3_url}"{/if}
                                                                        class="ty-tree-menu__submenu-link" {if $item3.new_window}target="_blank"{/if}>{$item3.$name}</a>
                                                            </li>
                                                        {/foreach}
                                                        {if $item2.show_more && $item2_url}
                                                            <li class="ty-tree-menu__submenu-item ty-tree-menu__submenu-alt-link">
                                                                <a href="{$item2_url}"
                                                                   class="ty-tree-menu__submenu-link" {if $item2.new_window}target="_blank"{/if}>{__("text_topmenu_view_more") nofilter}</a>
                                                            </li>
                                                        {/if}
                                                        {/hook}
                                                    {/if}
                                                </ul>
                                            </div>
                                        </li>
                                    {/foreach}
                                    {if $item1.show_more && $item1_url}
                                        <li class="ty-tree-menu__submenu-dropdown-bottom">
                                            <a href="{$item1_url}" {if $item1.new_window}target="_blank"{/if}>{__("text_topmenu_more", ["[item]" => $item1.$name]) nofilter}</a>
                                        </li>
                                    {/if}
                                </ul>
                            {/hook}
                        </div>
                    {/if}

                {/if}
            </li>
        {/foreach}

        {/hook}
    </ul>
{/if}
{/hook}
