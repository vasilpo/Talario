{if !$product.company_id && $show_view_offers_btn && !$is_allow_add_common_products_to_cart_list && (!$details_page || $quick_view)}{strip}
    {$add_to_cart_type = $add_to_cart_type|default:"text"}
    {$is_link_to_offers = $is_link_to_offers|default:false}

    {if $add_to_cart_type === "icon"}
        {$see_all_offers_but_text = " "}
        {$see_all_offers_but_icon = "ty-icon-cart ty-icon--no-margin"}
    {else}
        {$see_all_offers_but_text = __("master_products.view_product_offers")}
        {$see_all_offers_but_icon = ""}
    {/if}

    {if $is_link_to_offers}
        {* selected_section attribute for SEO add-on fix *}
        {$see_all_offers_but_href = "products.view&product_id=`$product.product_id`&selected_section=vendor_products#tabs_content"}
    {else}
        {$see_all_offers_but_href = "products.view?product_id=`$product.product_id`"}
    {/if}

    {/strip}{capture name="add_to_cart_`$obj_id`"}
        <a href="{$see_all_offers_but_href|fn_url}" class="cm-reload-{$obj_prefix}{$obj_id} ty-product-offers-btn ty-btn__offers ty-btn__primary ty-btn__big ty-btn__add-to-cart ty-btn" id="view_product_offers_btn_{$obj_prefix}{$obj_id}">
            {include_ext file="common/icon.tpl" class=$see_all_offers_but_icon}
            {$see_all_offers_but_text}
        <!--view_product_offers_btn_{$obj_prefix}{$obj_id}--></a>

        {if $show_list_buttons}
            {$compare_product_id = $product.product_id}

            {if $settings.General.enable_compare_products === "YesNo::YES"|enum}
                {include file="buttons/add_to_compare_list.tpl" product_id=$compare_product_id}
            {/if}
        {/if}
    {/capture}
    {if $no_capture}
        {assign var="capture_name" value="add_to_cart_`$obj_id`"}
        {$smarty.capture.$capture_name nofilter}
    {/if}
{/if}