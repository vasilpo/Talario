<div class="ty-exception ty-exception--lr-coming-soon">
    <div class="ty-exception__title-info">
        <p class="ty-exception__info">
            {__("lr_design_changes.exception_404_eyebrow")}
        </p>

        <h1 class="ty-exception__title"><strong>{__("lr_design_changes.exception_404_title")}<strong></h1>

        {include file="design/themes/abt__unitheme2/templates/addons/exikane_changes/components/guest_banner.tpl"
            banner_title=__("exikane_changes.guest_banner_title")
            banner_text=__("exikane_changes.guest_banner_text")
        }
    </div>
</div>

{if $lr_design_changes_recommended_products}
    <div class="ty-exception">
        <div class="ty-exception__title-info">
            <h2 class="ty-exception__title">{__("lr_design_changes.search_recommended_title")}</h2>
        </div>

        {include file="blocks/list_templates/grid_list.tpl"
            products=$lr_design_changes_recommended_products
            columns=3
            no_pagination=true
            no_sorting=true
            no_ids=true
            obj_prefix="lr_design_changes_recommended_"
            show_name=true
            show_old_price=true
            show_price=true
            show_rating=true
            show_clean_price=true
            show_list_discount=true
            show_add_to_cart=true
            but_role="action"
            show_features=false
            show_product_labels=true
            show_discount_label=true
            show_shipping_label=true
        }
    </div>
{/if}
