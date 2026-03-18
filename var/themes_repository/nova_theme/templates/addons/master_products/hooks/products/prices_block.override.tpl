{include file="design/themes/responsive/templates/addons/master_products/hooks/products/prices_block.override.tpl"
    product=$product
    hide_add_to_cart_button=$hide_add_to_cart_button
    obj_prefix=$obj_prefix
    obj_id=$obj_id
    details_page=$details_page
    quick_view=$quick_view
    show_other_offers_link=($runtime.controller === "products" && $runtime.mode === "view" || $quick_view)
}

{* Export *}
{$price_only_secondary_override_{$obj_id} = $price_only_secondary_override_{$obj_id} scope=parent}