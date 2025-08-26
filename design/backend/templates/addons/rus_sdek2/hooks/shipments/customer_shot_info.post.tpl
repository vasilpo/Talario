{if $shipment.carrier === "sdek2" && $status}
    <p class="strong">{__("rus_sdek2.delivery_status")}</p>
    <p>{$status.status}</p>
    <p class="strong">{__("rus_sdek2.location")}</p>
    <p>{$status.city}</p>
{/if}