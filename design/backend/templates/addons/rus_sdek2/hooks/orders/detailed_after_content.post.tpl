{foreach from=$order_info.shipping item="shipping" key="shipping_id" name="f_shipp"}
    {foreach from=$shipping.shipment_keys item="shipment_key"}
        {$shipment = $shipments[$shipment_key]}
        {if $shipment.carrier === "sdek2"}
            {assign var="shipment_id" value=$shipment.shipment_id}

            {if $data_shipments.$shipment_id}
                {capture name="add_new_sdek_picker"}
                    {include file="addons/rus_sdek2/views/shipments/components/new_shipment_sdek.tpl" group_key=$shipment.shipment_id}
                {/capture}
                {include file="common/popupbox.tpl" id="add_new_shipment_sdek_`$shipment.shipment_id`" content=$smarty.capture.add_new_sdek_picker text="{__("rus_sdek2.add_shipment")}" act="hidden"}
            {/if}
        {/if}
    {/foreach}
{/foreach}