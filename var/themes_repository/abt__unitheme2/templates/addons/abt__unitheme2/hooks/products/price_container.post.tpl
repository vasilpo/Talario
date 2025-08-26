{* Product page component amount *}
{assign var="product_amount" value="product_amount_`$obj_id`"}
{$smarty.capture.$product_amount nofilter}

{* Product page component brand *}
{if $settings.abt__ut2.general.brand_feature_id}
    {include file="blocks/product_templates/components/product_brand_logo_prepare.tpl"}
    {if $brand_feature}
        {hook name="products:brand"}
        {if $settings.abt__ut2.products.view.show_brand_format[$settings.ab__device] === "logo"}
            <div class="ut2-pb__product-brand">
                {include file="views/products/components/product_features_short_list.tpl" features=array($brand_feature) feature_image=true hide_name=true feature_link=true}
            </div>
        {/if}
        {/hook}
    {/if}
{/if}

