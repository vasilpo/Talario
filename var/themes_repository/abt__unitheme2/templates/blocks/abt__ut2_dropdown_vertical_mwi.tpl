{hook name="blocks:topmenu_dropdown"}

{strip}
{if $items}

    {$block.properties.abt_menu_ajax_load = "YesNo::NO"|enum}

    {if $settings.ab__device === "desktop"}
    <div class="ut2-menu__backdrop cm-external-click" style="display: none" data-ca-external-click-id="sw_dropdown_{$block.snapping_id}"></div>
    {/if}

    <div class="ut2-menu__header-mobile" style="display: none">{$block.name}</div>

    {$settings_cols = min(6, $block.properties.abt__ut2_columns_count|default:4)}

    <div class="ut2-v__menu ut2-menu" style="--ut2-vertical-menu-block-height: {$block.properties.abt__ut2_menu_min_height|default:430}px;">
        <div class="ut2-menu__inbox{if $block.properties.open_on_sticky_panel_button === "Y"} cm-external-triggered{/if}{if $block.properties.abt__menu_compact_view === "YesNo::YES"|enum} compact{/if}">
            <ul class="ut2-menu__list" style="--menu-columns: {$settings_cols}">
                {hook name="blocks:topmenu_dropdown_top_menu"}

                {foreach from=$items item="item1" name="item1"}
                    {assign var="item1_url" value=$item1|fn_form_dropdown_object_link:$block.type}
                    {if $settings.ab__device === "desktop"}
                        {assign var="url_mobile" value=$item1_url}
                    {/if}
                    {assign var="unique_elm_id" value="topmenu_{$block.block_id}_{$block.snapping_id}_{substr(crc32(serialize($item1)), 0, 10)}"}
                    {assign var="subitems_count" value=$item1.$childs|count}
                    {assign var="show_second_level" value=($block.properties.dropdown_second_level_elements>0 || !isset($block.properties.dropdown_second_level_elements)) && ($item1.$childs || ($item1.abt__ut2_mwi__status === "YesNo::YES"|enum && $item1.abt__ut2_mwi__text))}

                    <li class="ut2-menu__item{if !$item1.$childs} item-1st-no-drop{/if}{if $item1.class} {$item1.class}{/if}" data-subitems-count="{$item1.$childs|count}">
                        {if $item1.$childs}
                            <span class="ty-menu__item-toggle cm-responsive-menu-toggle">
                                <i class="ut2-mark-fold-unfold"></i>
                            </span>
                        {/if}

                        <a href="{if $item1.$childs|count < 1}{$item1_url|default:"javascript:void(0)"}{else}{$url_mobile|default:"javascript:void(0)"}{/if}"{if $item1_url && $item1.new_window == "YesNo::YES"|enum} target="_blank"{/if} class="ut2-menu__link">
                            <span class="ut2-menu__link__in {if $item1.abt__ut2_mwi__status === "YesNo::YES"|enum && $item1.abt__ut2_mwi__desc|strip_tags|trim}has-descr{/if}">
                                {if $item1.abt__ut2_mwi__status === "YesNo::YES"|enum && $item1.abt__ut2_mwi__icon}
                                    {include file="common/image.tpl" images=$item1.abt__ut2_mwi__icon class="ut2-mwi-icon" image_width=32 height=$image_data.height no_ids=true lazy_load=false}
                                {/if}
                                <span class="ut2-menu__link__text">
                                    {strip}
                                    <span class="ut2-menu__link__name">{$item1.$name nofilter}</span>
                                    {if $item1.abt__ut2_mwi__status === "YesNo::YES"|enum && $item1.abt__ut2_mwi__label}
                                        <span class="m-label"
                                              style="color: {$item1.abt__ut2_mwi__label_color}; background-color: {$item1.abt__ut2_mwi__label_background}; {if $item1.abt__ut2_mwi__label_background == '#ffffff'}border: 1px solid {$item1.abt__ut2_mwi__label_color}{else}border: 1px solid {$item1.abt__ut2_mwi__label_background};{/if}">{$item1.abt__ut2_mwi__label}</span>
                                    {/if}
                                    {/strip}
                                    {if $item1.abt__ut2_mwi__desc|strip_tags|trim}
                                        <span class="ut2-mwi-text">{$item1.abt__ut2_mwi__desc|strip_tags|trim}</span>
                                    {/if}
                                </span>
                                {if $show_second_level}
                                    <i class="ut2-icon-outline-arrow_forward"></i>
                                {/if}
                            </span>
                        </a>
                        {if $show_second_level}
                        {capture name="children"}
                            {$col_width = 100 / $settings_cols}
                            {include file="blocks/menu/components/vertical/`$block.properties.abt__ut2_filling_type|default:'column_filling'`.tpl"}
                        {/capture}

                        {if $block.properties.abt_menu_ajax_load === "YesNo::NO"|enum}
                            <div class="ut2-menu__submenu" id="{$unique_elm_id}">
                                {if $item1_url}
                                    <a href="{$item1_url}" class="ut2-menu__mob-more-cat-link" target="_self">
                                        <span class="ut2-menu__mob-more-cat-link__in">{__('all')} - {$item1.$name}</span>
                                        <span class="ty-btn ty-btn__primary">{__('view')}</span>
                                    </a>
                                {/if}
                                {$smarty.capture.children nofilter}
                            </div>
                        {else}
                            <div class="abt__ut2_am ut2-menu__submenu" id="{$unique_elm_id}_{$smarty.const.DESCR_SL}_{$settings.ab__device}"></div>
                            {$smarty.capture.children|fn_abt__ut2_ajax_menu_save:$unique_elm_id}
                            {/if}
                        {/if}
                    </li>
                {/foreach}
                {/hook}
            </ul>
        </div>
    </div>
{/if}
{if $block.properties.abt_menu_ajax_load == "YesNo::YES"|enum && !$smarty.capture.ut2_mwi_ajax_upload_included}
    {capture name="ut2_mwi_ajax_upload_included"}1{/capture}

    {include file="blocks/menu/components/ajax_upload.tpl"}
{/if}
{/strip}
{/hook}