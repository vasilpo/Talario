{strip}
    {*
        LEGEND
        ut2-fly-menu                    -- ut2-fm
        ut2-light-first-level             -- ut2-lfl
        ut2-second-level-fly-menu-wrap  -- ut2-slw
        ut2-light-second-level            -- ut2-lsl
        ut2-third-level-fly-menu-wrap   -- ut2-tlw
        ut2-fly-menu-back-to-main       -- ut2-fmbtm
        ut2-fly-menu-wrap               -- ut2-fmw
        ut2-menu-toggler                  -- ut2-mt
    *}

    {$props = $block.properties}
    {$unique_part = "abt__ut2_lm_{$block.snapping_id}_{$block.block_id}"}
    <nav class="ut2-fm {if $m_item.content.user_class} {$m_item.content.user_class}{/if}">
        <div class="ut2-fmbtm hidden">{__('abt__ut2.fly_menu.back_to_main')}</div>
        {*{foreach $menu as $menus}*}
        <div class="ut2-fmw{if $state === "YesNo::YES"|enum && $show_title === "YesNo::YES"|enum} toggle-it{/if}">
            {if $menu_name && $show_title === "YesNo::YES"|enum}
                <div class="ut2-mt{if $state === "YesNo::NO"|enum} active{/if}">{$menu_name} <i></i></div>
            {/if}
            {foreach $items as $item}
                <div class="ut2-lfl {$item.class}{if $item.subitems} ut2-lfl_inclusive{/if}">
                    {if $item.abt__ut2_mwi__status === "YesNo::YES"|enum && $item.abt__ut2_mwi__icon}
                        {include file="common/image.tpl" images=$item.abt__ut2_mwi__icon class="ut2-lfl-icon" image_width=32 height=$image_data.height no_ids=true lazy_load=false}
                    {/if}
                    <p class="ut2-lfl__head">
                        <a href="{if trim($item.href)}{$item.href|fn_url}{else}javascript:void(0){/if}">
                            <span class="ut2-lfl__head__t">
                                <span class="ut2-lfl__head__name">{$item.item}</span>
                            {if $item.abt__ut2_mwi__label}
                                <span class="m-label"
                                      style="color:{$item.abt__ut2_mwi__label_color};background-color:{$item.abt__ut2_mwi__label_background};{if $item.abt__ut2_mwi__label_background === '#ffffff'}border: 1px solid {$item.abt__ut2_mwi__label_color}{else}border: 1px solid {$item.abt__ut2_mwi__label_background};{/if}">{$item.abt__ut2_mwi__label}</span>
                            {/if}
                            </span>
                            {if $item.abt__ut2_mwi__desc}<span class="ut2-lfl__head__d">{$item.abt__ut2_mwi__desc}</span>{/if}
                        </a>
                    </p>
                    {if $item.subitems}
                        <i></i>
                        <div class="ut2-slw{if $item.abt__ut2_mwi__text_position !== "bottom"} ut2-slw__right-panel{/if}">

                            {if $item.abt__ut2_mwi__status === "YesNo::YES"|enum && $item.abt__ut2_mwi__text|trim && $item.abt__ut2_mwi__dropdown === "YesNo::NO"|enum}
                            <div class="ut2-slw__html">
                                {/if}

                                {capture name="children"}
                                    {$max_elements_for_second_level = $block.properties.dropdown_second_level_elements|default:30}
                                    {foreach $item.subitems as $subitem}
                                        <div class="ut2-lsl{if $subitem.class} {$subitem.class}{/if}{if $item.abt__ut2_mwi__text && $item.abt__ut2_mwi__text_position !== "bottom"} with-pic{/if}{if $subitem.subitems|count > $props.abt__no_hidden_elements_third_level_view} ut2-lsl__more{/if}{if $subitem@iteration > $max_elements_for_second_level} ut2-lsl__show_more{/if}">
                                            <p{if $subitem.active} class="ut2-fm-active-item"{/if}>
                                                <a href="{if trim($subitem.href)}{$subitem.href|fn_url}{else}javascript:void(0){/if}">
                                                    {if $block.properties.abt_menu_icon_items === "YesNo::YES"|enum && $subitem.abt__ut2_mwi__status === "YesNo::YES"|enum && $subitem.abt__ut2_mwi__icon}
                                                        <span class="img">{include file="common/image.tpl" images=$subitem.abt__ut2_mwi__icon class="ut2-lfl-icon" width=$image_data.width height=$image_data.height no_ids=true lazy_load=false}</span>
                                                    {/if}
                                                    <span>{$subitem.item}
                                                        {if $subitem.abt__ut2_mwi__label}
                                                            <span class="m-label" style="color:{$subitem.abt__ut2_mwi__label_color};background-color:{$subitem.abt__ut2_mwi__label_background};{if $subitem.abt__ut2_mwi__label_background === '#ffffff'}border: 1px solid {$subitem.abt__ut2_mwi__label_color}{else}border: 1px solid {$subitem.abt__ut2_mwi__label_background};{/if}">{$subitem.abt__ut2_mwi__label}</span>
                                                        {/if}
                                                        </span>
                                                </a>
                                            </p>

                                            {if $subitem.subitems}
                                                <div class="ut2-tlw">
                                                    {if $subitem.subitems|count > $props.abt__no_hidden_elements_third_level_view}
                                                        <a href="{if $block.properties.abt__ut2_view_more_btn_behavior|default:'view_items' === 'view_items'}javascript:void(0);" onclick="$(this).parent().find('a').removeClass('hidden');$(this).addClass('hidden');{else}{$subitem.href|fn_url|default:"javascript:void(0)"}" rel="nofollow{/if}" class="ut2-lsl__more-link" title=""><span>{__("more")}</span></a>
                                                    {/if}
                                                    {foreach $subitem.subitems as $sub_subitem}
                                                        <a href="{if trim($sub_subitem.href)}{$sub_subitem.href|fn_url}{else}javascript:void(0){/if}" class="{if $sub_subitem.class}{$sub_subitem.class}{/if}{if $sub_subitem.active} ut2-fm-active-item{/if}{if $sub_subitem@iteration > $block.properties.abt__no_hidden_elements_third_level_view} hidden{/if}">
                                                            {$sub_subitem.item}
                                                            {if $sub_subitem.abt__ut2_mwi__label}
                                                                <span class="m-label" style="color:{$sub_subitem.abt__ut2_mwi__label_color};background-color:{$sub_subitem.abt__ut2_mwi__label_background};{if $sub_subitem.abt__ut2_mwi__label_background === '#ffffff'}border: 1px solid {$sub_subitem.abt__ut2_mwi__label_color}{else}border: 1px solid {$sub_subitem.abt__ut2_mwi__label_background};{/if}">{$sub_subitem.abt__ut2_mwi__label}</span>
                                                            {/if}
                                                        </a>
                                                    {/foreach}
                                                </div>
                                            {/if}
                                        </div>
                                        {if $item.show_more && trim($item.href) && $subitem@iteration == $max_elements_for_second_level}
                                            <div class="ut2-lsl">
                                                <div class="ty-menu__submenu-alt-link"><a class="ty-btn-text" href="{if trim($item.href)}{$item.href|fn_url}{else}javascript:void(0){/if}" title="">{__("text_topmenu_more", ["[item]" => $item.item])}</a></div>
                                            </div>
                                        {/if}
                                    {/foreach}
                                {/capture}
                                {$smarty.capture.children nofilter}
                                {if $item.abt__ut2_mwi__status === "YesNo::YES"|enum && $item.abt__ut2_mwi__text|trim && $item.abt__ut2_mwi__dropdown === "YesNo::NO"|enum}
                            </div>
                            <div class="ut2-slw__html-item{if $item.abt__ut2_mwi__dropdown === "YesNo::YES"|enum} bottom{else} {$item.abt__ut2_mwi__text_position}{/if} hidden-phone">{$item.abt__ut2_mwi__text nofilter}</div>
                            {/if}
                        </div>
                    {/if}
                </div>
            {/foreach}
        </div>
        {*{/foreach}*}
    </nav>
{/strip}