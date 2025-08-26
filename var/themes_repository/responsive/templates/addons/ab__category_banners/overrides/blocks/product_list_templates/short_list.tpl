{** template-description:compact_list **}
{capture name="short_list_html"}
    {include file="blocks/list_templates/compact_list.tpl"
        show_name=true
        show_sku=true
        show_price=true
        show_old_price=true
        show_clean_price=true
        show_add_to_cart=$show_add_to_cart|default:true
        but_role="action"
        hide_form=true
        hide_qty_label=true
        show_product_labels=false
        show_discount_label=false
        show_shipping_label=false
    }
{/capture}

{if $ab__cb_banner_exists}
    {capture name="short_list_html"}
        {$smarty.capture.short_list_html|fn_ab__cb_insert_category_banner:'short_list' nofilter}
    {/capture}
{/if}

{$smarty.capture.short_list_html nofilter}