{strip}
{$show_cart_bottom_fixed = $show_cart_bottom_fixed|default:true}
{$show_total_value = $show_total_value|default:true}
{$show_items = $show_items|default:true}
{$show_proceed_to_checkout = $show_proceed_to_checkout|default:true}

{hook name="checkout:cart_bottom_fixed"}
{if $show_cart_bottom_fixed}
    <div class="ty-cart-bottom-fixed" id="cart_bottom_fixed">
        <div class="ty-cart-bottom-fixed__inner">
            <div class="ty-cart-bottom-fixed__left">
                {if $show_total_value}
                    <div class="ty-cart-bottom-fixed__total-value">
                        {include file="common/price.tpl"
                            value=$total_value|default:$_total|default:$smarty.capture._total|default:$cart.total
                            span_id="cart_total_bottom_fixed"
                            class="ty-price"
                        }
                    </div>
                {/if}
                {if $show_items}
                    <div class="ty-cart-bottom-fixed__items">
                        {$cart_amount|default:$cart.amount} {__("items")}
                    </div>
                {/if}
            </div>
            <div class="ty-cart-bottom-fixed__right">
                {if $show_proceed_to_checkout && $payment_methods}
                    {include file="buttons/proceed_to_checkout.tpl"
                        but_text=__("proceed_to_checkout_short")
                        but_href=$proceed_to_checkout_href|default:"checkout.checkout"
                    }
                {/if}
            </div>
        </div>
    <!--cart_bottom_fixed--></div>
{/if}
{/hook}
{/strip}