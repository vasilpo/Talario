{if $navigation.static.central || $navigation.static.top}{strip}
    {$is_collapse = ($main_menu_type === "MainMenuTypeVariants::COLLAPSE"|enum)}
    {$default_menu_item_icon = "file_alt"}
    {$selected_id_path = "`$navigation.selected_tab`/`$navigation.subsection`"}
    {$level = $level|default:1}
    {$prefix = $prefix|default:""}
    {$suffix = $suffix|default:"1"}
    {$item_title_limit = 15} {* 15 - for tooltip. 20 - for narrow text *}
    {$show_collapse_default = $show_collapse_default|default:true}
    {$attrs_wrapper = (isset($item_parent.attrs.wrapper))
        ? ($attrs_wrapper|array_merge:$item_parent.attrs.wrapper)
        : ($attrs_wrapper|default:[])
    }
    {if !isset($item_parent.attrs.wrapper)}
        {$item_parent.attrs.wrapper = []}
    {/if}
    {$main_menu_root_menu_attrs["data-main-menu"] = "rootMenu"}
    {$main_menu_root_menu_attrs["data-main-menu-main-type"] = "menu"}
    {$main_menu_nested_menu_attrs["data-main-menu"] = "subMenu"}
    {$main_menu_nested_menu_attrs["data-main-menu-main-type"] = "menu"}

    {* Dropdown / collapse menu attributes *}
    {if !$is_collapse}
        {$main_menu_nested_menu_attrs["style"] = "display: none;"}
    {/if}

    {function menu_attrs attrs=[]}{foreach $attrs as $attr => $value}{$attr}="{$value}" {/foreach}{/function}

    <div class="accordion main-menu main-menu-{$level} {""}
        {if $level !== 1}
            collapse {""}
        {/if}
        {if $selected_item_parent}
            in {""}
        {/if}" {""}
        {menu_attrs attrs=$attrs_wrapper} {""}
        {if $level === 1}
            {menu_attrs attrs=$main_menu_root_menu_attrs} {""}
        {else}
            {menu_attrs attrs=$main_menu_nested_menu_attrs} {""}
        {/if}
        id="{$prefix}_main_menu_{$suffix}">
        {foreach $items as $item_key => $item}
            {if $item.status === "ObjectStatuses::DISABLED"|enum && !$item.is_show}
                {continue}
            {/if}
            {$selected_item = ($level === 1)
                ? ($item_key === $navigation.selected_tab)
                : ($item_key_parent === $navigation.selected_tab && $item_key === $navigation.subsection)
            }
            {$item_title = $item.title|default:__($item_key)}
            {$item_icon = $item.icon|default:$default_menu_item_icon}
            {$is_show_first_link = false}
            {$item_title_length = $item_title|count_characters:true}
            {if $item.items}
                {$first_item_key = $item.items|@key}
                {$first_item = $item.items.$first_item_key}
                {$is_show_first_link = (
                    $level === 1
                    && $first_item
                    && $first_item.type !== "button"
                    && $first_item.href
                    && $first_item.new_window !== "YesNo::YES"|enum
                    && (
                        !$first_item.attrs
                        || (
                            $first_item.attrs
                            && $first_item.attrs.class_href
                            && !$first_item.attrs.class_href|strstr:"cm-dialog-opener"
                        )
                    )
                )}
            {/if}

            {* Dropdown / collapse menu classes *}
            {$main_menu_item_nested_accordion_heading_class = ""}
            {$main_menu_item_link_link_wrapper_class = ""}
            {if !$is_collapse}
                {* Main menu item inner *}
                {if $item.items || $item.subitems || $item.is_accordion}
                    {$main_menu_item_nested_accordion_heading_class = "main-menu__link-wrapper--ui-menu-item-wrapper"}
                {else}
                    {$main_menu_item_link_link_wrapper_class = "main-menu__link-wrapper--ui-menu-item-wrapper"}
                {/if}
            {/if}

            {* Menu item icon *}
            {capture name="main_menu_link_icon"}
                {$icon_active_class = ($selected_item) ? "main-menu-`$level`__icon--active" : ""}
                <span class="main-menu-{$level}__link-icon">
                    {include_ext file="common/icon.tpl"
                        source=$item_icon
                        class="main-menu-`$level`__icon `$icon_active_class`"
                    }
                </span>
            {/capture}

            {capture name="main_menu_item"}
                <div class="main-menu__item main-menu-{$level}__item {""}
                    {if $item.items || $item.subitems || $item.is_accordion}accordion-group{/if} {""}
                    {$item.attrs.class}" {""}
                    data-main-menu="item"
                    data-main-menu-item-level="{$level}"
                    {menu_attrs attrs=$item.attrs.main}
                >
                    {if $item.type === "title_divider"}
                        {* Title divider *}
                        <div class="main-menu-{$level}__link-wrapper">
                            <div class="main-menu__link main-menu-{$level}__link main-menu-{$level}__link--title-divider {""}
                                {$item.attrs.class_href}" {""}
                                data-main-menu="link" {""}
                                data-main-menu-link-type="titleDivider" {""}
                                {menu_attrs attrs=$item.attrs.href}
                            >
                                {if $level === 1}
                                    {$smarty.capture.main_menu_link_icon nofilter}
                                {/if}
                                <span class="main-menu-{$level}__link-content">
                                    {$item_title}
                                </span>
                            </div>
                        </div>
                    {elseif $item.type === "divider"}
                        {* Divider *}
                        <div><div class="main-menu-{$level}__divider"></div></div>
                    {elseif $item.items || $item.subitems || $item.is_accordion}
                        {* Nested menu accordion *}
                        <div class="accordion-heading main-menu-{$level}__accordion-heading {$main_menu_item_nested_accordion_heading_class}" data-main-menu="nestedAccordionHeading">
                            {* Link for icon (first item) *}
                            {if $level === 1 && $is_show_first_link}
                                <a href="{$first_item.href|fn_url}" {""}
                                    class="main-menu__link main-menu-{$level}__link main-menu-{$level}__link--icon {""}
                                        {$first_item.attrs.class_href} {""}
                                        {if $selected_item}main-menu-{$level}__link--icon--active {/if}" {""}
                                    data-main-menu="link" {""}
                                    data-main-menu-link-type="nestedMenuAccordionIcon" {""}
                                    title="{$item_title}" {""}
                                    {menu_attrs attrs=$first_item.attrs.href}
                                >
                                    {$smarty.capture.main_menu_link_icon nofilter}
                                </a>
                            {/if}

                            {* Accordion toggle *}
                            <a href="#{$prefix}_main_menu_{$suffix}_{$item@iteration}" {""}
                                class="accordion-toggle main-menu__link main-menu-{$level}__link cm-no-ajax {""}
                                    {if $selected_item}main-menu-{$level}__toggle--active {""}{/if}
                                    {if $is_show_first_link}main-menu-{$level}__link--with-icon {""}{/if}
                                    {$item.attrs.class_href}" {""}
                                {if $item.new_window === "YesNo::YES"|enum}target="_blank" {""}{/if}
                                data-parent="#{$prefix}_main_menu_{$suffix}" {""}
                                data-main-menu="linkNestedMenu" {""}
                                data-main-menu="link" {""}
                                data-main-menu-link-type="accordionToggle" {""}
                                {if $level === 1 || $item_title_length > $item_title_limit}title="{$item_title}" {""}{/if}
                                {menu_attrs attrs=$item.attrs.href}
                            >
                                {if $level === 1 && !$is_show_first_link}
                                    {$smarty.capture.main_menu_link_icon nofilter}
                                {/if}
                                <span class="main-menu-{$level}__link-content {""}
                                    {if $selected_item}main-menu-{$level}__link-content--active {""}{/if}
                                    {if $item_title_length > $item_title_limit + 5}
                                        main-menu-{$level}__link-content--long {""}
                                    {/if}
                                ">
                                    {$item_title}
                                </span>
                            </a>
                        </div>
                        {include file="components/menu/main_menu.tpl"
                            items=$item.items|default:$item.subitems
                            level=$level + 1
                            suffix="`$suffix`_`$item@iteration`"
                            item_parent=$item
                            item_key_parent=$item_key
                            selected_item_parent=$selected_item
                        }
                    {else}
                        {* Link menu item *}
                        <div class="main-menu-{$level}__link-wrapper {$main_menu_item_link_link_wrapper_class}" data-main-menu="linkLinkWrapper">
                            {if $item.type === "button"}
                            <button {""}
                            {else}
                            <a href="{if $item.href}{$item.href|fn_url}{else}#{$item_key}{/if}" {""}
                            {/if}
                                {if $item.id_path}
                                    id="{$item.id_path|replace:"/":"_"|replace:".":"_"}" {""}
                                {/if}
                                class="main-menu__link main-menu__link--simple-link main-menu-{$level}__link {""}
                                    {if $selected_item}main-menu-{$level}__link--active {""}{/if}
                                    {$item.attrs.class_href}" {""}
                                {if $item.new_window === "YesNo::YES"|enum}target="_blank" {""}{/if}
                                {if $level === 1 || $item_title_length > $item_title_limit}title="{$item_title}" {""}{/if}
                                data-main-menu="link" {""}
                                data-main-menu-link-type="simpleLink" {""}
                                {menu_attrs attrs=$item.attrs.href}
                            >
                                {if $level === 1}
                                    {$smarty.capture.main_menu_link_icon nofilter}
                                {/if}
                                <span class="main-menu-{$level}__link-content {""}
                                    {if $selected_item}main-menu-{$level}__link-content--active {""}{/if}
                                    {if $item_title_length > $item_title_limit + 5}
                                        main-menu-{$level}__link-content--long {""}
                                    {/if}">
                                    {$item_title}
                                </span>
                            {if $item.type === "button"}
                            </button>
                            {else}
                            </a>
                            {/if}
                        </div>
                    {/if}
                </div>
            {/capture}
            {$extra_params_block = ($level === 1) ? [
                id_path => $item.id_path,
                menu_level => 1
            ] : [
                id_path => $item.id_path
            ]}
            {include file="views/block_manager/frontend_render/block.tpl"
                content=$smarty.capture.main_menu_item
                block=$item
                is_clearfix=false
                location_data=$location_data
                snapping_id=$item.id_path
                object_type="menu_item"
                parent_grid=[
                    location_id => $item.section
                ]
                suffix=$id
                popup_title="{__("admin_menu.edit_item_title")}: `$item_title|truncate:100`"
                show_delete=!$item.is_main
                is_popup=true
                block_menu_compact=true
                return_url=$config.current_url|escape:url
                extra_params=$extra_params_block
                is_editing_allowed=$item.is_editing_allowed
            }
        {/foreach}
        {$extra_params_add = ($level === 1) ? [
            id_path => 0,
            menu_level => 1
        ] : [
            id_path => $item.id_path
        ]}
        {include file="components/menu/add_item.tpl"
            menu_name=$item_parent.title|default:__($item_key_parent)
            id=$item_key_parent
            has_items=($item_parent.items || $item_parent.subitems)
            extra_params=$extra_params_add
            is_subitem=($item_parent.items || $item_parent.subitems)
        }
    </div>
{/strip}{/if}