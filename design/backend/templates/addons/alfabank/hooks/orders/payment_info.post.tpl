{if fn_alfabank_can_refund_order($order_info)}
        <a class="btn btn-block cm-dialog-opener cm-ajax idabi cm-dialog-destroy-on-close"
           href="{"alfabank.payment_management?order_id=`$order_info.order_id`"|fn_url}"
           data-ca-dialog-title="{__("addons.alfabank.payment_management")} - Alfabank"
        >{__("addons.alfabank.payment_management")}</a>
{/if}