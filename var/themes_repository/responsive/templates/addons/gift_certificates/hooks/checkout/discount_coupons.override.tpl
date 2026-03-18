{$discount_coupons_text = $discount_coupons_text|default:__("promo_code_or_certificate")}
<div class="ty-gift-certificate-coupon ty-discount-coupon__control-group ty-input-append">
    <label for="coupon_field{$position}" class="hidden cm-required">{__("promo_code")}</label>
    <input type="text" class="ty-input-text cm-hint" id="coupon_field{$position}" name="coupon_code" size="40" value="{$discount_coupons_text}" />
    {include file="buttons/go.tpl" but_name="checkout.apply_coupon" alt=__("apply") but_text=__("apply")}
</div>