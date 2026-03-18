{strip}
{$wishlist_count = ""|fn_wishlist_get_count scope=parent}

{$navbar_mobile_items = array_merge($navbar_mobile_items, [
    wishlist => [
        id => "wishlist",
        insert_before_id => "account",
        container_id => "account_info_navbar_mobile_wishlist",
        href => "wishlist.view",
        controllers => ["wishlist"],
        badge => (($wishlist_count > 0) ? $wishlist_count : ""),
        icon => "ty-icon-heart",
        text => __("wishlist.navbar_mobile.text")
    ]
])}

{* Export *}
{$navbar_mobile_items = $navbar_mobile_items scope=parent}
{/strip}