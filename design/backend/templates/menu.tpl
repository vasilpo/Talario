{$link_tpl = $config.current_url|fn_link_attach:"main_menu_type="}
<div class="cs-main-menu"
    data-menu="main"
    {foreach $main_menu_type_variants as $id => $item}
        data-menu-toggle-{$id}-href="{$link_tpl}{$id}"
    {/foreach}
    data-menu-default-type="{$main_menu_type}"
>
    <div class="cs-main-menu__header">
        <div class="cs-main-menu__mobile-menu-closer-btn-wrapper mobile-visible">
            <button type="button" class="btn mobile-menu-closer-btn" data-mobile-menu="closer">
                {include_ext file="common/icon.tpl"
                    class="icon icon-remove overlay-navbar-open"
                }
            </button>
        </div>
        <div class="cs-main-menu__logo-wrapper mobile-hidden">
            {include file="components/menu/logo_menu.tpl"}
        </div>
    </div>
    <div class="cs-main-menu__outer" id="header_navbar">
        <div class="cs-main-menu__inner">
            {if $auth.user_id}
                {* Get main menu content: $primary_items, $attrs_wrapper, $show_collapse_default *}
                {include file="components/menu/get_primary_items.tpl"
                    navigation=$navigation
                    quick_menu=$quick_menu
                }
                <div class="cs-main-menu__primary {$main_menu_primary_class}">
                    {include file="components/menu/main_menu.tpl"
                        items=$primary_items
                        prefix="primary"
                        show_collapse_default=$show_collapse_default
                    }
                </div>

                {* Get secondary menu content: $secondary_items, $attrs_wrapper, $show_collapse_default *}
                {include file="components/menu/get_secondary_items.tpl"
                    navigation=$navigation
                }
                {if $secondary_items}
                    <div class="cs-main-menu__secondary">
                        {include file="components/menu/main_menu.tpl"
                            items=$secondary_items
                            prefix="secondary"
                            show_collapse_default=$show_collapse_default
                        }
                    </div>
                {/if}
                {include file="components/menu/main_menu_toggle.tpl"}
            {/if}
        </div>
    <!--header_navbar--></div>
</div>
<div class="cs-main-menu__backdrop" data-mobile-menu="closer">
</div>

{* Content of quick menu *}
{include file="common/quick_menu.tpl"
    quick_menu=$quick_menu
}
