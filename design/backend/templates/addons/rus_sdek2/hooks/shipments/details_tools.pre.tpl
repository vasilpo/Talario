{if $shipment.carrier === "sdek2" && $sdek_shipment_created}
    <li>{btn type="list" text=__("rus_sdek2.receipt_order") href="orders.sdek_get_ticket?order_id=`$order_info.order_id`&shipment_id=`$shipment.shipment_id`" class="cm-new-window"}</li>
{/if}
