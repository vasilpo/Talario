{if $show_main_menu_toggle && $main_menu_type_variants}{strip}
    <div class="cs-main-menu__bottom-toolbar mobile-hidden">
        <div class="main-menu main-menu-1">
            {foreach $main_menu_type_variants as $id => $item}
                <div class="main-menu__item main-menu-1__item" {""}
                    data-main-menu="item" {""}
                    data-main-menu-item-level="1" {""}
                >
                    <div class="main-menu-1__link-wrapper main-menu__link-wrapper--main-menu-toggle">
                        <button name="{$id}" {""}
                            class="main-menu__link main-menu-1__link cs-main-menu-toggle__btn--menu-type-{$id}" {""}
                            title="{__("main_menu_type.switch_to_`$id`")}" {""}
                            data-main-menu-toggle="btn" {""}
                        >
                            <span class="main-menu-1__link-icon">
                                {include_ext file="common/icon.tpl"
                                    source="double_angle_down"
                                    class="cs-main-menu-toggle__icon"
                                }
                            </span>
                            <span class="main-menu-1__link-content ">
                                {__("main_menu_type.switch_to_`$id`")}
                            </span>
                        </button>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
{/strip}{/if}