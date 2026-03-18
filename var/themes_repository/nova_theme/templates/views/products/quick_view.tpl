{*
    Table of contents
    ---
    #5. Quick view wrapper (only quick view wrapper)
    #10. Product details outer
    #20. Product details inner
    #25. Previous and next page buttons
    #35. Deprecated: Product form wrapper (only quick view wrapper)
    #45. Product form inner (only quick view wrapper)

    #100. Product image

    #200. Product info
    #210. Product info columns wrapper

    #300. Product info. Column 1
    #310. Product title
    #320. Product brand and features short list

        #420. Product price container
        #430. Product price
        #440. Product list price (old_price)
        #450. Product price without taxes (clean_price) and You save (list_discount)

    #330. Product options
    #340. Advanced product info and vendor info
    #350. Product CODE
    #360. Product electronically distributed

    #380. Product short description

        #460. Product promo text
        #470. Product availability and quantity
        #480. Product availability
        #490. Product quantity
        #500. Product minimum quantity
        #510. Product buttons
        #520. Product buttons: Add to cart and addons
        #530. View full product details
*}

{*
    See also: default_template.tpl
*}

{capture name="val_hide_form"}{/capture}
{capture name="val_no_ajax"}{/capture}

{* Product data *}
{$quick_view = "true"}
{$hide_form = $smarty.capture.val_hide_form}
{$show_product_labels = $show_product_labels|default:true}
{$show_discount_label = $show_discount_label|default:true}
{$show_shipping_label = $show_shipping_label|default:true}
{$show_rating = $show_rating|default:true}
{$show_product_common_info = $show_product_common_info|default:true}
{$show_old_price = $show_old_price|default:true}
{$show_old_price_label = $show_old_price_label|default:false}
{$show_price = $show_price|default:true}
{$show_list_discount = $show_list_discount|default:true}
{$show_clean_price = $show_clean_price|default:true}
{$show_product_options = $show_product_options|default:true}
{$show_product_amount = $show_product_amount|default:true}
{$show_sku = $show_sku|default:true}
{$show_amount_label = $show_amount_label|default:false}
{$show_in_stock_label = $show_in_stock_label|default:false}
{$hide_qty_label = $hide_qty_label|default:true}
{$min_qty = $min_qty|default:true}
{$show_edp = $show_edp|default:true}
{$show_descr = $show_descr|default:false}
{$show_add_to_cart = $show_add_to_cart|default:true}
{$add_to_cart_type = $add_to_cart_type|default:"text"}
{$add_to_cart_class = $add_to_cart_class|default:"ty-products-buttons-block"}
{$show_list_buttons = $show_list_buttons|default:true}
{$block_width = $block_width|default:true}
{$separate_buttons = $separate_buttons|default:true}
{$show_product_company_data = $show_product_company_data|default:true}
{* /Product data *}

{* Quick view *}
{$obj_prefix = $obj_prefix|default:"ajax"}
{$show_view_tools = $show_view_tools|default:false}
{$show_simple_product_images = $show_simple_product_images|default:false}
{$thumbnail_width = $thumbnail_width|default:$settings.Thumbnails.product_quick_view_thumbnail_width}
{$thumbnail_height = $thumbnail_height|default:$settings.Thumbnails.product_quick_view_thumbnail_height}
{$product_img_wrapper_style = $product_img_wrapper_style|default:[
    "--ty-product-img-width"    => "`$thumbnail_width|default:$thumbnail_height`px",
    "--ty-product-img-height"   => "`$thumbnail_height|default:$thumbnail_width`px",
    "width"                     => "var(--ty-product-img-width)",
    "height"                    => "var(--ty-product-img-height)"
]}
{$thumbnails_size = $thumbnails_size|default:68}
{$show_product_features_short_list = $show_product_features_short_list|default:true}
{$enable_product_block_title_link = $enable_product_block_title_link|default:true}
{$show_promo_text = $show_promo_text|default:true}
{$show_details_button = $show_details_button|default:true}
{* /Quick view *}

