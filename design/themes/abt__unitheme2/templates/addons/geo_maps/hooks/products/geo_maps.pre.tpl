{$show_shipping_estimation = $show_shipping_estimation|default:true}
{$talario_map_product_id = $shipping_estimation_product_id|default:$product.product_id|default:0}

{if $show_shipping_estimation && $talario_map_product_id|intval != 1238}
    {include
        file = "addons/geo_maps/views/geo_maps/shipping_estimation.tpl"
        shipping_methods = null
        product_id = $shipping_estimation_product_id|default:null
    }
{/if}