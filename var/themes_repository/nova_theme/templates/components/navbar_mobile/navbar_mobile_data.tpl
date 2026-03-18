{strip}
{$navbar_mobile_items = [
    homepage => [
        id => "homepage",
        href => "",
        not_controllers => [],
        icon => "ty-icon-home",
        text => __("navbar_mobile.homepage")
    ],
    menu => [
        id => "menu",
        href => "menus.manage?mainbox_title={__("navbar_mobile.menu")}",
        controllers => ["menus", "categories", "products"],
        icon => "ty-icon-category-search",
        text => __("navbar_mobile.menu")
    ],
    cart => [
        id => "cart",
        container_id => "cart_status_navbar_mobile",
        href => "checkout.cart",
        controllers => ["checkout"],
        badge => (($smarty.session.cart.amount > 0) ? $smarty.session.cart.amount : ""),
        icon => "ty-icon-basket",
        text => __("navbar_mobile.cart")
    ],
    account => [
        id => "account",
        href => "profiles.manage",
        controllers => ["profiles", "auth", "orders", "product_features"],
        icon => "ty-icon-account-circle",
        text => __("navbar_mobile.account")
    ]
]}

{if $user_info && $user_info.firstname && $user_info.lastname}
    {capture name="account_custom_icon" assign="account_custom_icon"}
        <span class="ty-navbar-mobile__item-profile-icon">
            {$user_info.firstname|upper|truncate:1:""}{$user_info.lastname|upper|truncate:1:""}
        </span>
    {/capture}
    {$navbar_mobile_items.account.custom_icon = $account_custom_icon}
{/if}

{hook name="index:navbar_mobile"}{/hook}

{* Export *}
{$navbar_mobile_items = $navbar_mobile_items scope=parent}
{/strip}