{if $show_quick_view_for_options}
    {* Product data *}
    {$show_product_labels = false}
    {$show_old_price = false}
    {$show_list_discount = false}
    {$show_descr = false}
    {$show_product_company_data = false}
    {$show_add_to_compare_list = false}
    {* /Product data *}

    {* Quick view *}
    {$obj_prefix = "for_options_`$obj_prefix`"}
    {$quick_view_wrapper_class = "`$quick_view_wrapper_class` ty-quick-view__wrapper--for-options"}
    {$quick_view_form_inner_class = "`$quick_view_form_inner_class` ty-product-block__form-inner--for-options"}
    {$show_view_tools = false}
    {$show_simple_product_images = true}
    {$thumbnail_width = 120}
    {$thumbnail_height = 120}
    {$show_product_features_short_list = false}
    {$enable_product_block_title_link = false}
    {$show_promo_text = false}
    {$show_details_button = false}
    {* /Quick view *}
{/if}

{* #5. Quick view wrapper (only quick view wrapper) *}
<div class="ty-quick-view__wrapper {$quick_view_wrapper_class}">
    {script src="js/tygh/exceptions.js"}

    {* #10. Product details outer *}
    <div class="ty-product-block" id="product_main_info_{$obj_prefix}">

        {* #20. Product details inner *}
        <div class="ty-product-block__wrapper ty-product-block__wrapper--quick-view">

            {hook name="products:quick_view_view_main_info"}

                {hook name="products:view_main_info"}

                    {if $product}
                        {* #25. Previous and next page buttons *}
                        <div class="ty-quick-view-tools"{if !$show_view_tools}style="opacity: 0; pointer-events: none;"{/if}>
                            {include file="common/view_tools.tpl"
                                quick_view=true
                            }
                        </div>
                        {* /Previous and next page buttons *}

                        {$obj_id = $product.product_id}

                        {* Get product data *}
                        {include file="common/product_data.tpl"
                            obj_prefix=$obj_prefix
                            obj_id=$obj_id
                            product=$product
                            but_role="big"
                            but_text=__("add_to_cart")
                            add_to_cart_meta="cm-form-dialog-closer"
                            add_to_cart_type=$add_to_cart_type|default:"text"
                            show_sku=$show_sku
                            show_rating=$show_rating
                            show_old_price=$show_old_price
                            show_price=$show_price
                            show_list_discount=$show_list_discount
                            show_clean_price=$show_clean_price
                            details_page=true
                            show_product_labels=$show_product_labels
                            show_discount_label=$show_discount_label
                            show_shipping_label=$show_shipping_label
                            show_product_amount=$show_product_amount
                            show_product_options=$show_product_options
                            hide_form=$hide_form
                            min_qty=$min_qty
                            show_edp=$show_edp
                            show_add_to_cart=$show_add_to_cart
                            show_list_buttons=$show_list_buttons
                            separate_buttons=$separate_buttons
                            block_width=$block_width
                            no_ajax=$smarty.capture.val_no_ajax
                            show_descr=$show_descr
                            quick_view=true
                        }

                        {* #35. Deprecated: Product form wrapper (only quick view wrapper) *}
                        <div id="product_main_info_form_{$obj_prefix}">

                            {* #45. Product form inner (only quick view wrapper) *}
                            <div class="ty-product-block__form-inner {$quick_view_form_inner_class}">

                                {* #100. Product image *}
                                <div
                                    class="ty-product-block__img-wrapper ty-product-block__img-wrapper--quick-view"
                                >
                                    {hook name="products:quick_view_image_wrap"}
                                        {if !$no_images}
                                            <div
                                                class="ty-product-block__img cm-reload-{$obj_prefix}{$obj_id}"
                                                data-ca-previewer="true"
                                                id="product_images_{$obj_prefix}{$obj_id}_update"
                                            >
                                                {$product_labels = "product_labels_`$obj_prefix``$obj_id`"}
                                                {* (Hooks) products:product_labels *}
                                                {*
                                                    Addons:
                                                    - Save up to (master_products)
                                                *}
                                                {$smarty.capture.$product_labels nofilter}

                                                {if $show_simple_product_images}
                                                    <div class="ty-product-img ty-product-img--simple">
                                                        {include file="common/image.tpl"
                                                            image_width=$thumbnail_width
                                                            image_height=$thumbnail_height
                                                            images=$product.main_pair
                                                            no_ids=true
                                                        }
                                                    </div>
                                                {else}
                                                    {include file="views/products/components/product_images.tpl"
                                                        product=$product
                                                        image_pair_var=$image_pair_var
                                                        preview_id=$preview_id
                                                        image_id=$image_id
                                                        image_width=$thumbnail_width
                                                        image_height=$thumbnail_height
                                                        product_img_wrapper_style=$product_img_wrapper_style
                                                        image_pair=$image_pair
                                                        settings=$settings
                                                        thumbnails_size=$thumbnails_size
                                                        show_detailed_link=true
                                                    }
                                                {/if}
                                            <!--product_images_{$obj_prefix}{$obj_id}_update--></div>
                                        {/if}
                                    {/hook}
                                </div>
                                {* /Product image *}

                                {* #200. Product info *}
                                {* ty-product-block__left - right block (backward compatibility) *}
                                <div class="ty-product-block__left">

                                    {$form_open = "form_open_`$obj_id`"}
                                    {$smarty.capture.$form_open nofilter}

                                        {* #210. Product info columns wrapper *}
                                        <div class="ty-product-block__columns-wrapper ty-product-block__columns-wrapper--quick-view">

                                            {* #300. Product info. Column 1 *}
                                            <div class="ty-product-block__col-1">
                                                {capture name="product_detail_view_url"}
                                                    {$product_detail_view_url = "products.view?product_id=`$product.product_id`"}
                                                    {hook name="products:product_detail_view_url"}
                                                        {$product_detail_view_url}
                                                    {/hook}
                                                {/capture}

                                                {$product_detail_view_url = $smarty.capture.product_detail_view_url|trim}

                                                {*
                                                    Addons:
                                                    - Product stars, reviews, and write a review (product_reviews)
                                                    - Product stars, reviews, and write a review (discussion)
                                                    - etc...
                                                *}
                                                {hook name="products:quick_view_title"}
                                                    {* #310. Product title *}
                                                    {if !$hide_title}
                                                        {if $enable_product_block_title_link}
                                                            <h1 class="ty-product-block-title">
                                                                <a
                                                                    href="{$product_detail_view_url|fn_url}"
                                                                    class="ty-quick-view__title"
                                                                    {live_edit name="product:product:{$product.product_id}"}
                                                                >
                                                                    <bdi>{$product.product nofilter}</bdi>
                                                                </a>
                                                            </h1>
                                                        {else}
                                                            <h1 class="ty-product-block-title ty-product-block-title--quick-view" {live_edit name="product:product:{$product.product_id}"}>
                                                                <bdi>{$product.product nofilter}</bdi>
                                                            </h1>
                                                        {/if}
                                                    {/if}
                                                    {* /Product title *}
                                                {/hook}

                                                {* #320. Product brand and features short list *}
                                                {hook name="products:brand"}
                                                    {if $show_product_features_short_list}
                                                        <div class="ty-brand">
                                                            {include file="views/products/components/product_features_short_list.tpl"
                                                                features=$product.header_features
                                                                feature_image=$feature_image
                                                                product=$product
                                                                image_size=$image_size
                                                                no_container=$no_container
                                                                settings=$settings
                                                                smarty=$smarty
                                                            }
                                                        </div>
                                                    {/if}
                                                {/hook}
                                                {* /Product brand and features short list *}



                                                {* #420. Product price container *}
                                                {$old_price = "old_price_`$obj_id`"}
                                                {$price = "price_`$obj_id`"}
                                                {$clean_price = "clean_price_`$obj_id`"}
                                                {$list_discount = "list_discount_`$obj_id`"}
                                                <div
                                                    class="price-wrap
                                                        {if $smarty.capture.$old_price|trim
                                                            || $smarty.capture.$clean_price|trim
                                                            || $smarty.capture.$list_discount|trim
                                                        }
                                                            prices-container
                                                        {/if}"
                                                >
                                                    {if $smarty.capture.$old_price|trim
                                                        || $smarty.capture.$clean_price|trim
                                                        || $smarty.capture.$list_discount|trim
                                                    }
                                                        <div class="ty-product-prices">
                                                    {/if}

                                                    {* #430. Product price *}
                                                    {*
                                                        Addons:
                                                        - Price per unit (price_per_unit)
                                                        - etc...
                                                    *}
                                                    {hook name="products:main_price"}
                                                        {if $smarty.capture.$price|trim}
                                                            <div class="ty-product-block__price-actual">
                                                                {* (Hooks) products:prices_block *}
                                                                {*
                                                                    Addons:
                                                                    - Divido calculator (divido)
                                                                    - And other offers (master_products)
                                                                    - etc...
                                                                *}
                                                                {$smarty.capture.$price nofilter}
                                                            </div>
                                                        {/if}
                                                    {/hook}
                                                    {* /Product price *}

                                                    {* #440. Product list price (old_price) *}
                                                    {if $smarty.capture.$old_price|trim
                                                        || $smarty.capture.$clean_price|trim
                                                        || $smarty.capture.$list_discount|trim
                                                    }
                                                        {* (Hooks) products:old_price *}
                                                        {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}
                                                    {/if}
                                                    {* /Product list price (old_price) *}

                                                    {* #450. Product price without taxes (clean_price) and You save (list_discount) *}
                                                    {if $smarty.capture.$old_price|trim
                                                        || $smarty.capture.$clean_price|trim
                                                        || $smarty.capture.$list_discount|trim
                                                    }
                                                            {$smarty.capture.$clean_price nofilter}
                                                            {$smarty.capture.$list_discount nofilter}
                                                        </div>
                                                    {/if}
                                                    {* /Product price without taxes (clean_price) and You save (list_discount) *}
                                                </div>
                                                {* /Product price container *}



                                                {* #330. Product options *}
                                                <div class="ty-product-block__option">
                                                    {$product_options = "product_options_`$obj_id`"}
                                                    {* (Hooks) products:product_option_content *}
                                                    {*
                                                        Addons:
                                                        - Product variation features as options (product_variations)
                                                        - etc...
                                                    *}
                                                    {$smarty.capture.$product_options nofilter}
                                                </div>
                                                {* /Product options *}

                                                {* #340. Advanced product info and vendor info *}
                                                <div class="ty-product-block__advanced-option clearfix">
                                                    {$advanced_options = "advanced_options_`$obj_id`"}
                                                    {* (Hooks) products:options_advanced *}
                                                    {* Addons:
                                                        - Return period (rma)
                                                        - Supplier (suppliers)
                                                        - Already bought (required_products)
                                                        - Ask a question (vendor_communication)
                                                        - etc...
                                                    *}
                                                    {$smarty.capture.$advanced_options nofilter}
                                                </div>
                                                {* /Advanced product info and vendor info *}

                                                {* #350. Product CODE *}
                                                <div class="ty-product-block__sku">
                                                    {$sku = "sku_`$obj_id`"}
                                                    {$smarty.capture.$sku nofilter}
                                                </div>
                                                {* /Product CODE *}

                                                {* #360. Product electronically distributed *}
                                                {$product_edp = "product_edp_`$obj_id`"}
                                                {$smarty.capture.$product_edp nofilter}
                                                {* /Product electronically distributed *}

                                                {* #380. Product short description *}
                                                {$prod_descr = "prod_descr_`$obj_id`"}
                                                {if $show_descr && $smarty.capture.$prod_descr|strip_tags|trim}
                                                    <div class="ty-product-block__description ty-product-block__description--quick-view">
                                                        <div class="ty-product-block__description-content ty-product-block__description-content--quick-view">
                                                            {$smarty.capture.$prod_descr nofilter}
                                                        </div>
                                                    </div>
                                                {/if}
                                                {* /Product short description *}



                                                {* #460. Product promo text *}
                                                {hook name="products:promo_text"}
                                                    {if $show_promo_text && $product.promo_text}
                                                        <div class="ty-product-block__note-wrapper">
                                                            <div class="ty-product-block__note ty-product-block__note-inner">
                                                                {$product.promo_text nofilter}
                                                            </div>
                                                        </div>
                                                    {/if}
                                                {/hook}
                                                {* /Product promo text *}

                                                {* #470. Product availability and quantity *}
                                                <div class="ty-product-block__field-group">
                                                    {* #480. Product availability *}
                                                    {$product_amount = "product_amount_`$obj_id`"}
                                                    {* (Hooks) products:product_amount *}
                                                    {* Addons:
                                                        - Price in points, Reward points (reward_points)
                                                        - etc...
                                                    *}
                                                    {$smarty.capture.$product_amount nofilter}
                                                    {* /Product availability *}

                                                    {* #490. Product quantity *}
                                                    {$qty = "qty_`$obj_id`"}
                                                    {* (Hooks) products:qty *}
                                                    {$smarty.capture.$qty nofilter}
                                                    {* /Product quantity *}

                                                    {* #500. Product minimum quantity *}
                                                    {$min_qty = "min_qty_`$obj_id`"}
                                                    {* (Hooks) products:qty_description *}
                                                    {$smarty.capture.$min_qty nofilter}
                                                    {* /Product minimum quantity *}
                                                </div>
                                                {* /Product availability and quantity *}

                                                {* #510. Product buttons *}
                                                <div class="ty-product-block__button">
                                                    {* #520. Product buttons: Add to cart and addons *}
                                                    {$add_to_cart = "add_to_cart_`$obj_id`"}
                                                    {*
                                                        (Hooks)
                                                        products:add_to_cart
                                                        products:add_to_cart_but_id
                                                        products:buttons_block
                                                        products:out_of_stock_block
                                                        products:buy_now
                                                    *}
                                                    {*
                                                        Addons:
                                                        - Buy now with 1-click (call_requests)
                                                        - etc
                                                    *}
                                                    {$smarty.capture.$add_to_cart nofilter}
                                                    {* /Product buttons: Add to cart and addons *}

                                                    {* #530. View full product details *}
                                                    {if $show_details_button}
                                                        {include file="buttons/button.tpl"
                                                            but_href="products.view?product_id=`$product.product_id`"
                                                            but_text=__("view_details")
                                                            but_role="submit"
                                                            but_meta="ty-product-block__view-details"
                                                        }
                                                    {/if}
                                                    {* /View full product details *}
                                                </div>
                                                {* /Product buttons *}

                                            </div>
                                            {* /Product info. Column 2 *}

                                        </div>
                                        {* /Product info columns wrapper *}

                                        {if $obj_prefix === "ajax"}
                                            <input
                                                type="hidden"
                                                class="cm-no-hide-input"
                                                name="security_hash"
                                                value="{""|fn_generate_security_hash}"
                                            >
                                        {/if}

                                    {$form_close = "form_close_`$obj_id`"}
                                    {$smarty.capture.$form_close nofilter}

                                </div>
                                {* /Product info *}
                            </div>
                            {* /Product form inner (only quick view wrapper) *}

                        <!--product_main_info_form_{$obj_prefix}--></div>
                        {* /Deprecated: Product form wrapper (only quick view wrapper) *}
                    {/if}

                {/hook}

            {/hook}

        </div>
        {* /Product details inner *}

        {if $smarty.capture.hide_form_changed == "Y"}
            {$hide_form = $smarty.capture.orig_val_hide_form}
        {/if}

    <!--product_main_info_{$obj_prefix}--></div>
    {* /Product details outer *}

</div>
{* /Quick view wrapper (only quick view wrapper) *}