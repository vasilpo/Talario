{* An example of how to convey that this is a block
    {include file="blocks/list_templates/grid_list.tpl"
    products=$banner.products
    block=["columns" => $banner.abt__ut2_products_grid_columns, "rows" => $banner.abt__ut2_products_grid_rows]
    }
*}
{$obj_prefix="`$banner.banner_id`_`$block.block_id`_`$block.snapping_id`_"}

{if $banner.abt__ut2_products_template === "grid_items"}
    {include file="blocks/list_templates/grid_list.tpl"
    products=$banner.products
    columns=$banner.abt__ut2_products_grid_columns
    no_sorting="YesNo::YES"|enum
    no_pagination="YesNo::YES"|enum
    no_ids="YesNo::YES"|enum
    products_scroller=true
    elements_to_scroll=1
    show_gallery=false
    show_name=true
    show_old_price=true
    show_price=true
    show_rating=true
    show_rating_num=true
    show_clean_price=true
    show_list_discount=$settings.abt__ut2.product_list.show_you_save[$settings.ab__device] !== "none"
    hide_qty_label=true
    show_sku_label=true
    show_product_labels=true
    show_discount_label=true
    show_shipping_label=true
    show_product_amount=$settings.abt__ut2.product_list.products_multicolumns.show_amount[$settings.ab__device]|default:{"YesNo::NO"|enum} === "YesNo::YES"|enum
    show_amount_label=false
    show_sku=$settings.abt__ut2.product_list.products_multicolumns.show_sku[$settings.ab__device]|default:{"YesNo::NO"|enum} === "YesNo::YES"|enum
    show_qty=$settings.abt__ut2.product_list.products_multicolumns.show_qty[$settings.ab__device]|default:{"YesNo::NO"|enum} === "YesNo::YES"|enum
    show_features=$settings.abt__ut2.product_list.products_multicolumns.grid_item_bottom_content[$settings.ab__device] === "features" || $settings.abt__ut2.product_list.products_multicolumns.grid_item_bottom_content[$settings.ab__device] === "features_and_description" || $settings.abt__ut2.product_list.products_multicolumns.grid_item_bottom_content[$settings.ab__device] === "features_and_variations"
    show_descr=$settings.abt__ut2.product_list.products_multicolumns.grid_item_bottom_content[$settings.ab__device] === "description" || $settings.abt__ut2.product_list.products_multicolumns.grid_item_bottom_content[$settings.ab__device] === "features_and_description" || $settings.abt__ut2.product_list.products_multicolumns.grid_item_bottom_content[$settings.ab__device] === "features_and_variations"
    show_brand_name=$settings.abt__ut2.product_list.$tmpl.show_brand[$settings.ab__device] === "name"
    show_brand_logo=$settings.abt__ut2.product_list.$tmpl.show_brand[$settings.ab__device] === "logo"
    show_list_buttons=false
    show_add_to_cart=true
    but_role="action"
    }
{elseif $banner.abt__ut2_products_template === "small_items"}

    {include file="blocks/list_templates/small_items.tpl"
    products=$banner.products
    columns=$banner.abt__ut2_products_small_items_columns
    rows=$banner.abt__ut2_products_small_items_rows
    products_scroller=true
    elements_to_scroll=1
    show_name=true
    show_product_amount=$settings.abt__ut2.product_list.small_items.show_amount[$settings.ab__device]|default:{"YesNo::NO"|enum} === "YesNo::YES"|enum
    show_amount_label=false
    show_discount_label=false
    show_add_to_cart=true
    }
{elseif $banner.abt__ut2_products_template === "links_thumb"}

    {include file="blocks/list_templates/links_thumb.tpl"
    products=$banner.products
    columns=$banner.abt__ut2_products_links_thumb_columns
    rows=$banner.abt__ut2_products_links_thumb_rows
    hide_links=true
    products_scroller=true
    elements_to_scroll=1
    show_price=true
    show_old_price=true
    show_clean_price=false
    show_list_discount=false
    show_name=false
    show_trunc_name=true
    show_product_labels=false
    show_discount_label=false
    show_add_to_cart=false
    }
{/if}
