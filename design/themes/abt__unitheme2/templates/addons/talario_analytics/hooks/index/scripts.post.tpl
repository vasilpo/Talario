{assign var="talario_is_booking_order" value=false}
{if $order_info.products|default:[]}
    {foreach $order_info.products as $talario_order_product}
        {if $talario_order_product.extra.booking_info.booking_type|default:"" == "T"}
            {assign var="talario_is_booking_order" value=true}
        {/if}
    {/foreach}
{/if}
<script>
window.talarioAnalyticsContext = {
    controller: {$runtime.controller|default:""|json_encode nofilter},
    mode: {$runtime.mode|default:""|json_encode nofilter},
    product_id: {$product.product_id|default:0|intval},
    company_id: {$product.company_id|default:0|intval},
    order_id: {$order_info.order_id|default:0|intval},
    order_total: {$order_info.total|default:0|json_encode nofilter},
    order_status: {$order_info.status|default:""|json_encode nofilter},
    is_booking_order: {if $talario_is_booking_order}true{else}false{/if}
};
</script>
{script src="js/addons/talario_analytics/behavior.js"}
