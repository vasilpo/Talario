{if !isset($show_buy_now) && isset($show_add_to_cart_secondary) && $show_add_to_cart_secondary === false}
    {$show_buy_now = false}
{/if}
{if !$hide_form
    && $addons.call_requests.buy_now_with_one_click == "YesNo::YES"|enum
    && ($auth.user_id
        || $settings.Checkout.allow_anonymous_shopping == "allow_shopping"
    ) && $show_buy_now|default:true
}
    {$is_not_required_option = true}
    {$add_to_cart_type = $add_to_cart_type|default:"text"}
    {$add_to_cart_secondary_type = $add_to_cart_secondary_type|default:"text"}

    {if $add_to_cart_type === "icon"}
        {$buy_now_but_text = ""}
        {$buy_now_but_icon = "ty-icon-shopping-bag-speed"}
        {$buy_now_but_meta_with_popup = "ty-btn ty-btn-icon ty-cr-product-button cm-dialog-destroy-on-close"}
        {$buy_now_but_meta_simple = "btn ty-btn ty-btn-icon ty-cr-product-button"}
    {else}
        {* $add_to_cart_type === "text" *}
        {$buy_now_but_text = __("call_requests.buy_now_with_one_click")}
        {$buy_now_but_icon = ""}
        {$buy_now_but_meta_with_popup = "ty-btn ty-cr-product-button cm-dialog-destroy-on-close"}
        {$buy_now_but_meta_simple = "btn ty-btn ty-cr-product-button"}
    {/if}

    {if $add_to_cart_secondary_type === "tertiary"}
        {$buy_now_but_meta_with_popup = "`$buy_now_but_meta_with_popup` ty-btn__tertiary"}
        {$buy_now_but_meta_simple = "`$buy_now_but_meta_simple` ty-btn__tertiary"}
    {else}
        {$buy_now_but_meta_with_popup = "`$buy_now_but_meta_with_popup` ty-btn__text"}
        {$buy_now_but_meta_simple = "`$buy_now_but_meta_simple` ty-btn__text"}
    {/if}

    {foreach $product.product_options as $option}
        {if $option.required === "YesNo::YES"|enum}
            {$is_not_required_option = false}
            {break}
        {/if}
    {/foreach}
    {if (
        $settings.General.inventory_tracking == "YesNo::NO"|enum
        || $settings.General.allow_negative_amount == "YesNo::YES"|enum
        || (
            $product_amount > 0
            && $product_amount >= $product.min_qty
        )
        || $product.tracking == "ProductTracking::DO_NOT_TRACK"|enum
        || $product.is_edp == "YesNo::YES"|enum
        || $product.out_of_stock_actions == "OutOfStockActions::BUY_IN_ADVANCE"|enum
    )}
        {hook name="call_requests:call_request_button"}
            {if $show_product_options || ($is_not_required_option || $details_page)}
                {include file="common/popupbox.tpl"
                    href="call_requests.request?product_id={$product.product_id}&obj_prefix={$obj_prefix}"
                    link_text=$buy_now_but_text
                    text=__("call_requests.buy_now_with_one_click")
                    id="buy_now_with_one_click_{$obj_prefix}{$product.product_id}"
                    link_meta=$buy_now_but_meta_with_popup
                    content=""
                    dialog_additional_attrs=["data-ca-product-id" => $product.product_id, "data-ca-dialog-purpose" => "call_request"]
                    link_icon=$buy_now_but_icon
                }
            {else}
                {include file="buttons/button.tpl"
                    but_text=$buy_now_but_text
                    but_href="products.view?product_id=`$product.product_id`"
                    but_role="text"
                    but_id="buy_now_with_one_click_{$obj_prefix}{$product.product_id}"
                    but_meta=$buy_now_but_meta_simple
                    but_icon=$buy_now_but_icon
                }
            {/if}
        {/hook}
    {/if}
{/if}