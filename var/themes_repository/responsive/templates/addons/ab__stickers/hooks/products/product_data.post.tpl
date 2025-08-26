{$display_places = fn_ab__stickers_get_display_places(['hook_themes' => 'YesNo::NO'|enum])}

{if $display_places}
    {include file="addons/ab__stickers/views/ab__stickers/components/stickers_wrapper.tpl" display_places=$display_places}
{/if}

{if !$display_places.product_labels}
    {$product_id = 0}

    {if $product.product_id}
        {$product_id = $product.product_id}
    {elseif $smarty.request.product_id}
        {$product_id = $smarty.request.product_id}
    {/if}

    {if $is_allow_add_common_products_to_cart_list && $product_id}
        {if $obj_prefix}
            {$obj_real_id = "`$obj_prefix`_$product_id"}
        {else}
            {$obj_real_id = "$product_id"}
        {/if}
    {else}
        {$obj_real_id = "`$obj_prefix``$obj_id`"}
    {/if}

    {$real_capture_name = "product_labels_`$obj_real_id`"}

    {capture name=$real_capture_name}{/capture}
{/if}