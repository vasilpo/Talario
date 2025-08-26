{if $items}{strip}
    {$actions_count_threshold_xl = $config.actions_menu.count_threshold_xl|default:3}
    {$actions_count_threshold_xxl = $config.actions_menu.count_threshold_xxl|default:5}
    {$actions_count_threshold_xxxl = $config.actions_menu.count_threshold_xxxl|default:7}
    {$icon_prefix = "icon-"}
    {$icon_prefix_length = $icon_prefix|strlen}
    {$button_characters_threshold = 30}
    {$button_characters_mobile_threshold = 20}
    {$actions_menu_btn_group_class = "actions-menu__btn-group"}
    {if $items|@count > $actions_count_threshold_xxxl}
        {* Skip *}
    {elseif $items|@count > $actions_count_threshold_xxl}
        {$actions_menu_btn_group_class = "actions-menu__btn-group--xxxl"}
    {elseif $items|@count > $actions_count_threshold_xl}
        {$actions_menu_btn_group_class = "actions-menu__btn-group--xxl"}
    {elseif $items|@count > 0}
        {$actions_menu_btn_group_class = "actions-menu__btn-group--xl"}
    {/if}

    {foreach $items as $item_key => $item}
        {$item_text = $item.text|default:__($item_key)}
        {$item_title = $item.title|default:""}
        {$item_class = "btn actions-menu__btn `$item.class` `$item.meta`"}
        {if $item@iteration > 0}
            {$item_class = "`$item_class` actions-menu__btn--visible-xl"}
        {/if}
        {if $item@iteration > $actions_count_threshold_xl}
            {$item_class = "`$item_class` actions-menu__btn--visible-xxl"}
        {/if}
        {if $item@iteration > $actions_count_threshold_xxl}
            {$item_class = "`$item_class` actions-menu__btn--visible-xxxl"}
        {/if}
        {if $item@iteration > $actions_count_threshold_xxxl}
            {continue}
        {/if}

        {capture name="item_text" assign="item_text_html"}
            {if $item_text|count_characters:true > $button_characters_mobile_threshold}
                <span class="mobile-hidden">{$item_text}</span>
                <span class="mobile-visible">{($item.text_mobile|truncate
                    :$button_characters_mobile_threshold:'...':true:true)|default
                    :($item_text|truncate:$button_characters_mobile_threshold:'...':true:true)
                }</span>
            {else}
                <span>{$item_text}</span>
            {/if}
        {/capture}
        {if $item_text|count_characters:true > $button_characters_threshold}
            {$item_title = $item_text}
            {$item_text = "`$item_text|substr:0:$button_characters_threshold`..."}
        {/if}
        {$item_icon = ""}
        {if $item.icon}
            {$item_icon = $item.icon|trim}
            {* Source without "icon-" prefix *}
            {if $item_icon|substr:0:$icon_prefix_length !== $icon_prefix}
                {$item_icon = "`$icon_prefix``$item_icon`"}
            {/if}
        {/if}

        {if $item.wrapper_class}<span class="shift-left shift-right {$item.wrapper_class}">{/if}
            {btn type=$item.type|default:"text"
                href=$item.href|default:""
                text=$item_text_html
                title=$item_title
                id=(($item.id) ? "`$item.id`_primary" : "")
                class=$item_class
                dispatch=$item.dispatch|default:""
                form=$item.form|default:""
                method=$item.method|default:""
                target=$item.target|default:""
                target_id=$item.target_id|default:""
                process=$item.process|default:""
                onclick=$item.onclick|default:""
                raw=true
                icon=$item_icon
                icon_first=true
                data=$item.data|default:[]
            }
        {if $item.wrapper_class}</span>{/if}
    {/foreach}

    {capture name="tools_list"}
        {foreach $items as $item_key => $item name="actions"}
            {$item_wrapper_class = $item.wrapper_class}
            {if $item@iteration > $actions_count_threshold_xxxl}
                {* Skip *}
            {elseif $item@iteration > $actions_count_threshold_xxl}
                {$item_wrapper_class = "`$item_wrapper_class` actions-menu__dropdown-item--hidden-xxxl"}
            {elseif $item@iteration > $actions_count_threshold_xl}
                {$item_wrapper_class = "`$item_wrapper_class` actions-menu__dropdown-item--hidden-xxl"}
            {elseif $item@iteration > 0}
                {$item_wrapper_class = "`$item_wrapper_class` actions-menu__dropdown-item--hidden-xl"}
            {/if}

            {$item_text = $item.text|default:__($item_key)}
            {capture name="item_text" assign="item_text_html"}
                <span>{$item_text}</span>
            {/capture}
            {$item_icon = ""}
            {if $item.icon}
                {$item_icon = $item.icon|trim}
                {* Source without "icon-" prefix *}
                {if $item_icon|substr:0:$icon_prefix_length !== $icon_prefix}
                    {$item_icon = "`$icon_prefix``$item_icon`"}
                {/if}
            {/if}

            <li class="actions-menu__dropdown-item-wrapper {$item_wrapper_class}">
                {btn type=$item.type|default:"text"
                    href=$item.href|default:""
                    text=$item_text_html
                    title=$item.title|default:""
                    id=$item.id|default:""
                    class="actions-menu__dropdown-item `$item.class` `$item.meta`"
                    dispatch=$item.dispatch|default:""
                    form=$item.form|default:""
                    method=$item.method|default:""
                    target=$item.target|default:""
                    target_id=$item.target_id|default:""
                    process=$item.process|default:""
                    onclick=$item.onclick|default:""
                    raw=true
                    icon=$item_icon
                    icon_first=true
                    data=$item.data|default:[]
                }
            </li>
        {/foreach}
    {/capture}
    {capture name="link_text_tools_list"}{strip}
        <span class="actions-menu__link-text">{__("actions.more")}</span>
    {/strip}{/capture}
    {include file="common/tools.tpl"
        hide_actions=true
        tools_list=$smarty.capture.tools_list
        link_text=$smarty.capture.link_text_tools_list
        icon="icon-ellipsis-horizontal"
        caret=true
        prefix="actions_menu"
        tool_meta=$actions_menu_btn_group_class
        override_meta="btn actions-menu__dropdown-toggle"
    }
{/strip}{/if}