{assign var="show_old_price" value=true}
{assign var="show_clean_price" value=true}
{assign var="show_rating" value=true}
{assign var="show_qty" value=$settings.abt__ut2.product_list.$tmpl.show_qty[$settings.ab__device] === "YesNo::YES"|enum}
{assign var="hide_qty_label" value=true}

{if $settings.abt__ut2.product_list.$tmpl.show_amount[$settings.ab__device] === "YesNo::YES"|enum}
    {assign var="show_product_amount" value=true}
{/if}

{$button_type_add_to_cart = $settings.abt__ut2.product_list.$tmpl.show_button_add_to_cart[$settings.ab__device]}

{if $product}
    {assign var="obj_id" value=$obj_id|default:$product.product_id}
    {assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
    {include file="common/product_data.tpl" obj_id=$obj_id product=$product show_labels_in_title=$show_labels_in_title}

    <div class="ut2-simple-list clearfix" style="--sl-lines-in-name-product: {$settings.abt__ut2.product_list.$tmpl.lines_number_in_name_product[$settings.ab__device]};">
        {assign var="form_open" value="form_open_`$obj_id`"}
        {$smarty.capture.$form_open nofilter}
        {if $item_number == "YesNo::YES"|enum}<strong>{$smarty.foreach.products.iteration}.&nbsp;</strong>{/if}

        {assign var="product_labels" value="product_labels_`$obj_prefix``$obj_id`"}
        {$smarty.capture.$product_labels nofilter}

        <div class="ut2-w-c-q__buttons {if $settings.abt__ut2.product_list.hover_buttons_w_c_q[$settings.ab__device] === "YesNo::YES"|enum}w_c_q-hover{/if}" {if $smarty.capture.abt__service_buttons_id}id="{$smarty.capture.abt__service_buttons_id}"{/if}>
            {if !$quick_view && $settings.Appearance.enable_quick_view === "YesNo::YES"|enum && $settings.abt__ut2.product_list.products_scroller.show_quick_view_button[$settings.ab__device] === "YesNo::YES"|enum || !$quick_view && $settings.Appearance.enable_quick_view === "YesNo::YES"|enum && !$hide_quick_view_button}
                {include file="views/products/components/quick_view_link.tpl" quick_nav_ids=$quick_nav_ids}
            {/if}
            {if $addons.wishlist.status == "ObjectStatuses::ACTIVE"|enum && !$hide_wishlist_button && $settings.abt__ut2.product_list.button_wish_list_view[$settings.ab__device] === "YesNo::YES"|enum}
                {include file="addons/wishlist/views/wishlist/components/add_to_wishlist.tpl" but_id="button_wishlist_`$obj_prefix``$product.product_id`" but_name="dispatch[wishlist.add..`$product.product_id`]" but_role="text"}
            {/if}
            {if $settings.General.enable_compare_products === "YesNo::YES"|enum && !$hide_compare_list_button && $settings.abt__ut2.product_list.button_compare_view[$settings.ab__device] === "YesNo::YES"|enum}
                {include file="buttons/add_to_compare_list.tpl" product_id=$product.product_id}
            {/if}
            <!--{$smarty.capture.abt__service_buttons_id}--></div>

        <div class="ut2-simple-list__wrap {if $settings.abt__ut2.product_list.price_position_top == "YesNo::YES"|enum}price-top-position{/if}">
            {assign var="name" value="name_$obj_id"}{$smarty.capture.$name nofilter}
            {assign var="sku" value="sku_$obj_id"}{$smarty.capture.$sku nofilter}

            {include file="blocks/product_list_templates/components/average_rating.tpl"}

            {assign var="product_amount" value="product_amount_`$obj_id`"}
            {$smarty.capture.$product_amount nofilter}

            {if $capture_options_vs_qty}{capture name="product_options"}{/if}

            {if $show_features || $show_descr}
                <div class="ut2-simple-list__feature">{assign var="product_features" value="product_features_`$obj_id`"}{$smarty.capture.$product_features nofilter}</div>
                <div class="ut2-simple-list__descr">{assign var="prod_descr" value="prod_descr_`$obj_id`"}{$smarty.capture.$prod_descr nofilter}</div>
            {/if}

            {assign var="product_options" value="product_options_`$obj_id`"}
            {$smarty.capture.$product_options nofilter}

            {assign var="advanced_options" value="advanced_options_`$obj_id`"}
            {$smarty.capture.$advanced_options nofilter}
            {if $capture_options_vs_qty}{/capture}{/if}

            {assign var="min_qty" value="min_qty_`$obj_id`"}
            {$smarty.capture.$min_qty nofilter}

            {assign var="product_edp" value="product_edp_`$obj_id`"}
            {$smarty.capture.$product_edp nofilter}

            {capture name="simple_list_control_data_wrapper"}
                {if $show_add_to_cart && $button_type_add_to_cart != 'none'}

                    {assign var="qty" value="qty_`$obj_id`"}

                    <div class="ut2-simple-list__control {if $settings.abt__ut2.product_list.$tmpl.show_buttons_on_hover[$settings.ab__device] === "YesNo::YES"|enum} hidden{/if}{if $settings.abt__ut2.product_list.$tmpl.show_qty[$settings.ab__device] === "YesNo::YES"|enum && $smarty.capture.$qty|strip_tags:false|replace:"&nbsp;":""|trim|strlen} ut2-view-qty{/if}{if $button_type_add_to_cart != 'none'} {$button_type_add_to_cart}{/if}">

                        {capture name="simple_list_control_data"}
                            {hook name="products:simple_list_control"}
                            {if $show_qty && $smarty.capture.$qty|strip_tags:false|replace:"&nbsp;":""|trim|strlen}
                                {$smarty.capture.$qty nofilter}
                            {/if}
                            {$add_to_cart = "add_to_cart_`$obj_id`"}
                            {$smarty.capture.$add_to_cart nofilter}
                            {/hook}
                        {/capture}
                        {$smarty.capture.simple_list_control_data nofilter}
                    </div>
                {/if}
            {/capture}

            {if $button_type_add_to_cart == 'icon' || $button_type_add_to_cart == 'icon_button'}
            <div class="ut2-simple-list__mix-price-and-button {if $show_qty}qty-wrap{/if}">
            {/if}

            {if !$hide_price}
                <div class="ut2-simple-list__price pr-{$settings.abt__ut2.product_list.price_display_format}{if $product.list_discount || $product.discount} pr-color{/if}">
                    <div>
                        {assign var="price" value="price_`$obj_id`"}
                        {$smarty.capture.$price nofilter}

                        {if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "short"}<span>{/if}
                        {assign var="old_price" value="old_price_`$obj_id`"}
                        {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}

                        {assign var="list_discount" value="list_discount_`$obj_id`"}
                        {$smarty.capture.$list_discount nofilter}
                        {if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "short"}</span>{/if}
                    </div>
                    {assign var="clean_price" value="clean_price_`$obj_id`"}
                    {$smarty.capture.$clean_price nofilter}
                </div>
            {/if}

            {if $settings.abt__ut2.product_list.$tmpl.show_qty[$settings.ab__device] === "YesNo::NO"|enum || $settings.ab__device === "desktop"}
                {if $button_type_add_to_cart == 'icon' || $button_type_add_to_cart == 'icon_button'}
                    {if $smarty.capture.simple_list_control_data|trim}
                        {$smarty.capture.simple_list_control_data_wrapper nofilter}
                    {/if}
                {/if}
            {/if}

            {if $button_type_add_to_cart == 'icon' || $button_type_add_to_cart == 'icon_button'}
            </div>
            {/if}
        </div>

        {if $button_type_add_to_cart == 'text' || $button_type_add_to_cart == 'icon_and_text' || $settings.abt__ut2.product_list.$tmpl.show_qty[$settings.ab__device] === "YesNo::YES"|enum && $settings.ab__device !== "desktop"}
            {if $smarty.capture.simple_list_control_data|trim}
                {$smarty.capture.simple_list_control_data_wrapper nofilter}
            {/if}
        {/if}

        {hook name="products:product_list_form_close_tag"}
        {assign var="form_close" value="form_close_`$obj_id`"}
        {$smarty.capture.$form_close nofilter}
        {/hook}
    </div>
{/if}