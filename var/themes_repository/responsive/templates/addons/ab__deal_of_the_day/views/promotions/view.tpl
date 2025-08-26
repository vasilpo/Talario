{if $promotion.hide_products_block != 'Y'}
    {if $products}
    <div id="category_products_{$block.block_id}">
        <div class="ab__dotd_promotions-products" id="promotion_products">
            {assign var="layouts" value=""|fn_get_products_views:false:0}
            {if $layouts.$selected_layout.template}
                {include file="`$layouts.$selected_layout.template`" columns=$settings.Appearance.columns_in_products_list}
            {/if}
        <!--promotion_products--></div>
    <!--category_products_{$block.block_id}--></div>
    {/if}
    {if $category_groups}
    {assign var="layouts" value=""|fn_get_products_views:false:0}
    {$tmpl_extra="products_multicolumns_extra"}
    {foreach $category_groups as $category_group}
        <div class="clearfix ty-mb-s">
            <h2 class="ty-mainbox-title">{$category_group.category}</h2>
            {$products = $category_group.products}
            {if $layouts.$selected_layout.template}
                {include file="`$layouts.$selected_layout.template`" columns=$settings.Appearance.columns_in_products_list}
            {/if}
        </div>
    {/foreach}
    {/if}
{/if}