{if $item1.abt__ut2_mwi__dropdown === "YesNo::YES"|enum}
    {$cols_tree_level = 1}
{else}
    {$cols_tree_level = $settings_cols}
{/if}

{$second_level_counter = 0}

{hook name="blocks:topmenu_dropdown_3levels_cols"}
    <div class="ut2-menu__submenu__carrier {if $item1.abt__ut2_mwi__dropdown === "YesNo::YES"|enum}cascading {elseif $item1.abt__ut2_mwi__text}{if $item1.abt__ut2_mwi__text_position !== "bottom"}submenu-1st-has-side-banner {else}submenu-1st-has-bottom-banner {/if}{/if}row-filling {$dropdown_class}" data-cols-count="{$cols_tree_level}">

        {$Viewlimit=$block.properties.abt__no_hidden_elements_third_level_view|default:5}

        <div class="ut2-menu__submenu__wrapper">
            <div class="ut2-menu__2nd-list">
                <div class="ut2-menu__2nd-col">
                    {foreach from=$item1.$childs item="item2" name="item2"}
                        <div class="ut2-menu__2nd-item" data-elem-index="{$second_level_counter}">
                            {$second_level_counter = $second_level_counter + 1}
                            {assign var="item2_url" value=$item2|fn_form_dropdown_object_link:$block.type}

                            <div class="ut2-menu__2nd-item__header{if $item2.abt__ut2_mwi__icon && $item1.abt__ut2_mwi__dropdown === "YesNo::NO"|enum} ut2-mwi-icon-wrap{/if}{if empty($item2.$childs)} no-items{/if}">
                                <a {if $settings.ab__device !== "mobile" || !$item2.childs}href="{$item2_url|default:"javascript:void(0)"}"
                                   {else}href="javascript:void(0)"{/if}{if $item2_url && $item2.new_window == "YesNo::YES"|enum} target="_blank"{/if}
                                   class="ut2-menu__2nd-link {if $item2.class}{$item2.class}{/if}">
                                    {if $item2.abt__ut2_mwi__icon && $block.properties.abt_menu_icon_items === "YesNo::YES"|enum && $settings.ab__device !== "mobile"}
                                        {include file="common/image.tpl" images=$item2.abt__ut2_mwi__icon class="ut2-mwi-icon" no_ids=true}
                                    {/if}
                                    {strip}
                                        <span class="ut2-menu__2nd-link__text">
                                            <span class="ut2-menu__2nd-link__name">{$item2.$name nofilter}</span>
                                            {if $item2.abt__ut2_mwi__status === "YesNo::YES"|enum && $item2.abt__ut2_mwi__label}
                                                <span class="m-label"
                                                      style="color: {$item2.abt__ut2_mwi__label_color};background-color: {$item2.abt__ut2_mwi__label_background}; {if $item2.abt__ut2_mwi__label_background == '#ffffff'}border: 1px solid {$item2.abt__ut2_mwi__label_color}{else}border: 1px solid {$item2.abt__ut2_mwi__label_background};{/if}">{$item2.abt__ut2_mwi__label}</span>
                                            {/if}
                                        </span>
                                    {/strip}
                                </a>
                                {if $item2.$childs && $item1.abt__ut2_mwi__dropdown === "YesNo::YES"|enum}
                                    <i class="ut2-icon-outline-arrow_forward"></i>
                                {/if}
                            </div>

                            {if $item2.$childs}
                                <span class="ty-menu__item-toggle cm-responsive-menu-toggle">
                                    <i class="ut2-mark-fold-unfold"></i>
                                </span>
                            {/if}

                            {if !empty($item2.$childs)}
                                <div class="ut2-menu__2nd-submenu{if $item2.abt__ut2_mwi__text}{if $item2.abt__ut2_mwi__text_position !== "bottom"} submenu-2nd-has-side-banner{else} submenu-2nd-has-bottom-banner{/if}{/if}">
                                    <div class="ut2-menu__2nd-submenu__wrapper">
                                        {if $item1.abt__ut2_mwi__dropdown === "YesNo::YES"|enum}
                                            {$max_amount_3rd=$block.properties.dropdown_third_level_elements|default:30}
                                            {$item2.$childs=array_slice($item2.$childs, 0, $max_amount_3rd, true)}

                                            {foreach from=array_chunk($item2.$childs, ceil($item2.$childs|count / $cols_tree_level), true) item="item2_childs"}
                                                {if $item2_childs}
                                                    <div class="ut2-menu__3rd-list {if $item1.abt__ut2_mwi__dropdown === "YesNo::YES"|enum}{if $item2_childs|count > 19}multicol {/if}{elseif $item2_childs|count > $Viewlimit}clipped {/if}" data-cols-count="{$settings_cols}" {if $item2_childs|count > $Viewlimit && $item1.abt__ut2_mwi__dropdown === "YesNo::NO"|enum}style="--menu-items:{$Viewlimit}"{/if}>
                                                        {hook name="blocks:topmenu_dropdown_3levels_col_elements"}
                                                        {if $item2.href && $settings.ab__device !== "desktop"}
                                                            <div class="ut2-menu__3rd-item item-3rd-has-more-cat-link">
                                                                <a href="{$item2.href|fn_url}"
                                                                   {if $item2_url && $item2.new_window == "YesNo::YES"|enum}target="_blank"{/if}
                                                                   class="ut2-menu__3rd-link {if $item2.class}{$item2.class}{/if}"
                                                                   title="">
                                                                    {if $block.properties.abt_menu_icon_items === "YesNo::YES"|enum && $item2.abt__ut2_mwi__icon && $settings.ab__device === "desktop"}
                                                                        {include file="common/image.tpl" images=$item2.abt__ut2_mwi__icon class="ut2-mwi-icon" no_ids=true}
                                                                    {/if}
                                                                    {if $item2_url}
                                                                        <span class="ut2-menu__3rd-link__text">
                                                                            <span class="ut2-menu__3rd-link__name">{__('all')} {$item2.item nofilter}</span>
                                                                        </span>
                                                                    {/if}
                                                                </a>
                                                            </div>
                                                        {/if}
                                                        {foreach from=$item2_childs item="item3" name="item3"}
                                                            {assign var="item3_url" value=$item3|fn_form_dropdown_object_link:$block.type}
                                                            <div class="ut2-menu__3rd-item">
                                                                <a href="{$item3_url|default:"javascript:void(0)"}"
                                                                   {if $item3_url && $item3.new_window == "YesNo::YES"|enum}target="_blank"{/if}
                                                                   class="ut2-menu__3rd-link {if $item3.class}{$item3.class}{/if}"
                                                                   title="">
                                                                    {strip}
                                                                        <span class="ut2-menu__3rd-link__text">
                                                                            <span class="ut2-menu__3rd-link__name">{$item3.$name nofilter}</span>
                                                                            {if $item3.abt__ut2_mwi__status === "YesNo::YES"|enum && $item3.abt__ut2_mwi__label}
                                                                                <span class="m-label"
                                                                                      style="color: {$item3.abt__ut2_mwi__label_color}; background-color: {$item3.abt__ut2_mwi__label_background};{if $item3.abt__ut2_mwi__label_background === '#ffffff'}border: 1px solid {$item3.abt__ut2_mwi__label_color}{else}border: 1px solid {$item3.abt__ut2_mwi__label_background};{/if}">{$item3.abt__ut2_mwi__label}</span>
                                                                            {/if}
                                                                        </span>
                                                                    {/strip}
                                                                </a>
                                                            </div>
                                                        {/foreach}
                                                        {/hook}
                                                    </div>
                                                {/if}
                                            {/foreach}
                                        {else}
                                            {if $item2.$childs}
                                                <div class="ut2-menu__3rd-list {if $item2.abt__ut2_mwi__dropdown === "YesNo::YES"|enum}{if $item2_childs|count > 19}multicol {/if}{elseif $item2.$childs|count > $Viewlimit}clipped {/if}" data-cols-count="{$settings_cols}" {if $item2.$childs|count > $Viewlimit}style="--menu-items:{$Viewlimit};"{/if}>
                                                    {hook name="blocks:topmenu_dropdown_3levels_col_elements"}
                                                    {if $item2.href && $settings.ab__device !== "desktop"}
                                                        <div class="ut2-menu__3rd-item item-3rd-has-more-cat-link">
                                                            <a href="{$item2.href|fn_url}"
                                                               {if $item2_url && $item2.new_window == "YesNo::YES"|enum}target="_blank"{/if}
                                                               class="ut2-menu__3rd-link {if $item2.class}{$item2.class}{/if}"
                                                               title="">
                                                                {if $block.properties.abt_menu_icon_items === "YesNo::YES"|enum && $item2.abt__ut2_mwi__icon && $settings.ab__device === "desktop"}
                                                                    {include file="common/image.tpl" images=$item2.abt__ut2_mwi__icon class="ut2-mwi-icon" no_ids=true}
                                                                {/if}
                                                                {if $item2_url}
                                                                    <span class="ut2-menu__3rd-link__text">
                                                                        <span class="ut2-menu__3rd-link__name">{__('all')} {$item2.item nofilter}</span>
                                                                    </span>
                                                                {/if}
                                                            </a>
                                                        </div>
                                                    {/if}
                                                    {foreach from=$item2.$childs item="item3" name="item3"}
                                                        {assign var="item3_url" value=$item3|fn_form_dropdown_object_link:$block.type}
                                                        <div class="ut2-menu__3rd-item{if $item3.class} {$item3.class}{/if}">
                                                            <a href="{$item3_url|default:"javascript:void(0)"}"
                                                               {if $item3_url && $item3.new_window == "YesNo::YES"|enum}target="_blank"{/if}
                                                               class="ut2-menu__3rd-link" title="">
                                                                {strip}
                                                                    <span class="ut2-menu__3rd-link__text">
                                                                        <span class="ut2-menu__3rd-link__name">{$item3.$name nofilter}</span>
                                                                        {if $item3.abt__ut2_mwi__status === "YesNo::YES"|enum && $item3.abt__ut2_mwi__label}
                                                                            <span class="m-label"
                                                                                  style="color: {$item3.abt__ut2_mwi__label_color};background-color: {$item3.abt__ut2_mwi__label_background};{if $item3.abt__ut2_mwi__label_background === "#ffffff"}border: 1px solid {$item3.abt__ut2_mwi__label_color}{else}border: 1px solid {$item3.abt__ut2_mwi__label_background};{/if}">{$item3.abt__ut2_mwi__label}</span>
                                                                        {/if}
                                                                    </span>
                                                                {/strip}
                                                            </a>
                                                        </div>
                                                    {/foreach}
                                                    {/hook}
                                                </div>
                                            {/if}
                                            {if $item2.$childs|count > $Viewlimit}
                                                <a {if $block.properties.abt__ut2_view_more_btn_behavior|default:"view_items" === "view_items"}href="javascript:void(0);" onclick="$(this).prev().addClass('view');$(this).addClass('hidden');" {else}href="{$item2_url|default:"javascript:void(0)"}" rel="nofollow"{/if} class="ut2-more-btn" title=""><span>{__("more")}</span></a>
                                            {/if}
                                        {/if}

                                        {if $item1.abt__ut2_mwi__dropdown === "YesNo::YES"|enum && $item2_url && $settings.ab__device !== "mobile"}
                                            <div class="ut2-menu__more-cat-link{if $item2.show_more} show-not-mobile{/if}">
                                                <a class="ty-btn-text" href="{$item2_url}" title="">
                                                    <span class="ut2-menu__more-cat-link__in">
                                                        {__("text_topmenu_more", ["[item]" => $item2.$name])}
                                                    </span>
                                                </a>
                                            </div>
                                        {/if}
                                    </div>

                                    {if $item1.abt__ut2_mwi__dropdown === "YesNo::YES"|enum && $item2.abt__ut2_mwi__text && $settings.ab__device !== "mobile"}
                                        <div class="ut2-mwi-html {$item2.abt__ut2_mwi__text_position} hidden-phone">
                                            <div class="ut2-mwi-html__in">
                                                {$item2.abt__ut2_mwi__text nofilter}
                                            </div>
                                        </div>
                                    {/if}
                                </div>
                            {/if}
                        </div>
                    {/foreach}
                </div>
            </div>

            {if $item1_url && $settings.ab__device !== "mobile"}
                <div class="ut2-menu__more-cat-link{if $item1.show_more} show-not-mobile{/if}">
                    <a class="ty-btn-text" href="{$item1_url}" title="">
                        <span class="ut2-menu__more-cat-link__in">
                            {__("text_topmenu_more", ["[item]" => $item1.$name])}
                        </span>
                    </a>
                </div>
            {/if}
        </div>

        {if $item1.abt__ut2_mwi__dropdown === "YesNo::NO"|enum && $item1.abt__ut2_mwi__text && $settings.ab__device !== "mobile"}
            <div class="ut2-mwi-html {$item1.abt__ut2_mwi__text_position} hidden-phone">
                <div class="ut2-mwi-html__in">
                    {$item1.abt__ut2_mwi__text nofilter}
                </div>
            </div>
        {/if}
    </div>
{/hook}