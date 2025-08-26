<div class="ut2-cart-statistic">
    <div class="ut2-cart-statistic__item ut2-cart-statistic_subtotal">
        <span class="ut2-cart-statistic__title">{__("subtotal")}</span>
        <span class="ut2-cart-statistic__value">{include file="common/price.tpl" value=$cart.display_subtotal}</span>
    </div>
    
    {hook name="checkout:checkout_totals"}
        {$show_shipping_estimation = ($location != "cart" || $settings.Checkout.estimate_shipping_cost == "YesNo::YES"|enum)}
        {if $cart.shipping_required == true}
            <div class="ut2-cart-statistic__item ut2-cart-statistic__item_shipping-method">
            {if $cart.shipping}
                <span class="ut2-cart-statistic__title">
                    {foreach $cart.shipping as $shipping_id => $shipping name="f_shipp"}
                        {$shipping.shipping}{if !$smarty.foreach.f_shipp.last}, {/if}
                    {/foreach}
                    {if $show_shipping_estimation}
                        <span class="ty-nowrap">({$smarty.capture.shipping_estimation|trim nofilter})</span>
                    {/if}
                </span>
                <span class="ut2-cart-statistic__value">{include file="common/price.tpl" value=$cart.display_shipping_cost}</span>
            {elseif $show_shipping_estimation}
                <span class="ut2-cart-statistic__title">{__("shipping_cost")}</span>
                <span class="ut2-cart-statistic__value">{$smarty.capture.shipping_estimation nofilter}</span>
            {/if}
            </div>
        {/if}
    {/hook}
    
    {if ($cart.discount|floatval)}
    <div class="ut2-cart-statistic__item ut2-cart-statistic__item_discount">
        <span class="ut2-cart-statistic__title">{__("including_discount")}</span>
        <span class="ut2-cart-statistic__value discount-price">{include file="common/price.tpl" value="-`$cart.discount`"}</span>
    </div>
    
    {/if}

    {if ($cart.subtotal_discount|floatval)}
    <div class="ut2-cart-statistic__item ut2-cart-statistic__item_subtotal-discount">
        <span class="ut2-cart-statistic__title">{__("order_discount")}</span>
        <span class="ut2-cart-statistic__value discount-price">{include file="common/price.tpl" value="-`$cart.subtotal_discount`"}</span>
    </div>
    {hook name="checkout:checkout_discount"}{/hook}
    {/if}

    {if $cart.taxes}
    <div class="ut2-cart-statistic__item ut2-cart-statistic__item_taxes ut2-cart-statistic__item_group">
        <span class="ut2-cart-statistic__title ut2-cart-statistic_title_main">{__("taxes")}</span>
    </div>
    {foreach from=$cart.taxes item="tax"}
    <div class="ut2-cart-statistic__item ut2-cart-statistic__item_tax">
        <span class="ut2-cart-statistic__title">{$tax.description}&nbsp;({include file="common/modifier.tpl" mod_value=$tax.rate_value mod_type=$tax.rate_type}{if $tax.price_includes_tax == "Y" && ($settings.Appearance.cart_prices_w_taxes != "Y" || $settings.Checkout.tax_calculation == "subtotal")}&nbsp;{__("included")}{/if})</span>
        <span class="ut2-cart-statistic__value">{include file="common/price.tpl" value=$tax.tax_subtotal}</span>
    </div>
    {/foreach}
    {/if}

    {if $cart.payment_surcharge && !$take_surcharge_from_vendor}
    <div class="ut2-cart-statistic__item ut2-cart-statistic__item_payment-surcharge" id="payment_surcharge_line">
        {assign var="payment_data" value=$cart.payment_id|fn_get_payment_method_data}
        <span class="ut2-cart-statistic__title">{$cart.payment_surcharge_title|default:__("payment_surcharge")}{if $payment_data.payment}&nbsp;({$payment_data.payment}){/if}:</span>
        <span class="ut2-cart-statistic__value">{include file="common/price.tpl" value=$cart.payment_surcharge span_id="payment_surcharge_value"}</span>
    </div>
    {math equation="x+y" x=$cart.total y=$cart.payment_surcharge assign="_total"}
    {capture name="_total"}{$_total}{/capture}
    {/if}
</div>