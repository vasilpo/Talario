{strip}
{*
    $obj_id
    $show_product_bottom_fixed
    $show_price
    $show_list_price
    $show_clean_price
    $show_add_to_cart
    $smarty
*}

{$price_only_secondary = "price_only_secondary_`$obj_id`"}
{$old_price_only_secondary = "old_price_only_secondary_`$obj_id`"}
{$clean_price_only_secondary = "clean_price_only_secondary_`$obj_id`"}
{$add_to_cart_button_secondary = "add_to_cart_button_secondary_`$obj_id`"}
{$show_price = (($show_price === true || !isset($show_price))
    && $smarty.capture.$price_only_secondary|trim !== "")
}
{$show_list_price = (($show_list_price === true || !isset($show_list_price))
    && $smarty.capture.$old_price_only_secondary|trim !== "")
}
{$show_clean_price = (($show_clean_price === true || !isset($show_clean_price))
    && $smarty.capture.$clean_price_only_secondary|trim !== ""
)}
{$show_add_to_cart = (($show_add_to_cart === true || !isset($show_add_to_cart))
    && $smarty.capture.$add_to_cart_button_secondary|trim !== ""
)}
{$show_product_bottom_fixed = (($show_product_bottom_fixed === true || !isset($show_product_bottom_fixed))
    && ($show_price || $show_list_price || $show_clean_price || $show_add_to_cart))
}

{if $show_product_bottom_fixed}
    <div class="ty-product-bottom-fixed" id="product_bottom_fixed">
        <div class="ty-product-bottom-fixed__inner">
            {if $show_price || $show_list_price || $show_clean_price}
                <div class="ty-product-bottom-fixed__left">
                    {if $show_price || $show_list_price}
                        <div class="ty-product-bottom-fixed__left-row">
                            {if $show_price}
                                <div class="ty-product-bottom-fixed__price">
                                    {$smarty.capture.$price_only_secondary nofilter}
                                </div>
                            {/if}

                            {if $show_list_price}
                                <div class="ty-product-bottom-fixed__list-price">
                                    {$smarty.capture.$old_price_only_secondary nofilter}
                                </div>
                            {/if}
                        </div>
                    {/if}

                    {if $show_clean_price}
                        <div class="ty-product-bottom-fixed__left-row">
                            <div class="ty-product-bottom-fixed__clean-price">
                                {$smarty.capture.$clean_price_only_secondary nofilter}
                            </div>
                        </div>
                    {/if}
                </div>
            {/if}

            {if $show_add_to_cart}
                <div class="ty-product-bottom-fixed__right">
                    <div class="ty-product-bottom-fixed__right-row">
                        <div class="ty-product-bottom-fixed__add-to-cart">
                            {$smarty.capture.$add_to_cart_button_secondary nofilter}
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    <!--product_bottom_fixed--></div>
{/if}
{/strip}