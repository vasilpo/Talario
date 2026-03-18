{hook name="blocks:topmenu_dropdown"}

{strip}
{if $items}
    <div class="ut2-h__menu ut2-menu{if $block.properties.abt_menu_long_names === "YesNo::YES"|enum} multi-line-1st-item{/if}{if $block.properties.abt__menu_add_horizontal_scroll_sections|default:{"YesNo::YES"|enum} === "YesNo::YES"|enum} ut2-m-slider{/if}" style="--ut2-horizontal-menu-block-height: {$block.properties.abt__ut2_menu_min_height|default:430}px;">
        <div class="ut2-h__menu__in">

            {if $settings.ab__device === "desktop"}
			<a href="javascript:void(0);" onclick="$(this).next().toggleClass('view');$(this).toggleClass('open');" class="ut2-h__menu__burger{if $block.properties.open_on_sticky_panel_button === "Y"} cm-external-triggered{/if}"><i class="ut2-icon-outline-menu"></i></a>
            {/if}

            {$settings_cols = min(6, $block.properties.abt__ut2_columns_count|default:4)}

            <ul class="ut2-menu__list" style="--menu-columns: {$settings_cols}">
                {hook name="blocks:topmenu_dropdown_top_menu"}

                {foreach from=$items item="item1" name="item1"}
                    {assign var="item1_url" value=$item1|fn_form_dropdown_object_link:$block.type}
                    {assign var="unique_elm_id" value="topmenu_`$block.block_id`_`$block.snapping_id`_`$item1_url|md5`"}
                    {assign var="subitems_count" value=$item1.$childs|count}
                    {assign var="show_second_level" value=$block.properties.dropdown_second_level_elements && ($item1.$childs || ($item1.abt__ut2_mwi__status === "YesNo::YES"|enum && $item1.abt__ut2_mwi__text))}
                    <li class="ut2-menu__item{if !$item1.$childs} item-1st-no-drop{/if}{if $item1.class} {$item1.class}{/if}" data-subitems-count="{$item1.$childs|count}">

                        {if $show_second_level && $settings.ab__device === "desktop"}
							<a class="ty-menu__item-toggle cm-responsive-menu-toggle">
								<i class="ut2-icon-outline-expand_more"></i>
                            </a>
                        {/if}

                        <a href="{$item1_url|default:"javascript:void(0)"}"{if $item1_url && $item1.new_window == "YesNo::YES"|enum} target="_blank"{/if} class="ut2-menu__link{if $item1.$childs} item-1st-has-childs{elseif $item1.abt__ut2_mwi__status === "YesNo::YES"|enum && $item1.abt__ut2_mwi__text} mwi-html{/if}">
	                        <span class="ut2-menu__link__in{if $item1.abt__ut2_mwi__status === "YesNo::YES"|enum && $item1.abt__ut2_mwi__icon} item-icon{/if}">
                                {if $item1.abt__ut2_mwi__status === "YesNo::YES"|enum && $item1.abt__ut2_mwi__icon && $settings.ab__device !== "mobile"}
                                    {include file="common/image.tpl" images=$item1.abt__ut2_mwi__icon class="ut2-mwi-icon" image_width=32 height=$image_data.height no_ids=true lazy_load=false}
                                {/if}
                                <span{if $block.properties.abt_menu_long_names === "YesNo::YES"|enum} style="max-width: {$block.properties.abt_menu_long_names_max_width|intval|default:100}px"{/if} class="ut2-menu__link__text">
                                    {strip}
                                        <span class="ut2-menu__link__name">{$item1.$name nofilter}</span>
                                        {if $item1.abt__ut2_mwi__status === "YesNo::YES"|enum && $item1.abt__ut2_mwi__label}
                                            <span class="m-label"
                                                  style="color: {$item1.abt__ut2_mwi__label_color}; background-color: {$item1.abt__ut2_mwi__label_background}; {if $item1.abt__ut2_mwi__label_background === "#ffffff"}border: 1px solid {$item2.abt__ut1_mwi__label_color}{else}border: 1px solid {$item1.abt__ut2_mwi__label_background};{/if}">
                                                {$item1.abt__ut2_mwi__label}
                                                <span class="arrow"
                                                      style="border-color: {if $item1.abt__ut2_mwi__label_background === "#ffffff"}{$item1.abt__ut2_mwi__label_color}{else}{$item1.abt__ut2_mwi__label_background}{/if} transparent transparent transparent;"></span>
                                            </span>
                                        {/if}
                                    {/strip}
                                </span>
	                        </span>
	                    </a>

                        {if $show_second_level && $settings.ab__device === "desktop"}
                            <div class="ut2-menu__submenu" id="{$unique_elm_id}">
                                {$col_width = 100 / $settings_cols}
                                {include file="blocks/menu/components/horizontal/`$block.properties.abt__ut2_filling_type|default:'column_filling'`.tpl"}
                            </div>
                        {/if}
                    </li>
                {/foreach}
                {/hook}

            </ul>

        </div>
    </div>
{/if}
{/strip}
{/hook}

<script>
    (function(_, $) {
        _.tr({
            abt__ut2_go_back: '{__("go_back")}',
            abt__ut2_go_next: '{__("next")}',
        });
    })(Tygh, Tygh.$);
</script>
{if $block.properties.abt__menu_add_horizontal_scroll_sections|default:{"YesNo::YES"|enum} === "YesNo::YES"|enum}
    {script src="js/addons/abt__unitheme2/abt__ut2_horizontal_menu_slider.js"}
{/if}