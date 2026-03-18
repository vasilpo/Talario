{if $gift_cert}
    {$notification_image_width = $notification_image_width|default:"50"}
    {$notification_image_height = $notification_image_height|default:"50"}
    <div class="ty-product-notification__item clearfix">
        {include file="addons/gift_certificates/views/gift_certificates/components/gift_certificates_cart_icon.tpl" width=$notification_image_width height=$notification_image_height class="ty-product-notification__image"}
        <div class="ty-product-notification__content clearfix">
            <a href="{"gift_certificates.update?gift_cert_id=`$gift_cert.gift_cert_id`"|fn_url}" class="ty-product-notification__product-name">{__("gift_certificate")}</a>
            <div class="ty-product-notification__price">
            {include file="common/price.tpl" value=$gift_cert.display_subtotal span_id="price_`$gift_cert.gift_cert_id`" class="none"}
            </div>
        </div>
    </div>
{/if}