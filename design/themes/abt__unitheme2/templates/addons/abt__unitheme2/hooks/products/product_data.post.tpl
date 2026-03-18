
{capture name="abt__service_buttons_id"}{if $block.properties}ut2_list_buttons_{$obj_id}_{$block.block_id}_{$selected_layout|default:{str_replace("/", "_", substr($block.properties.template|default:"", 0,-4))}}{/if}{/capture}


{$c_name = "add_to_cart_`$obj_id`"}
{$sticky_add_to_cart_position = $settings.abt__ut2.products.view.show_sticky_panel_add_to_cart[$settings.ab__device]}
{if $sticky_add_to_cart_position !== "none" && $details_page && $smarty.capture.$c_name|strpos:"checkout.add.."  && ($smarty.request.dispatch == "products.view" || $smarty.request.dispatch === "products.options") }
    {include file="buttons/sticky_add_to_cart.tpl" sticky_add_to_cart_position=$sticky_add_to_cart_position}
{/if}


{$_block = $block_data|default:$block}
{$form_capture_id = "form_open_`$obj_id`"}

{capture name=$form_capture_id}
    {$smarty.capture.$form_capture_id nofilter}
    <input type="hidden" name="abt__ut2_variations_block_key" value="{$smarty.request.block_key|default:fn_abt__ut2_get_block_unique_id($_block.block_id)}">
    <input type="hidden" name="abt__ut2_variations_template_name" value="{$smarty.request.template_name|default:fn_abt__ut2_get_current_template_name($_block)}">
    <input type="hidden" name="abt__ut2_variations_form_name" value="form_open_{$obj_id}">
{/capture}
