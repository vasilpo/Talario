{* Only two levels. Vertical output *}
{if !$item1.$childs|fn_check_second_level_child_array:$childs}
    {hook name="blocks:topmenu_dropdown_2levels_elements"}
        {$has_icon = false}
        {foreach $item1.$childs as $child}
            {if $child.abt__ut2_mwi__icon}
                {$has_icon = true}
            {/if}
        {/foreach}

        <div class="ut2-menu__submenu__carrier ut2-simple {if $item1.abt__ut2_mwi__text && $item1.abt__ut2_mwi__dropdown === "YesNo::NO"|enum}{if $item1.abt__ut2_mwi__text_position !== "bottom"}submenu-1st-has-side-banner {else}submenu-1st-has-bottom-banner{/if}{/if}">
            <div class="ut2-menu__submenu__wrapper">
                <div class="ut2-menu__2nd-list">
                    {include file="blocks/menu/components/vertical/two_level_columns.tpl"}
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

            {if $item1.abt__ut2_mwi__status === "YesNo::YES"|enum && $item1.abt__ut2_mwi__dropdown === "YesNo::NO"|enum && $item1.abt__ut2_mwi__text|trim && $settings.ab__device !== "mobile"}
                <div class="ut2-mwi-html {$item1.abt__ut2_mwi__text_position} hidden-phone">
                    <div class="ut2-mwi-html__in">
                        {$item1.abt__ut2_mwi__text nofilter}
                    </div>
                </div>
            {/if}
        </div>
    {/hook}
{else}
    {hook name="blocks:topmenu_dropdown_3levels_cols"}
        <div class="ut2-menu__submenu__carrier {if $item1.abt__ut2_mwi__dropdown === "YesNo::YES"|enum}cascading {else}{$dropdown_class} {/if}{if $item1.abt__ut2_mwi__text && $item1.abt__ut2_mwi__dropdown === "YesNo::NO"|enum}{if $item1.abt__ut2_mwi__text_position !== "bottom"}submenu-1st-has-side-banner {else}submenu-1st-has-bottom-banner{/if}{/if}">
            <div class="ut2-menu__submenu__wrapper">
                <div class="ut2-menu__2nd-list">
                    {include file="blocks/menu/components/vertical/three_level_columns.tpl"}
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

            {if $item1.abt__ut2_mwi__status === "YesNo::YES"|enum && $item1.abt__ut2_mwi__dropdown === "YesNo::NO"|enum && $item1.abt__ut2_mwi__text|trim && $settings.ab__device !== "mobile"}
                <div class="ut2-mwi-html {$item1.abt__ut2_mwi__text_position} hidden-phone">
                    <div class="ut2-mwi-html__in">
                        {$item1.abt__ut2_mwi__text nofilter}
                    </div>
                </div>
            {/if}
        </div>
    {/hook}
{/if}
