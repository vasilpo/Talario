{if $addons.ab__image_previewers.ps_display_price == "Y" && $smarty.capture.show_price_values}

    <div class="pswp__price-wrap{if $product.discount || $product.list_discount} discounted{/if}">
        <bdi>
            {include file="common/price.tpl" value=$product.price}
        </bdi>
        {assign var="clean_price" value="clean_price_`$obj_id`"}
        {$smarty.capture.$clean_price nofilter}
    </div>

{/if}
{if $addons.ab__image_previewers.ps_display_add_to_cart == "Y" && $smarty.capture.ab__ip_cart_button_id}
    <div class="pswp__button_external" data-ca-external-click-id="{$smarty.capture.ab__ip_cart_button_id}">
        {include file="buttons/add_to_cart.tpl" but_text=__("add_to_cart") but_name=""}
    </div>
{/if}