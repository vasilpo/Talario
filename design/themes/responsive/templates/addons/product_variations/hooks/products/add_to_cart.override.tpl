{$show_select_variations_button=$show_select_variations_button|default:true}

{if !$details_page && $product.has_child_variations && $show_select_variations_button}
    {$show_select_variation = $show_select_variation|default:true}
    {$select_variation_but_text = $select_variation_but_text|default:__("product_variations.select_variation")}

    {hook name="products:add_to_cart"}
        {include file="addons/product_variations/components/products_add_to_cart_pre.tpl"}

        {if $show_select_variation}
            {include file="buttons/button.tpl"
                but_id="button_cart_`$obj_prefix``$obj_id`"
                but_text=$select_variation_but_text
                but_name=""
                but_href="{"products.view?product_id=`$product.product_id`"|fn_url}"
                but_role=$opt_but_role
                but_meta="ty-btn__primary ty-btn__big"
                but_icon=$select_variation_but_icon
            }
        {/if}
    {/hook}
{/if}
