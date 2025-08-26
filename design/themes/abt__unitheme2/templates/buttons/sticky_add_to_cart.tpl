{$but_role = 'act'}
{assign var="obj_id" value=$product.product_id}

<div class="ut2-pb__sticky-add-to-cart {if $sticky_add_to_cart_position}position-{$sticky_add_to_cart_position}{/if} cm-reload-{$product.product_id}" id="ut2_pb__sticky_add_to_cart">
    <div class="ut2-pb__sticky-add-to-cart_block">
        {if $settings.ab__device !== "mobile"}
        <div class="ut2-pb__sticky-add-to-cart_product">
            {include file="common/image.tpl" image_width="40" image_height="40" images=$product.main_pair no_ids=true lazy_load=false}

            <div class="ut2-pb__sticky-add-to-cart_product-content">
                <div class="ut2-pb__sticky-add-to-cart_product-name">{$product.product|default:fn_get_product_name($product.product_id) nofilter}</div>

                <div class="ut2-pb__sticky-add-to-cart_product-rating">
                    {include file="blocks/product_templates/components/product_rating.tpl"}
                </div>
            </div>
        </div>
        {/if}

        <div class="ut2-pb__sticky-add-to-cart_price">
            {assign var="old_price" value="old_price_`$obj_id`"}
            {assign var="price" value="price_`$obj_id`"}
            {assign var="clean_price" value="clean_price_`$obj_id`"}
            {assign var="list_discount" value=false}

            {include file="blocks/product_templates/components/product_price.tpl" pp_compact_view=true}
        </div>

        <div class="ut2-pb__sticky-add-to-cart_list-buttons" id="abt__service_buttons_id_{$obj_id}">

            {if $addons.wishlist.status == "ObjectStatuses::ACTIVE"|enum && !$hide_wishlist_button}
            <div class="cm-external-click" data-ca-external-click-id="button_wishlist_{$obj_id}">
                {include file="addons/wishlist/views/wishlist/components/add_to_wishlist.tpl" but_id="button_wishlist_`$obj_id`" but_name="dispatch[wishlist.add..`$obj_id`]" but_role="text" hidden_label=true hidden_but_label=true but_title=false but_tooltip=false}
            </div>
            {/if}

            {if $settings.General.enable_compare_products == "YesNo::YES"|enum && !$hide_compare_list_button}
            <div>
                {include file="buttons/add_to_compare_list.tpl" hidden_label=true hidden_but_label=true but_title=false but_tooltip=false compare_but_href="0" product_id=$obj_id compare_but_href=null }
            </div>
            {/if}

        <!--abt__service_buttons_id_{$obj_id}--></div>

        <div class="cm-external-click{if $product.product_id} cm-reload-{$product.product_id}{/if}" data-ca-external-click-id="{$_but_id}">
            {include file="buttons/add_to_cart.tpl" but_id=$but_id but_name="dispatch[checkout.add..`$obj_id`]" but_role=$but_role block_width=$block_width obj_id=$obj_id but_target_id=$obj_id product=$product but_meta=$add_to_cart_meta but_text=__("add_to_cart") show_price_in_button=false}
        <!--ut2_pb__sticky_add_to_cart--></div>
    </div>
</div>
