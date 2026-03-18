{strip}
{script src="js/tygh/exceptions.js"}
{$obj_id = $product.product_id}
{$obj_prefix = "svw"}
{$quick_view = false}
{$ut2_select_variation = true}

{if $redirect_url}
    {$r_url = $redirect_url}
{else}
    {$r_url = false}
{/if}

{if $settings.ab__device === "mobile"}
    <div class="ut2_select_variation__title">{__("select_options")}<div class="ut2-btn-close"><i class="ut2-icon-baseline-close"></i></div></div>
{/if}
<div id="ut2_select_variation_wrapper_{$obj_prefix}">
    {include file="common/product_data.tpl"
    product=$product
    show_add_to_cart=true
    show_product_options=true
    show_name=true
    show_price_values=true
    show_old_price=true
    show_price=true
    show_clean_price=false
    show_list_discount=false
    hide_qty_label=true
    redirect_url=$r_url
    show_product_amount=true
    show_amount_label=false}

    {assign var="form_open" value="form_open_`$obj_id`"}
    {$smarty.capture.$form_open nofilter}
        {hook name="ut2_select_variation"}
            <div class="ut2_select_variation__product">
                {include file="views/products/components/product_icon.tpl"
                    product=$product
                    show_gallery=false
                    image_width="80"
                    image_height="80"
                }

                <div class="ut2_select_variation__product-title list">
                    {assign var="name" value="name_$obj_id"}
                    {$smarty.capture.$name nofilter}

                    {include file="blocks/product_list_templates/components/average_rating.tpl" meta="" show_label_in_title="true"}

                    {$product_amount="product_amount_`$obj_id`"}
                    {$smarty.capture.$product_amount nofilter}
                </div>
            </div>
            
            {hook name="ut2_select_variation_form"}
                <div class="ut2_select_variation__option">
                    {assign var="product_options" value="product_options_`$obj_id`"}
                    {$smarty.capture.$product_options nofilter}
                </div>

                <a href="{"products.view?product_id=`$obj_id`"|fn_url}" title="" class="ut2_extra-link"><span>{__("abt__ut2_extra_link_full_product_information")}</span><i class="ut2-icon-outline-arrow_forward"></i></a>

                <div class="buttons-container ut2_select_variation__buttons">

                    <div class="ut2_select_variation__price	pr-{$settings.abt__ut2.product_list.price_display_format}{if $product.list_discount || $product.discount} pr-color{/if}">
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

                    {if $settings.abt__ut2.products.view.show_qty[$settings.ab__device]  === "YesNo::YES"|enum}
                    <div class="ty-inline-block">
                        {assign var="qty" value="qty_`$obj_id`"}
                        {$smarty.capture.$qty nofilter}
                    </div>
                    {/if}

                    {assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
                    {$smarty.capture.$add_to_cart nofilter}
                </div>
            {/hook}
        {/hook}
    {assign var="form_close" value="form_close_`$obj_id`"}
    {$smarty.capture.$form_close nofilter}
<!--ut2_select_variation_wrapper_{$obj_prefix}--></div>
{/strip}