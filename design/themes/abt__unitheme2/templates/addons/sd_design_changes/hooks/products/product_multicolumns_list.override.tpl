<!-- The "{$smarty.template}" template was overriden by the "sd_design_changes" add-on -->
{assign var="form_open" value="form_open_{$obj_id}"}
{$smarty.capture.$form_open nofilter}

<div class="ut2-gl__body{if $settings.abt__ut2.product_list.decolorate_out_of_stock_products == "YesNo::YES"|enum && $product.amount < 1 && $product.out_of_stock_actions != "OutOfStockActions::BUY_IN_ADVANCE"|enum} decolorize{/if}">
    <div class="ut2-gl__image">
        {include file="views/products/components/product_icon.tpl" product=$product image_width=$tbw image_height=$tbh thumbnails_size=$thumbnails_size show_gallery=$show_gallery}

        {assign var="product_labels" value="product_labels_{$obj_prefix}{$obj_id}"}
        {$smarty.capture.$product_labels nofilter}

        <div class="ut2-w-c-q__buttons {if $settings.abt__ut2.product_list.hover_buttons_w_c_q[$settings.ab__device] === "YesNo::YES"|enum}w_c_q-hover{/if}" {if $smarty.capture.abt__service_buttons_id}id="{$smarty.capture.abt__service_buttons_id}"{/if}>

        {if !$quick_view && $settings.Appearance.enable_quick_view == "YesNo::YES"|enum && $settings.ab__device === "desktop"}
            {include file="views/products/components/quick_view_link.tpl" quick_nav_ids=$quick_nav_ids}
        {/if}

        {if $addons.wishlist.status == "ObjectStatuses::ACTIVE"|enum && !$hide_wishlist_button && $settings.abt__ut2.product_list.button_wish_list_view[$settings.ab__device] === "YesNo::YES"|enum && !$is_wishlist}
            {include file="addons/wishlist/views/wishlist/components/add_to_wishlist.tpl" but_id="button_wishlist_{$obj_prefix}{$product.product_id}" but_name="dispatch[wishlist.add..{$product.product_id}]" but_role="text"}

        {elseif $addons.wishlist.status == "ObjectStatuses::ACTIVE"|enum && !$hide_wishlist_button && $settings.abt__ut2.product_list.button_wish_list_view[$settings.ab__device] === "YesNo::YES"|enum && $is_wishlist}
            {include file="addons/wishlist/views/wishlist/components/remove_from_wishlist.tpl" but_id="button_wishlist_{$obj_prefix}{$product.product_id}" but_name="dispatch[wishlist.add..{$product.product_id}]" but_role="text"}
        {/if}

        {if $settings.General.enable_compare_products == "YesNo::YES"|enum && !$hide_compare_list_button && $settings.abt__ut2.product_list.button_compare_view[$settings.ab__device] === "YesNo::YES"|enum || $product.feature_comparison == "YesNo::YES"|enum && $settings.abt__ut2.product_list.button_compare_view[$settings.ab__device] === "YesNo::YES"|enum}
            {include file="buttons/add_to_compare_list.tpl" product_id=$product.product_id}
        {/if}
        <!--{$smarty.capture.abt__service_buttons_id}--></div>

        {if $show_brand_logo && $settings.abt__ut2.general.brand_feature_id > 0}
            {$b_feature=$product.abt__ut2_features[$settings.abt__ut2.general.brand_feature_id]}
            {if $b_feature.variants[$b_feature.variant_id].image_pairs}
                <div class="brand-img">
                    {include file="common/image.tpl" image_height=20 images=$b_feature.variants[$b_feature.variant_id].image_pairs no_ids=true}
                </div>
            {/if}
        {/if}
    </div>

    {capture name="product_multicolumns_list_control_data_wrapper"}
        {if $show_add_to_cart && $button_type_add_to_cart !== 'none'}

            {assign var="qty" value="qty_{$obj_id}"}

            <div class="ut2-gl__control {if $settings.abt__ut2.product_list.$tmpl.show_buttons_on_hover[$settings.ab__device] === "YesNo::YES"|enum} hidden{/if}{if $settings.abt__ut2.product_list.$tmpl.show_qty[$settings.ab__device] === "YesNo::YES"|enum && $smarty.capture.$qty|strip_tags:false|replace:"&nbsp;":""|trim|strlen} ut2-view-qty{/if}{if $button_type_add_to_cart != 'none'} {$button_type_add_to_cart}{/if}">

                {capture name="product_multicolumns_list_control_data"}
                    {hook name="products:product_multicolumns_list_control"}
                        {$add_to_cart = "add_to_cart_{$obj_id}"}
                        {$smarty.capture.$add_to_cart nofilter}

                        {if $show_qty && $smarty.capture.$qty|strip_tags:false|replace:"&nbsp;":""|trim|strlen}
                            {$smarty.capture.$qty nofilter}
                        {/if}
                    {/hook}
                {/capture}
                {$smarty.capture.product_multicolumns_list_control_data nofilter}
            </div>
        {/if}
    {/capture}

    {if $settings.abt__ut2.product_list.price_position_top|default:{"YesNo::NO"|enum} === "YesNo::YES"|enum}
        {if $button_type_add_to_cart === 'icon' || $button_type_add_to_cart === 'icon_button'}
            <div class="ut2-gl__mix-price-and-button {if $show_qty}qty-wrap{/if}">
        {/if}
        <div class="ut2-gl__price{if $product.price == 0} ut2-gl__no-price{/if}	pr-{$settings.abt__ut2.product_list.price_display_format}{if $product.list_discount || $product.discount} pr-color{/if}" style="min-height: {$smarty.capture.abt__ut2_pr_block_height  nofilter}px;">
            {hook name="products:list_price_block"}
                <div>
                    {assign var="price" value="price_{$obj_id}"}
                    {$smarty.capture.$price nofilter}

                    {if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "short"}<span>{/if}
                    {assign var="old_price" value="old_price_{$obj_id}"}
                    {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}

                    {assign var="list_discount" value="list_discount_{$obj_id}"}
                    {$smarty.capture.$list_discount nofilter}
                    {if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "short"}</span>{/if}
                </div>
                    {assign var="clean_price" value="clean_price_{$obj_id}"}
                    {$smarty.capture.$clean_price nofilter}
            {/hook}
        </div>

        {if $settings.abt__ut2.product_list.$tmpl.show_qty[$settings.ab__device] === "YesNo::NO"|enum || $settings.ab__device === "desktop"}
            {if $button_type_add_to_cart == 'icon' || $button_type_add_to_cart == 'icon_button'}
                {if $smarty.capture.product_multicolumns_list_control_data|trim}
                    {$smarty.capture.product_multicolumns_list_control_data_wrapper nofilter}
                {/if}
            {/if}
        {/if}

        {if $button_type_add_to_cart === 'icon' || $button_type_add_to_cart === 'icon_button'}
            </div>
        {/if}
    {/if}

    <div class="ut2-gl__content{if $settings.abt__ut2.product_list.$tmpl.show_content_on_hover[$settings.ab__device] === "YesNo::YES"|enum} content-on-hover{/if}">

        {include file="blocks/product_list_templates/components/average_rating.tpl"}

        <div class="ut2-gl__name">
            {if $item_number == "YesNo::YES"|enum}
                <span class="item-number">{$cur_number}.&nbsp;</span>
                {math equation="num + 1" num=$cur_number assign="cur_number"}
            {/if}

            {assign var="name" value="name_$obj_id"}
            {$smarty.capture.$name nofilter}
        </div>

        {if $product.product_code}
            {assign var="sku" value="sku_$obj_id"}
            {$smarty.capture.$sku nofilter}
        {/if}


        {if $settings.abt__ut2.product_list.$tmpl.show_amount[$settings.ab__device] === "YesNo::YES"|enum}
            <div class="ut2-gl__amount">
                {assign var="product_amount" value="product_amount_{$obj_id}"}
                {$smarty.capture.$product_amount nofilter}
            </div>
        {/if}

        {if ($show_features || $show_descr) && ($product.short_description || $product.abt__ut2_features)}
            {if empty($block.properties) && $settings.abt__ut2.product_list.$tmpl.show_content_on_hover[$settings.ab__device] === "YesNo::NO"|enum && !$products_scroller}
                <div class="ut2-gl__bottom">
                    {hook name="products:additional_info_before"}{/hook}
                    {if $product.short_description && ($settings.abt__ut2.product_list.$tmpl.grid_item_bottom_content[$settings.ab__device] === "features_and_description" || $settings.abt__ut2.product_list.$tmpl.grid_item_bottom_content[$settings.ab__device] === "description")}
                        {assign var="prod_descr" value="prod_descr_{$obj_id}"}
                        {$smarty.capture.$prod_descr nofilter}
                    {/if}

                    {hook name="products:ab__s_pictograms_pos_1"}{/hook}

                    {if $product.abt__ut2_features && !$hide_features}
                        <div class="ut2-gl__feature">
                            {assign var="product_features" value="product_features_{$obj_id}"}
                            {$smarty.capture.$product_features nofilter}
                        </div>
                    {/if}

                    {hook name="products:ab__s_pictograms_pos_2"}{/hook}
                </div>
            {/if}
        {/if}

        {if $product.short_description || $product.address}
            <div class="sd-product-info">
                {if !empty($block.properties.show_short_desc) && $block.properties.show_short_desc == "YesNo::YES"|enum && $product.short_description|trim|strip_tags}
                    <div class="ut2-gl__bottom">
                        <span class="product-description">
                            {$product.short_description|strip_tags nofilter}
                        </span>
                    </div>
                {/if}

                {if $product.address}
                    <div class="ut2-gl__bottom">
                        <span class="product-description">
                            <i class="ty-icon-location-arrow"></i>
                            {$product.address nofilter}
                        </span>
                    </div>
                {/if}
            </div>
        {/if}

        <div class="ut2-gl__price-wrap">
            {if $settings.abt__ut2.product_list.price_position_top === "YesNo::NO"|enum && ($button_type_add_to_cart === 'icon' || $button_type_add_to_cart === 'icon_button')}
                <div class="ut2-gl__mix-price-and-button {if $show_qty}qty-wrap{/if}">
            {/if}

            {if $settings.abt__ut2.product_list.price_position_top === "YesNo::NO"|enum}
            <div class="ut2-gl__price{if $product.price == 0} ut2-gl__no-price{/if}	pr-{$settings.abt__ut2.product_list.price_display_format}{if $product.list_discount || $product.discount} pr-color{/if}{if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "short"} ut2-sld-short{/if}">
                {hook name="products:list_price_block"}
                    <div>
                        {assign var="price" value="price_{$obj_id}"}
                        {$smarty.capture.$price nofilter}

                        {if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "short"}<span>{/if}
                        {assign var="old_price" value="old_price_{$obj_id}"}
                        {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}

                        {assign var="list_discount" value="list_discount_{$obj_id}"}
                        {$smarty.capture.$list_discount nofilter}
                        {if $settings.abt__ut2.product_list.show_you_save[$settings.ab__device] === "short"}</span>{/if}
                    </div>
                        {assign var="clean_price" value="clean_price_{$obj_id}"}
                        {$smarty.capture.$clean_price nofilter}
                {/hook}
            </div>
            {/if}

            {if $settings.abt__ut2.product_list.$tmpl.show_buttons_on_hover[$settings.ab__device] === "YesNo::NO"|enum
                && ($button_type_add_to_cart === 'text' || $button_type_add_to_cart === 'icon_and_text')
                ||
                $settings.abt__ut2.product_list.price_position_top === "YesNo::NO"|enum
                && ($button_type_add_to_cart === 'icon' || $button_type_add_to_cart === 'icon_button')
                ||
                $settings.abt__ut2.product_list.price_position_top === "YesNo::YES"|enum
                && $settings.abt__ut2.product_list.$tmpl.show_qty[$settings.ab__device] === "YesNo::YES"|enum
                && $settings.ab__device !== "desktop"
                }

                {if $smarty.capture.product_multicolumns_list_control_data|trim}
                    {$smarty.capture.product_multicolumns_list_control_data_wrapper nofilter}
                {/if}
            {/if}

            {if $settings.abt__ut2.product_list.price_position_top === "YesNo::NO"|enum && ($button_type_add_to_cart === 'icon' || $button_type_add_to_cart === 'icon_button')}
                </div>
            {/if}
        </div>
    </div>

    {hook name="products:ab__mv_vendor_info"}{/hook}

    {if $settings.abt__ut2.product_list.$tmpl.show_content_on_hover[$settings.ab__device] === "YesNo::YES"|enum && $settings.ab__device !== "mobile" && !$products_scroller}
        <div class="ut2-gl__bottom">

            {if $settings.abt__ut2.product_list.$tmpl.show_buttons_on_hover[$settings.ab__device] === "YesNo::YES"|enum
            && ($button_type_add_to_cart === 'text' || $button_type_add_to_cart === 'icon_and_text')}

                {if $smarty.capture.product_multicolumns_list_control_data|trim}
                    {$smarty.capture.product_multicolumns_list_control_data_wrapper nofilter}
                {/if}
            {/if}

            {hook name="products:additional_info"}{/hook}
            {hook name="products:additional_info_before"}{/hook}

            {if $product.short_description && ($settings.abt__ut2.product_list.$tmpl.grid_item_bottom_content[$settings.ab__device] === "features_and_description" || $settings.abt__ut2.product_list.$tmpl.grid_item_bottom_content[$settings.ab__device] === "description")}
                {assign var="prod_descr" value="prod_descr_{$obj_id}"}
                {$smarty.capture.$prod_descr nofilter}
            {/if}

            {hook name="products:ab__s_pictograms_pos_1"}{/hook}

            {if $show_features and $product.abt__ut2_features && !$hide_features}
                <div class="ut2-gl__feature">
                    {assign var="product_features" value="product_features_{$obj_id}"}
                    {$smarty.capture.$product_features nofilter}
                </div>
            {/if}

            {hook name="products:ab__s_pictograms_pos_2"}{/hook}
            {hook name="products:additional_info_after"}{/hook}
        </div>
    {/if}
</div>
{hook name="products:product_list_form_close_tag"}
    {assign var="form_close" value="form_close_{$obj_id}"}
    {$smarty.capture.$form_close nofilter}
{/hook}