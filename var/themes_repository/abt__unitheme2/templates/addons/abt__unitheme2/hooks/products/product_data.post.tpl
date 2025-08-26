
{capture name="abt__service_buttons_id"}{if $block.properties}ut2_list_buttons_{$obj_id}_{$block.block_id}_{$selected_layout|default:{str_replace("/", "_", substr($block.properties.template|default:"", 0,-4))}}{/if}{/capture}


{$c_name = "add_to_cart_`$obj_id`"}
{$sticky_add_to_cart_position = $settings.abt__ut2.products.view.show_sticky_panel_add_to_cart[$settings.ab__device]}
{if $sticky_add_to_cart_position !== "none" && $details_page && $smarty.capture.$c_name|strpos:'checkout.add..'  && $smarty.request.dispatch == 'products.view'}
    {include file="buttons/sticky_add_to_cart.tpl" sticky_add_to_cart_position=$sticky_add_to_cart_position}
{/if}