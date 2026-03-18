{strip}
{capture name="continue_shopping_button"}
    <a class="ty-btn ty-btn__secondary cm-notification-close ">
        <span class="visible-phone">{__("continue_shopping_short")}</span>
        <span class="hidden-phone">{__("continue_shopping")}</span>
    </a>
{/capture}
{include file="design/themes/responsive/templates/views/checkout/components/product_notification.tpl"
    amount=$amount
    display_subtotal=$display_subtotal
    proceed_to_checkout_href=$proceed_to_checkout_href
    settings=$settings
    smarty=$smarty
    continue_shopping_button=$smarty.capture.continue_shopping_button
}
{/strip}